const fs = require('fs-extra');
const path = require('path');
const cheerio = require('cheerio');

function sqlEscape(value) {
  if (value === null || value === undefined) {
    return 'NULL';
  }

  if (typeof value === 'number') {
    return value.toString();
  }

  if (typeof value === 'boolean') {
    return value ? '1' : '0';
  }

  value = value.toString();

  value = value.replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/"/g, '\\"')
    .replace(/\n/g, '\\n')
    .replace(/\r/g, '\\r')
    .replace(/\x00/g, '\\0')
    .replace(/\x1a/g, '\\Z');

  return `'${value}'`;
}

const CONFIG = {
  projectRoot: path.join(__dirname, '..'),
  htmlFiles: [],
  outputDir: path.join(__dirname, '../extraction-output'),
  imagesDir: path.join(__dirname, '../extraction-output/extracted-images'),
  logFile: path.join(__dirname, '../extraction-output/extraction-log.txt'),
  sqlFile: path.join(__dirname, '../extraction-output/premierplug-content-import.sql'),
  imageMappingFile: path.join(__dirname, '../extraction-output/image-mapping.csv'),
  reportFile: path.join(__dirname, '../extraction-output/content-extraction-report.md'),
  categoryFile: path.join(__dirname, '../extraction-output/category-structure.json'),
};

let logMessages = [];
let extractedPosts = [];
let imageMap = [];
let errorCount = 0;
let warningCount = 0;

function log(message, type = 'info') {
  const timestamp = new Date().toISOString();
  const logMessage = `[${timestamp}] [${type.toUpperCase()}] ${message}`;
  logMessages.push(logMessage);
  console.log(logMessage);

  if (type === 'error') errorCount++;
  if (type === 'warning') warningCount++;
}

function sanitizeSlug(text) {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .substring(0, 200);
}

function sanitizeTitle(text) {
  return text
    .replace(/\s+/g, ' ')
    .trim()
    .substring(0, 255);
}

function extractMetaDescription($) {
  const metaDesc = $('meta[name="description"]').attr('content');
  return metaDesc || '';
}

function extractTitle($, htmlFilename) {
  let title = $('title').text().trim();

  if (!title || title === 'PremierPlug.Org - Modern Media Agency') {
    const h1Content = $('#content-area h1').first();
    if (h1Content.length > 0) {
      title = h1Content.text().trim();
    } else {
      title = htmlFilename.replace('.html', '').replace(/-|_/g, ' ');
      title = title.split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
      ).join(' ');
    }
  }

  title = title.replace(/PremierPlug/gi, '').replace(/Asset 1/gi, '').trim();

  return sanitizeTitle(title);
}

function extractImages($, htmlFilename) {
  const images = [];

  $('img').each((index, element) => {
    const src = $(element).attr('src') || $(element).attr('data-desktop');
    const alt = $(element).attr('alt') || '';
    const role = $(element).attr('role');

    if (src && !src.startsWith('http') && !src.startsWith('data:')) {
      images.push({
        originalPath: src,
        alt: alt,
        role: role,
        sourcePage: htmlFilename
      });
    }
  });

  return images;
}

function extractContent($, htmlFilename) {
  $('header.site-header').remove();
  $('footer').remove();
  $('.nav-overlay').remove();
  $('script').remove();
  $('style').remove();
  $('.loading').remove();
  $('.animation-intro').remove();

  const contentArea = $('#content-area');
  let content = '';

  if (contentArea.length > 0) {
    const sections = contentArea.find('section');

    sections.each((index, section) => {
      const $section = $(section);

      const headings = $section.find('h1, h2, h3, h4, h5, h6');
      headings.each((i, heading) => {
        const text = $(heading).text().trim();
        if (text && text.length > 2) {
          const level = heading.tagName.toLowerCase();
          content += `<${level}>${text}</${level}>\n\n`;
        }
      });

      const paragraphs = $section.find('p');
      paragraphs.each((i, para) => {
        const text = $(para).text().trim();
        if (text && text.length > 10) {
          content += `<p>${text}</p>\n\n`;
        }
      });

      const lists = $section.find('ul, ol');
      lists.each((i, list) => {
        const $list = $(list);
        const isOrdered = list.tagName.toLowerCase() === 'ol';
        const tag = isOrdered ? 'ol' : 'ul';

        content += `<${tag}>\n`;
        $list.find('li').each((j, li) => {
          const text = $(li).text().trim();
          if (text) {
            content += `  <li>${text}</li>\n`;
          }
        });
        content += `</${tag}>\n\n`;
      });
    });
  }

  if (!content || content.length < 100) {
    log(`Warning: Very little content extracted from ${htmlFilename}`, 'warning');
    content = `<p>This page is under construction. Please check back soon for updates.</p>`;
  }

  return content.trim();
}

