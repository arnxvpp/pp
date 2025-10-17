# WordPress Content Import Instructions

## Overview

This package contains extracted content from 25 HTML pages, ready to be imported into WordPress.

**Contents:**
- **25 Posts/Pages:** 14 posts + 11 pages
- **5 Categories:** Company Information, Enterprise Solutions, Research Services, Talent Services, Home
- **52 Images:** Organized in `extracted-images/` folder
- **824KB SQL File:** `premierplug-content-import.sql`

---

## Prerequisites

- Fresh WordPress installation (5.0+ recommended)
- MySQL/MariaDB access
- FTP or file manager access for image uploads
- Database backup (ALWAYS backup before importing)

---

## Step-by-Step Import Process

### Step 1: Backup Your Database

**CRITICAL:** Always backup before importing!

```bash
mysqldump -u username -p database_name > backup_before_import.sql
```

### Step 2: Review the SQL File

Open `premierplug-content-import.sql` and review:
- Categories (lines 17-55)
- Posts/Pages structure (lines 57+)
- Check for any placeholder values

### Step 3: Import SQL File

**Option A: Via phpMyAdmin**
1. Login to phpMyAdmin
2. Select your WordPress database
3. Click "Import" tab
4. Choose `premierplug-content-import.sql`
5. Click "Go"

**Option B: Via Command Line**
```bash
mysql -u username -p database_name < premierplug-content-import.sql
```

**Option C: Via WordPress Plugin**
- Install "WP DB Backup" or "All-in-One WP Migration"
- Use plugin interface to import SQL

### Step 4: Verify Import

After import, check WordPress admin:

1. **Posts:** Go to Posts → All Posts
   - Should see 14 posts across categories

2. **Pages:** Go to Pages → All Pages
   - Should see 11 pages

3. **Categories:** Go to Posts → Categories
   - Company Information (10 posts)
   - Enterprise Solutions (6 posts)
   - Research Services (3 posts)
   - Talent Services (5 posts)
   - Home (1 post)

### Step 5: Upload Images

**Upload all images from `extracted-images/` to WordPress Media Library:**

1. Go to Media → Add New
2. Upload all 24 image files from `extracted-images/`
3. Note the Media ID for each image

**Alternative: Use FTP**
```bash
# Upload to WordPress uploads directory
/wp-content/uploads/2025/10/
```

### Step 6: Link Images to Posts

**Manual Method:**

For each post:
1. Edit post in WordPress admin
2. Set Featured Image using uploaded images
3. Save post

**Automated Method (SQL):**

After uploading images, run this query to link featured images:

```sql
-- Example: Link image ID 123 to post ID 100
UPDATE wp_postmeta
SET meta_value = '123'
WHERE post_id = 100 AND meta_key = '_thumbnail_id';
```

Refer to `image-mapping.csv` to see which images belong to which pages.

### Step 7: Review Content

1. **Check each page:**
   - Visit frontend to verify content displays correctly
   - Check for missing images
   - Verify formatting (headings, paragraphs, lists)

2. **Fix any issues:**
   - Broken links
   - Missing content
   - Formatting problems

### Step 8: Configure WordPress Settings

1. **Set Homepage:**
   - Go to Settings → Reading
   - Choose "A static page"
   - Select home page

2. **Configure Permalinks:**
   - Go to Settings → Permalinks
   - Choose "Post name" structure
   - Save changes

3. **Menus:**
   - Go to Appearance → Menus
   - Create navigation menus manually using imported pages

---

## Troubleshooting

### SQL Import Fails

**Error: "Duplicate entry for key 'PRIMARY'"**
- Solution: IDs 50-54 (categories) and 100-124 (posts) might exist
- Fix: Edit SQL file and change IDs

**Error: "Unknown column"**
- Solution: WordPress version mismatch
- Fix: Update WordPress to latest version

### Images Not Showing

**Check:**
1. Images uploaded to Media Library?
2. Featured image set for each post?
3. File permissions correct (755 for folders, 644 for files)?

**Fix:**
```bash
# Set proper permissions
chmod 755 /wp-content/uploads/
chmod 644 /wp-content/uploads/*.jpg
chmod 644 /wp-content/uploads/*.jpeg
```

