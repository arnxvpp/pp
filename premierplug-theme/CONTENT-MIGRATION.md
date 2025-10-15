# Content Migration Guide - Static HTML to WordPress

This guide helps you migrate content from the static PremierPlug HTML site to the WordPress theme.

## Overview

The static site contains 27 HTML pages that need to be converted to WordPress pages. This guide provides step-by-step instructions for migration.

## Page Mapping

### Homepage
- **Static:** `index.html`
- **WordPress:** Set as "Front Page" in Settings > Reading
- **Content:** Animated intro (automatic) or custom content
- **Special Notes:** Animation controlled via Customizer

### Research Section

| Static HTML File | WordPress Page Title | Suggested Slug |
|-----------------|---------------------|----------------|
| social-research.html | Social Research | social-research |
| market-research.html | Market Research | market-research |
| data-analysis.html | Data Analysis | data-analysis |

### For Talents Section

| Static HTML File | WordPress Page Title | Suggested Slug |
|-----------------|---------------------|----------------|
| motion-pictures.html | Motion Pictures | motion-pictures |
| digital-media.html | Digital Media | digital-media |
| speakers.html | Speakers | speakers |
| television.html | Television | television |
| voiceovers.html | Voiceovers | voiceovers |

### For Enterprise - Partnership Sales

| Static HTML File | WordPress Page Title | Suggested Slug |
|-----------------|---------------------|----------------|
| music-brand-partnerships.html | Music Brand Partnerships | music-brand-partnerships |
| publishing.html | Publishing | publishing |

### For Enterprise - Brand Solutions

| Static HTML File | WordPress Page Title | Suggested Slug |
|-----------------|---------------------|----------------|
| brand-consulting.html | Brand Consulting | brand-consulting |
| brandmanagement.html | Brand Management | brandmanagement |
| brand-studio_2.html | Brand Studio | brand-studio |
| marketing-it.html | Marketing & IT | marketing-it |

### Additional Pages

| Static HTML File | WordPress Page Title | Suggested Slug |
|-----------------|---------------------|----------------|
| about-us.html | About Us | about-us |
| careers.html | Careers | careers |
| contact.html | Contact | contact |
| entry-level-opportunities.html | Entry Level Opportunities | entry-level-opportunities |
| internships.html | Internships | internships |
| privacy-policy.html | Privacy Policy | privacy-policy |
| terms-of-use.html | Terms of Use | terms-of-use |
| client-privacy-notice.html | Client Privacy Notice | client-privacy-notice |
| human-rights.html | Human Rights | human-rights |
| social-responsibility.html | Social Responsibility | social-responsibility |

## Step-by-Step Migration Process

### Method 1: Manual Migration (Recommended for Small Sites)

#### For Each Page:

1. **Open the static HTML file** in a text editor
2. **Locate the main content area** (usually between `<main>` or `<article>` tags)
3. **Copy the content** (exclude header, footer, navigation)
4. **In WordPress admin:**
   - Go to `Pages > Add New`
   - Enter the page title
   - Paste content into the editor
   - Clean up HTML if needed
   - Set the slug (under Permalink)
   - Add featured image (if applicable)
   - Click `Publish`

#### Example: Migrating "About Us" Page

1. Open `about-us.html`
2. Find content between tags (typically starts after `<!-- HEADER CLOSED -->`)
3. Copy the content section
4. In WordPress:
   ```
   Title: About Us
   Slug: about-us
   Content: [paste content here]
   Featured Image: images/about-us.jpeg
   ```
5. Publish

### Method 2: Using Import Plugin

#### Setup

1. Install **WP All Import** plugin:
   - Go to `Plugins > Add New`
   - Search "WP All Import"
   - Install and activate

2. Install **HTML Import Extension**:
   - Go to `All Import > Settings`
   - Install HTML extension

#### Import Process

1. Prepare your HTML files in a spreadsheet:
   ```csv
   title,slug,content,featured_image
   "About Us","about-us","[content]","about-us.jpeg"
   "Careers","careers","[content]","career.jpeg"
   ```

2. Import using WP All Import:
   - Go to `All Import > New Import`
   - Upload CSV file
   - Map fields to WordPress
   - Run import

### Method 3: Custom Import Script

For developers comfortable with PHP, use the included import helper.

## Image Migration

### Hero Images

Match these images to their respective pages:

| Image File | Page |
|-----------|------|
| about-us.jpeg | About Us |
| brand-consulting.jpeg | Brand Consulting |
| brand-management.jpeg | Brand Management |
| brand-studio.jpeg | Brand Studio |
| career.jpeg | Careers |
| contact-us.jpeg | Contact |
| data-analysis.jpeg | Data Analysis |
| digital-media-roaster.jpeg | Digital Media |
| entry-level-opportunities.jpeg | Entry Level Opportunities |
| human-rights.jpeg | Human Rights |
| internship.jpeg | Internships |
| market-research.jpeg | Market Research |
| motion-picture.jpeg | Motion Pictures |
| music-brand-partnership.jpeg | Music Brand Partnerships |
| publishing.jpeg | Publishing |
| social-research.jpeg | Social Research |
| social-responsibility.jpeg | Social Responsibility |
| speakers.jpeg | Speakers |
| voiceover.jpeg | Voiceovers |