function categorizeContent(htmlFilename, title) {
  const filename = htmlFilename.toLowerCase();

  const researchFiles = ['social-research', 'market-research', 'data-analysis'];
  const talentsFiles = ['motion-pictures', 'digital-media', 'speakers', 'television', 'voiceovers'];
  const enterpriseFiles = [
    'music-brand-partnerships', 'publishing', 'brand-consulting',
    'brandmanagement', 'brand-studio', 'marketing-it'
  ];
  const informationFiles = [
    'about-us', 'careers', 'contact', 'privacy-policy',
    'terms-of-use', 'client-privacy-notice', 'human-rights',
    'social-responsibility', 'internships', 'entry-level-opportunities'
  ];

  if (filename === 'index.html') {
    return { category: 'Home', postType: 'page', parentCategory: null };
  }

  if (researchFiles.some(keyword => filename.includes(keyword))) {
    return { category: 'Research Services', postType: 'post', parentCategory: 'Research' };
  }

  if (talentsFiles.some(keyword => filename.includes(keyword))) {
    return { category: 'Talent Services', postType: 'post', parentCategory: 'For Talents' };
  }

  if (enterpriseFiles.some(keyword => filename.includes(keyword))) {
    return { category: 'Enterprise Solutions', postType: 'post', parentCategory: 'For Enterprise' };
  }

  if (informationFiles.some(keyword => filename.includes(keyword))) {
    return { category: 'Company Information', postType: 'page', parentCategory: null };
  }

  return { category: 'Uncategorized', postType: 'post', parentCategory: null };
}

async function copyImages(images) {
  await fs.ensureDir(CONFIG.imagesDir);

  for (const img of images) {
    const sourcePath = path.join(CONFIG.projectRoot, img.originalPath);

    try {
      if (await fs.pathExists(sourcePath)) {
        const filename = path.basename(img.originalPath);
        const destPath = path.join(CONFIG.imagesDir, filename);

        await fs.copy(sourcePath, destPath);

        imageMap.push({
          originalPath: img.originalPath,
          newPath: `extracted-images/${filename}`,
          alt: img.alt,
          sourcePage: img.sourcePage,
          exists: true
        });

        log(`Copied image: ${filename}`);
      } else {
        log(`Warning: Image not found: ${img.originalPath}`, 'warning');

        imageMap.push({
          originalPath: img.originalPath,
          newPath: 'MISSING',
          alt: img.alt,
          sourcePage: img.sourcePage,
          exists: false
        });
      }
    } catch (error) {
      log(`Error copying image ${img.originalPath}: ${error.message}`, 'error');

      imageMap.push({
        originalPath: img.originalPath,
        newPath: 'ERROR',
        alt: img.alt,
        sourcePage: img.sourcePage,
        exists: false,
        error: error.message
      });
    }
  }
}

async function parseHtmlFile(htmlFile) {
  const htmlPath = path.join(CONFIG.projectRoot, htmlFile);

  try {
    log(`Parsing: ${htmlFile}`);

    const html = await fs.readFile(htmlPath, 'utf-8');
    const $ = cheerio.load(html);

    const title = extractTitle($, htmlFile);
    const slug = sanitizeSlug(htmlFile.replace('.html', ''));
    const metaDescription = extractMetaDescription($);
    const content = extractContent($, htmlFile);
    const images = extractImages($, htmlFile);
    const categoryInfo = categorizeContent(htmlFile, title);

    if (images.length > 0) {
      await copyImages(images);
    }

    const post = {
      filename: htmlFile,
      title: title,
      slug: slug,
      content: content,
      excerpt: metaDescription.substring(0, 255),
      postType: categoryInfo.postType,
      category: categoryInfo.category,
      parentCategory: categoryInfo.parentCategory,
      featuredImage: images.length > 0 ? images[0].originalPath : null,
      images: images,
      wordCount: content.split(/\s+/).length,
      publishDate: new Date().toISOString().split('T')[0]
    };

    extractedPosts.push(post);
    log(`Successfully parsed: ${htmlFile} (${post.wordCount} words, ${images.length} images)`);

    return post;
  } catch (error) {
    log(`Error parsing ${htmlFile}: ${error.message}`, 'error');
    return null;
  }
}