### Content Looks Wrong

**Common Issues:**
- HTML not rendering → Check post format (should be "Standard", not "Plain Text")
- Broken formatting → Re-edit in WordPress visual editor
- Missing paragraphs → Check content in Text view, add `<p>` tags if needed

### Categories Not Showing

**Check:**
```sql
-- Verify categories imported
SELECT * FROM wp_terms WHERE term_id BETWEEN 50 AND 54;

-- Verify term taxonomy
SELECT * FROM wp_term_taxonomy WHERE term_id BETWEEN 50 AND 54;

-- Verify post-category relationships
SELECT * FROM wp_term_relationships WHERE object_id BETWEEN 100 AND 124;
```

---

## Post-Import Checklist

- [ ] All 25 posts/pages imported successfully
- [ ] All 5 categories created
- [ ] All 52 images uploaded to Media Library
- [ ] Featured images assigned to posts
- [ ] All content displays correctly on frontend
- [ ] Navigation menus configured
- [ ] Homepage set correctly
- [ ] Permalinks configured
- [ ] No broken links
- [ ] Mobile responsive display working
- [ ] SEO plugin configured (if using Rank Math/Yoast)

---

## Files Reference

### Generated Files

| File | Description | Size |
|------|-------------|------|
| `premierplug-content-import.sql` | WordPress SQL import | 824KB |
| `extracted-images/` | 24 image files | Various |
| `image-mapping.csv` | Image reference guide | 6.5KB |
| `category-structure.json` | Category hierarchy | 1.6KB |
| `content-extraction-report.md` | Detailed report | 3.3KB |
| `extraction-log.txt` | Extraction process log | 9.5KB |

### Image Mapping

See `image-mapping.csv` for complete mapping of:
- Original image path
- New path in extracted-images/
- Alt text
- Source page
- Exists status

---

## Known Issues

### Missing Images (6 images)

These images were referenced but not found in source:
1. `../sites/default/files/inline-images/Untitled design.png` (human-rights.html)
2. `sites/default/files/styles/square/public/2022-03/PromoPhoto081221_08202021062355_large_1.jpeg`
3. `sites/default/files/styles/square/public/2022-03/DojaCat_05072018064023_large_0.jpeg`
4. `sites/default/files/styles/widescreen_landscape/public/2022-03/Zac Brown Band Heashot_2.png`
5. `sites/default/files/styles/square/public/2022-03/DKO096704262021081520large_06212021094947_1.jpeg`

**Solution:**
- Find original images from source website
- Upload manually to Media Library
- Link to appropriate posts

### Low Word Count Pages (3 pages)

These pages had very little content extracted:
- Digital Media (11 words)
- Marketing & IT (11 words)
- Index/Homepage (11 words)

**Solution:**
- Review original HTML files
- Extract content manually if needed
- Add placeholder content for now

---

## Support

If you encounter issues:

1. Check `extraction-log.txt` for warnings and errors
2. Review `content-extraction-report.md` for statistics
3. Verify database table prefixes (default: `wp_`)
4. Check WordPress version compatibility
5. Ensure proper file permissions

---

## Next Steps After Import

1. **Install Theme:** Use PremierPlug custom theme from `/premierplug-theme/`
2. **Install Plugins:**
   - PremierPlug Talent Manager (custom)
   - Rank Math (SEO)
   - Contact Form 7 (forms)
   - W3 Total Cache (performance)
3. **Configure SEO:** Add Indian market keywords
4. **Test Everything:** Click through all pages, test forms, check mobile
5. **Go Live:** Point domain to WordPress installation

---

## Estimated Time

- **SQL Import:** 2-5 minutes
- **Image Upload:** 10-15 minutes
- **Featured Image Assignment:** 30-45 minutes
- **Content Review:** 1-2 hours
- **Menu Configuration:** 30 minutes
- **Testing:** 1-2 hours

**Total:** 3-5 hours for complete import and setup

---

**Generated:** 2025-10-17

**Source:** 25 HTML files from PremierPlug static website

**Extraction Tool:** Node.js + Cheerio + fs-extra