### Upload Process

1. Go to `Media > Add New`
2. Upload all images from `/images/` folder
3. For each page:
   - Edit the page
   - Click "Set Featured Image"
   - Select corresponding image
   - Update page

## Content Cleanup

After migration, check for:

### Links

- [ ] Update all internal links from `.html` to WordPress permalinks
- [ ] Example: Change `href="about-us.html"` to `href="/about-us/"`
- [ ] Test all navigation links

### Images

- [ ] Verify all images display correctly
- [ ] Update image paths if needed
- [ ] Use WordPress Media Library URLs

### Formatting

- [ ] Check heading hierarchy (H1, H2, H3)
- [ ] Verify paragraph spacing
- [ ] Check bullet lists and numbered lists
- [ ] Ensure blockquotes display correctly

### Special Elements

- [ ] Contact forms (replace with Contact Form 7 shortcode)
- [ ] Social media links
- [ ] Embedded videos
- [ ] Custom HTML/JavaScript

## SEO Migration

### Meta Titles

For each page, copy the `<title>` tag content from HTML:
1. Install Yoast SEO or Rank Math
2. Edit each page
3. Add the SEO title from the original HTML

### Meta Descriptions

Copy `<meta name="description">` content:
1. Find meta description in HTML `<head>`
2. Add to SEO plugin meta description field

### Example from index.html:
```html
<title>PremierPlug.Org - Modern Media Agency</title>
<meta name="description" content="The leading media consultancy...">
```

## Testing Checklist

After migration, verify:

### Functionality
- [ ] All pages load without errors
- [ ] Navigation menus work correctly
- [ ] Search functionality works
- [ ] Contact forms submit properly
- [ ] Mobile responsiveness
- [ ] Cross-browser compatibility

### Content
- [ ] All text migrated correctly
- [ ] Images display properly
- [ ] Links work (no 404 errors)
- [ ] Formatting preserved
- [ ] Special characters display correctly

### SEO
- [ ] Meta titles set
- [ ] Meta descriptions set
- [ ] URLs are clean (no .html extensions)
- [ ] Sitemap generated
- [ ] robots.txt configured

### Performance
- [ ] Page load time < 3 seconds
- [ ] Images optimized
- [ ] Caching enabled
- [ ] Lazy loading works

## Common Issues and Solutions

### Issue: Images Not Displaying
**Solution:**
- Check image paths in content
- Re-upload images to Media Library
- Use WordPress image insertion tool

### Issue: Broken Internal Links
**Solution:**
- Use Search & Replace plugin
- Find: `.html`
- Replace with: `/` (for pretty permalinks)

### Issue: Styling Differences
**Solution:**
- Check that CSS files loaded correctly
- Clear browser and WordPress cache
- Inspect elements for CSS conflicts

### Issue: Menu Not Matching Original
**Solution:**
- Verify menu structure in Appearance > Menus
- Check menu locations assigned correctly
- Ensure parent-child relationships correct

## Bulk Operations

### Find and Replace Links

Use **Better Search Replace** plugin:

1. Install plugin
2. Go to `Tools > Better Search Replace`
3. Search for: `.html"`
4. Replace with: `/"`
5. Select all tables
6. Run search (dry run first!)
7. Run replace

### Update Image Paths

If images don't load:

1. Search for: `images/`
2. Replace with: `<?php echo get_template_directory_uri(); ?>/assets/images/`
3. Or use WordPress Media Library instead

## Post-Migration Tasks

### 1. Set up 301 Redirects

Create redirects for old URLs:

Install **Redirection** plugin:
- about-us.html → /about-us/
- careers.html → /careers/
- etc.

### 2. Update Sitemap

1. Generate new XML sitemap (Yoast/Rank Math)
2. Submit to Google Search Console
3. Update robots.txt if needed

### 3. Set up Analytics

1. Install Google Analytics
2. Verify tracking works
3. Set up goals/conversions

### 4. Backup

1. Create full site backup
2. Test restore process
3. Set up automated backups

## Timeline Estimate

For manual migration of 27 pages:

- **Basic migration:** 4-6 hours
- **Content cleanup:** 2-3 hours
- **Image setup:** 1-2 hours
- **Menu configuration:** 1 hour
- **Testing:** 2-3 hours
- **SEO setup:** 1-2 hours

**Total:** 11-17 hours

## Support

If you encounter issues during migration:
- Check WordPress debug log
- Review browser console for errors
- Consult WordPress support forums
- Contact theme support

## Migration Checklist

Use this checklist to track progress:

### Pre-Migration
- [ ] Theme installed and activated
- [ ] Plugins installed
- [ ] Permalinks configured
- [ ] Backup of static site created

### Migration Phase
- [ ] All pages created
- [ ] All images uploaded
- [ ] Featured images assigned
- [ ] Menus created and assigned
- [ ] Content migrated
- [ ] Links updated

### Post-Migration
- [ ] All pages tested
- [ ] Forms tested
- [ ] SEO configured
- [ ] 301 redirects set up
- [ ] Analytics installed
- [ ] Site backup created
- [ ] Performance optimized

### Go-Live
- [ ] DNS pointed to WordPress site
- [ ] SSL certificate active
- [ ] Final testing completed
- [ ] Old site archived