function generateCategoryStructure() {
  const categories = {};

  extractedPosts.forEach(post => {
    if (!categories[post.category]) {
      categories[post.category] = {
        name: post.category,
        slug: sanitizeSlug(post.category),
        description: `${post.category} related content`,
        parent: post.parentCategory,
        posts: []
      };
    }
    categories[post.category].posts.push(post.title);
  });

  return categories;
}

function generateWordPressSQL() {
  let sql = `-- =====================================================
-- PremierPlug WordPress Content Import
-- Generated: ${new Date().toISOString()}
-- Total Posts: ${extractedPosts.length}
-- =====================================================

-- Set SQL mode for safe import
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Begin transaction for safety
START TRANSACTION;

`;

  let postId = 100;
  let termId = 50;
  let termTaxonomyId = 50;

  const categories = generateCategoryStructure();
  const categoryIdMap = {};

  sql += `-- =====================================================
-- Create Categories
-- =====================================================\n\n`;

  Object.values(categories).forEach(cat => {
    categoryIdMap[cat.name] = termId;

    sql += `-- Category: ${cat.name}\n`;
    sql += `INSERT INTO wp_terms (term_id, name, slug, term_group) VALUES
(${termId}, ${sqlEscape(cat.name)}, ${sqlEscape(cat.slug)}, 0);\n\n`;

    sql += `INSERT INTO wp_term_taxonomy (term_taxonomy_id, term_id, taxonomy, description, parent, count) VALUES
(${termTaxonomyId}, ${termId}, 'category', ${sqlEscape(cat.description)}, 0, ${cat.posts.length});\n\n`;

    termId++;
    termTaxonomyId++;
  });

  sql += `\n-- =====================================================
-- Create Posts
-- =====================================================\n\n`;

  extractedPosts.forEach((post, index) => {
    const postDate = post.publishDate + ' 00:00:00';
    const guid = `http://premierplug.org/?p=${postId}`;

    sql += `-- Post ${index + 1}: ${post.title}\n`;
    sql += `INSERT INTO wp_posts (
  ID, post_author, post_date, post_date_gmt, post_content, post_title,
  post_excerpt, post_status, comment_status, ping_status, post_password,
  post_name, to_ping, pinged, post_modified, post_modified_gmt,
  post_content_filtered, post_parent, guid, menu_order, post_type,
  post_mime_type, comment_count
) VALUES (
  ${postId}, 1, '${postDate}', '${postDate}',
  ${sqlEscape(post.content)},
  ${sqlEscape(post.title)},
  ${sqlEscape(post.excerpt)},
  'publish', 'closed', 'closed', '',
  ${sqlEscape(post.slug)}, '', '', '${postDate}', '${postDate}',
  '', 0, ${sqlEscape(guid)}, 0, ${sqlEscape(post.postType)},
  '', 0
);\n\n`;

    const categoryId = categoryIdMap[post.category] || 1;
    sql += `-- Link post to category: ${post.category}\n`;
    sql += `INSERT INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
VALUES (${postId}, ${categoryId}, 0);\n\n`;

    if (post.featuredImage) {
      sql += `-- Post meta: Featured image (will need manual media ID)\n`;
      sql += `INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
VALUES (${postId}, '_thumbnail_id', 'REPLACE_WITH_MEDIA_ID');\n\n`;
    }

    sql += `-- Post meta: Original filename\n`;
    sql += `INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
VALUES (${postId}, '_original_html_file', ${sqlEscape(post.filename)});\n\n`;

    postId++;
  });

  sql += `-- =====================================================
-- Commit Transaction
-- =====================================================
COMMIT;

-- =====================================================
-- Import Complete
-- Total Posts Created: ${extractedPosts.length}
-- Total Categories Created: ${Object.keys(categories).length}
-- =====================================================
`;

  return sql;
}

function generateImageMappingCSV() {
  let csv = 'Original Path,New Path,Alt Text,Source Page,Exists,Error\n';

  imageMap.forEach(img => {
    csv += `"${img.originalPath}","${img.newPath}","${img.alt}","${img.sourcePage}","${img.exists}","${img.error || ''}"\n`;
  });

  return csv;
}

function generateReport() {
  const categories = generateCategoryStructure();

  let report = `# Content Extraction Report

**Generated:** ${new Date().toISOString()}

## Summary

- **Total HTML Files Processed:** ${extractedPosts.length}
- **Total Posts Created:** ${extractedPosts.filter(p => p.postType === 'post').length}
- **Total Pages Created:** ${extractedPosts.filter(p => p.postType === 'page').length}
- **Total Categories:** ${Object.keys(categories).length}
- **Total Images Extracted:** ${imageMap.length}
- **Images Found:** ${imageMap.filter(i => i.exists).length}
- **Images Missing:** ${imageMap.filter(i => !i.exists).length}
- **Errors:** ${errorCount}
- **Warnings:** ${warningCount}

## Category Breakdown

`;

  Object.values(categories).forEach(cat => {
    report += `### ${cat.name}\n`;
    report += `- **Slug:** ${cat.slug}\n`;
    report += `- **Posts:** ${cat.posts.length}\n`;
    report += `- **Posts List:**\n`;
    cat.posts.forEach(postTitle => {
      report += `  - ${postTitle}\n`;
    });
    report += `\n`;
  });

  report += `## Extracted Posts\n\n`;
  report += `| # | Title | Type | Category | Word Count | Images |\n`;
  report += `|---|-------|------|----------|------------|--------|\n`;

  extractedPosts.forEach((post, index) => {
    report += `| ${index + 1} | ${post.title} | ${post.postType} | ${post.category} | ${post.wordCount} | ${post.images.length} |\n`;
  });

  if (warningCount > 0 || errorCount > 0) {
    report += `\n## Issues\n\n`;

    if (errorCount > 0) {
      report += `### Errors (${errorCount})\n\n`;
      report += `Please check \`extraction-log.txt\` for error details.\n\n`;
    }

    if (warningCount > 0) {
      report += `### Warnings (${warningCount})\n\n`;
      report += `Please check \`extraction-log.txt\` for warning details.\n\n`;
    }
  }

  report += `\n## Next Steps\n\n`;
  report += `1. Review \`premierplug-content-import.sql\` before importing\n`;
  report += `2. Test import on a fresh WordPress installation\n`;
  report += `3. Upload images from \`extracted-images/\` to WordPress media library\n`;
  report += `4. Replace 'REPLACE_WITH_MEDIA_ID' in SQL with actual media IDs\n`;
  report += `5. Verify all posts imported correctly\n`;
  report += `6. Check for broken links or missing content\n`;

  return report;
}

async function main() {
  console.log('========================================');
  console.log('PremierPlug Content Extraction Tool');
  console.log('========================================\n');

  log('Starting content extraction...');

  try {
    await fs.ensureDir(CONFIG.outputDir);
    await fs.ensureDir(CONFIG.imagesDir);

    const htmlFiles = await fs.readdir(CONFIG.projectRoot);
    CONFIG.htmlFiles = htmlFiles.filter(f => f.endsWith('.html'));

    log(`Found ${CONFIG.htmlFiles.length} HTML files`);

    for (const htmlFile of CONFIG.htmlFiles) {
      await parseHtmlFile(htmlFile);
    }

    log('Generating WordPress SQL...');
    const sql = generateWordPressSQL();
    await fs.writeFile(CONFIG.sqlFile, sql, 'utf-8');
    log(`SQL file created: ${CONFIG.sqlFile}`);

    log('Generating image mapping CSV...');
    const csv = generateImageMappingCSV();
    await fs.writeFile(CONFIG.imageMappingFile, csv, 'utf-8');
    log(`Image mapping created: ${CONFIG.imageMappingFile}`);

    log('Generating category structure JSON...');
    const categories = generateCategoryStructure();
    await fs.writeFile(CONFIG.categoryFile, JSON.stringify(categories, null, 2), 'utf-8');
    log(`Category structure created: ${CONFIG.categoryFile}`);

    log('Generating extraction report...');
    const report = generateReport();
    await fs.writeFile(CONFIG.reportFile, report, 'utf-8');
    log(`Report created: ${CONFIG.reportFile}`);

    log('Writing log file...');
    await fs.writeFile(CONFIG.logFile, logMessages.join('\n'), 'utf-8');

    console.log('\n========================================');
    console.log('EXTRACTION COMPLETE');
    console.log('========================================');
    console.log(`Posts Extracted: ${extractedPosts.length}`);
    console.log(`Images Copied: ${imageMap.filter(i => i.exists).length}/${imageMap.length}`);
    console.log(`Errors: ${errorCount}`);
    console.log(`Warnings: ${warningCount}`);
    console.log('========================================\n');
    console.log(`Check ${CONFIG.outputDir} for all generated files.`);

  } catch (error) {
    log(`Fatal error: ${error.message}`, 'error');
    console.error('Fatal error:', error);
    process.exit(1);
  }
}

if (require.main === module) {
  main().catch(console.error);
}

module.exports = { main, parseHtmlFile, generateWordPressSQL };
