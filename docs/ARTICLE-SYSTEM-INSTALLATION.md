# Article Management System - Complete Installation Guide

**Version:** 1.1.0
**For:** PremierPlug Talent Management Plugin
**Platform:** Shared WordPress Hosting (No build tools required)
**Time Required:** 15 minutes

---

## Overview

This article management system adds 5 new custom post types to your talent management plugin:

- **Press Releases** - Official announcements and press materials
- **Blog Articles** - Long-form content and thought leadership
- **Awards** - Recognition and achievements
- **News** - Current events and updates
- **Media Coverage** - Third-party articles and features

### Key Features

✅ Link multiple articles to multiple talents (many-to-many relationships)
✅ Display articles on talent profile pages with tabbed interface
✅ Auto-sync to Supabase database in real-time
✅ Featured article highlighting
✅ Publication source attribution
✅ Responsive grid and list layouts
✅ AJAX-powered filtering
✅ Archive pages for each article type
✅ Related articles suggestions

---

## Prerequisites

Before installation, ensure you have:

- WordPress 6.0 or higher
- PHP 7.4 or higher
- PremierPlug Talent Management Plugin v1.0.0 installed
- Supabase account (free tier works)
- FTP/cPanel access to your hosting

---

## Installation Steps

### Step 1: Backup Your Site

**IMPORTANT:** Always backup before making changes!

```
Backup these:
- WordPress database (via phpMyAdmin or backup plugin)
- /wp-content/plugins/premierplug-talent-management/ folder
```

### Step 2: Upload New Files

Upload the following files via FTP/cPanel File Manager:

#### A. PHP Class Files (includes/ folder)

Upload to: `/wp-content/plugins/premierplug-talent-management/includes/`

```
class-article-post-types.php (19 KB)
class-article-relationships.php (11 KB)
class-article-metaboxes.php (18 KB)
class-article-supabase.php (13 KB)
class-article-queries.php (9 KB)
class-article-shortcodes.php (11 KB)
```

#### B. Admin Class File (admin/ folder)

Upload to: `/wp-content/plugins/premierplug-talent-management/admin/`

```
class-articles-manager.php (15 KB)
```

#### C. Template Files (templates/ folder)

Upload to: `/wp-content/plugins/premierplug-talent-management/templates/`

```
talent-articles-section.php (3 KB)
article-card.php (3 KB)
archive-articles.php (2 KB)
single-article.php (5 KB)
```

#### D. Asset Files

**CSS:**
Upload to: `/wp-content/plugins/premierplug-talent-management/assets/css/`

```
articles.css (18 KB)
```

**JavaScript:**
Upload to: `/wp-content/plugins/premierplug-talent-management/assets/js/`

```
article-frontend.js (2 KB)
```

#### E. Main Plugin File (OVERWRITE)

**IMPORTANT:** This file replaces your existing plugin file!

Replace: `/wp-content/plugins/premierplug-talent-management/premierplug-talent-management.php`

**Backup the old file first!**

### Step 3: Verify File Upload

Check that all files uploaded successfully:

```
Total files uploaded: 14
Total size: ~110 KB

Run this command via SSH (optional):
ls -lh /wp-content/plugins/premierplug-talent-management/includes/class-article-*.php
```

You should see 6 class-article-*.php files listed.

### Step 4: Deactivate and Reactivate Plugin

1. Login to WordPress Admin: `https://yoursite.com/wp-admin`
2. Go to: **Plugins → Installed Plugins**
3. Find: "PremierPlug Talent Management"
4. Click: **Deactivate**
5. Wait 5 seconds
6. Click: **Activate**

This will:
- Register new post types
- Create WordPress database table for relationships
- Flush rewrite rules for new URLs

### Step 5: Verify Plugin Activation

Check that everything activated correctly:

1. Go to WordPress Admin sidebar
2. You should now see new menu: **Articles** (with portfolio icon)
3. Under Articles, you should see:
   - All Articles
   - Press Releases
   - Blog
   - Awards
   - News
   - Media Coverage

If you see all these menu items, **Step 5 is complete!** ✅

### Step 6: Create Supabase Database Tables

#### A. Login to Supabase

1. Go to: https://supabase.com
2. Login to your account
3. Select your project

#### B. Open SQL Editor

1. Click **SQL Editor** in left sidebar
2. Click **New Query**

#### C. Run Migration SQL

1. Open file: `ARTICLE-SYSTEM-SUPABASE.sql`
2. Copy entire contents
3. Paste into Supabase SQL Editor
4. Click **Run** button (bottom right)

**Expected Result:**
You should see: "Success. No rows returned"

#### D. Verify Tables Created

1. Click **Table Editor** in left sidebar
2. You should see 2 new tables:
   - `talent_articles`
   - `talent_article_relationships`

If you see both tables, **Step 6 is complete!** ✅

### Step 7: Configure Supabase Connection (Optional)

**Note:** If you already have Supabase credentials in your wp-config.php from the talent plugin, you can skip this step. The article system uses the same credentials.

If NOT configured yet:

#### Option A: Via wp-config.php (Recommended)

1. Open: `/wp-config.php` via FTP
2. Add these lines BEFORE `/* That's all, stop editing! */`:

```php
// Supabase Configuration
define('SUPABASE_URL', 'https://your-project.supabase.co');
define('SUPABASE_KEY', 'your-anon-key-here');
```

3. Save and upload

Get your credentials from Supabase:
- Dashboard → Settings → API
- Copy: Project URL
- Copy: anon/public key

#### Option B: Via .env File

Already configured in your `.env` file:

```
VITE_SUPABASE_URL=https://mdniuqoqqbcvlvldfskj.supabase.co
VITE_SUPABASE_ANON_KEY=your-key-here
```

The plugin will read from either location.

### Step 8: Test Supabase Connection

1. Go to: **WordPress Admin → Articles → All Articles**
2. Scroll down to "Supabase Sync" panel (right side)
3. Click: **Test Connection** button

**Expected Result:**
Green message: "Connection successful"

If you see this, **Step 8 is complete!** ✅

If connection fails:
- Verify Supabase credentials are correct
- Check that tables were created in Step 6
- Check PHP error logs for details

### Step 9: Flush Permalinks

**CRITICAL:** This ensures new URLs work correctly!

1. Go to: **Settings → Permalinks**
2. DON'T change anything
3. Just click: **Save Changes**

This flushes the rewrite rules and activates:
- `/press-releases/` archive
- `/blog/` archive
- `/awards/` archive
- `/news/` archive
- `/media-coverage/` archive

### Step 10: Create Test Article

Let's verify everything works!

#### A. Create Press Release

1. Go to: **Articles → Press Releases → Add New**
2. Title: "Test Press Release - Please Delete"
3. Content: "This is a test article to verify the system works."
4. Set Featured Image: Upload any image
5. Scroll to "Article Details" box
6. Publication Date: Today's date
7. Source: "Test Source"
8. Check: "Featured Article"
9. Click: **Publish**

#### B. Link to Talent

1. While still editing the press release
2. Find: "Link to Talents" metabox (right sidebar)
3. Search field: Start typing a talent name
4. Select a talent from dropdown
5. Talent appears below search
6. Click the radio button: "Primary"
7. Click: **Update**

#### C. Verify Sync to Supabase

1. Go to Supabase Dashboard
2. Click: **Table Editor**
3. Open: `talent_articles` table
4. You should see your test article!
5. Open: `talent_article_relationships` table
6. You should see the talent-article link!

If you see both entries, **Step 10 is complete!** ✅

---

## Verification Checklist

Run through this checklist to ensure everything is working:

### WordPress Admin

- [ ] Articles menu appears in admin sidebar
- [ ] All 5 article types show in submenu
- [ ] "All Articles" dashboard loads without errors
- [ ] Can create new press release
- [ ] Can search and link talents
- [ ] Can mark article as featured
- [ ] Supabase connection test passes
- [ ] Article appears in admin list

### Frontend

- [ ] Visit: `/press-releases/` - Archive page displays
- [ ] Visit single article permalink - Article displays correctly
- [ ] Visit talent profile page - Articles section appears
- [ ] Click tab buttons - Articles filter correctly (AJAX)
- [ ] Featured badge shows on featured articles
- [ ] Article cards display with images
- [ ] Source attribution shows correctly
- [ ] Related articles appear on single article page

### Database

- [ ] Supabase `talent_articles` table has test article
- [ ] Supabase `talent_article_relationships` table has link
- [ ] WordPress `wp_talent_article_relationships` table exists
- [ ] No PHP errors in WordPress debug.log

---

## Usage Examples

### Creating a Press Release

1. **WordPress Admin → Press Releases → Add New**
2. Enter title: "Actor Wins Best Performance Award"
3. Add full content with details
4. Set featured image
5. Scroll to "Article Details":
   - Publication Date: 2024-12-15
   - Source: Hollywood Reporter
   - Source URL: https://example.com/article
   - Author: Jane Smith
   - ✓ Featured Article
6. Scroll to "Link to Talents":
   - Search: "John"
   - Select: "John Doe"
   - Mark as Primary
7. Click **Publish**

**Result:** Article appears on John Doe's talent profile under "Awards" tab, synced to Supabase instantly!

### Displaying Articles on Talent Page

**Automatic Display:**
Articles automatically appear on talent single pages in a tabbed interface.

To customize the display location, edit your theme's single-talent.php:

```php
<?php
// After talent bio section
if (function_exists('PPTM_Article_Queries')) {
    include(WP_PLUGIN_DIR . '/premierplug-talent-management/templates/talent-articles-section.php');
}
?>
```

### Using Shortcodes

**Display all articles for a talent:**

```
[talent_articles id="123"]
```

**Display only press releases:**

```
[talent_articles id="123" type="press_release"]
```

**Display recent articles site-wide:**

```
[recent_articles limit="5"]
```

**Display featured articles:**

```
[featured_articles limit="3"]
```

**Display article grid:**

```
[article_grid type="blog_article" limit="12" columns="3"]
```

---

## Troubleshooting

### Issue: Plugin won't activate

**Solution:**
- Check PHP version (must be 7.4+)
- Check for syntax errors in uploaded files
- Enable WP_DEBUG in wp-config.php
- Check WordPress debug.log

### Issue: Articles menu doesn't appear

**Solution:**
- Deactivate and reactivate plugin
- Clear WordPress cache
- Check user has 'edit_posts' capability
- Verify all PHP files uploaded correctly

### Issue: "Link to Talents" metabox not showing

**Solution:**
- Verify class-article-metaboxes.php uploaded
- Check browser console for JavaScript errors
- Clear browser cache
- Verify jQuery is loaded

### Issue: Talent search autocomplete not working

**Solution:**
- Check browser console for errors
- Verify AJAX endpoint: `/wp-admin/admin-ajax.php`
- Check that talents exist and are published
- Verify jQuery and admin.js loaded

### Issue: Articles not syncing to Supabase

**Solution:**
- Test connection in Articles dashboard
- Verify Supabase credentials correct
- Check Supabase tables exist
- Enable WordPress debugging:
  ```php
  define('WP_DEBUG', true);
  define('WP_DEBUG_LOG', true);
  ```
- Check debug.log for sync errors

### Issue: Tabbed interface not switching

**Solution:**
- Check browser console for JavaScript errors
- Verify article-frontend.js loaded
- Check that articles exist for talent
- Clear browser cache (Ctrl+F5)

### Issue: Archive pages show 404

**Solution:**
- Go to Settings → Permalinks → Save Changes
- This flushes rewrite rules
- If still 404, check post types registered
- Verify plugin is activated

### Issue: Images not displaying

**Solution:**
- Verify featured images set on articles
- Check image URLs in browser console
- Verify image files exist in uploads folder
- Check file permissions (should be 644)

### Issue: Related articles not showing

**Solution:**
- Ensure articles are linked to same talent
- Verify at least 2 articles linked to shared talent
- Check that articles are published (not draft)
- Clear object cache if using caching plugin

---

## Database Schema Reference

### WordPress Tables

**Table:** `{prefix}_talent_article_relationships`

```sql
Columns:
- id (BIGINT, primary key)
- talent_id (BIGINT, talent post ID)
- article_id (BIGINT, article post ID)
- is_primary_talent (TINYINT, 0 or 1)
- display_order (INT, for sorting)
- created_at (DATETIME)

Indexes:
- unique_relationship (talent_id, article_id)
- idx_talent (talent_id)
- idx_article (article_id)
- idx_primary (is_primary_talent)
```

### Supabase Tables

**Table:** `talent_articles`

```sql
Columns:
- id (BIGSERIAL, primary key)
- wordpress_post_id (BIGINT, unique)
- title (TEXT)
- content (TEXT)
- excerpt (TEXT)
- article_type (TEXT, enum)
- featured_image_url (TEXT)
- publication_date (TIMESTAMPTZ)
- source_name (TEXT)
- source_url (TEXT)
- author_name (TEXT)
- is_featured (BOOLEAN)
- slug (TEXT)
- status (TEXT)
- view_count (INTEGER)
- created_at (TIMESTAMPTZ)
- updated_at (TIMESTAMPTZ)

Constraints:
- article_type IN ('press_release', 'blog_article', 'award', 'news', 'media_coverage')
- status IN ('publish', 'draft', 'pending', 'private')
```

**Table:** `talent_article_relationships`

```sql
Columns:
- id (BIGSERIAL, primary key)
- talent_id (BIGINT)
- article_id (BIGINT, foreign key → talent_articles.id)
- is_primary_talent (BOOLEAN)
- display_order (INTEGER)
- created_at (TIMESTAMPTZ)

Constraints:
- UNIQUE(talent_id, article_id)
- ON DELETE CASCADE for article_id
```

---

## Performance Optimization

### WordPress Object Caching

The plugin automatically uses WordPress transients for caching. To improve performance:

1. Install Redis/Memcached on server
2. Install Object Cache plugin
3. Enable persistent object cache

### Database Indexes

All necessary indexes are created automatically:
- WordPress: On talent_id, article_id, is_primary
- Supabase: On article_type, status, featured, publication_date

### Image Optimization

For best performance:
- Use WebP format for images
- Resize images before upload (max 1200px wide)
- Use lazy loading (built into WordPress 5.5+)
- Consider CDN for image delivery

### AJAX Performance

The tabbed interface uses AJAX to prevent full page reloads:
- First tab loads with page (no AJAX)
- Other tabs load on demand
- Results cached in browser session

---

## Security Best Practices

### Implemented Security Features

✅ **Nonce verification** on all form submissions
✅ **Capability checks** (only editors+ can manage articles)
✅ **Input sanitization** on all user input
✅ **Output escaping** on all displayed data
✅ **SQL injection prevention** via $wpdb->prepare()
✅ **XSS prevention** via esc_html, esc_url, esc_attr
✅ **Supabase RLS policies** for database security
✅ **CSRF protection** via WordPress nonces

### Additional Recommendations

1. **Keep WordPress Updated:** Always use latest version
2. **Use Strong Passwords:** For all admin accounts
3. **Limit File Permissions:** 644 for files, 755 for folders
4. **Use SSL:** HTTPS for all admin pages
5. **Regular Backups:** Automated daily backups
6. **Monitor Logs:** Check debug.log regularly
7. **Firewall:** Use Cloudflare or similar WAF

---

## Uninstallation (If Needed)

To remove the article system (keeps existing talent plugin):

### Step 1: Delete Articles (Optional)

If you want to keep articles, skip this.

Otherwise:
1. Go to each article type (Press Releases, Blog, etc.)
2. Select all → Move to Trash
3. Empty trash

### Step 2: Delete Supabase Tables

```sql
DROP TABLE IF EXISTS talent_article_relationships CASCADE;
DROP TABLE IF EXISTS talent_articles CASCADE;
```

### Step 3: Delete WordPress Table

```sql
DROP TABLE IF EXISTS wp_talent_article_relationships;
```

### Step 4: Remove Files

Delete these files from plugin folder:
- includes/class-article-*.php (6 files)
- admin/class-articles-manager.php
- templates/talent-articles-section.php
- templates/article-card.php
- templates/archive-articles.php
- templates/single-article.php
- assets/css/articles.css
- assets/js/article-frontend.js

### Step 5: Restore Old Plugin File

Replace premierplug-talent-management.php with your backup from Step 1.

### Step 6: Reactivate Plugin

Deactivate and reactivate plugin to clear any cached data.

---

## Support & Resources

### Documentation

- Main Plugin Docs: `/PLUGIN-TALENT-MANAGEMENT.md`
- Theme Docs: `/INSTALLATION.md`
- Content Import: `/IMPORT-INSTRUCTIONS.md`

### Troubleshooting

- Enable debugging: Set `WP_DEBUG` to `true` in wp-config.php
- Check logs: `/wp-content/debug.log`
- Browser console: Press F12 to check for errors

### Testing

Run through verification checklist after installation to ensure everything works.

### Getting Help

If issues persist:
1. Check WordPress debug.log
2. Check browser console (F12)
3. Verify all files uploaded correctly
4. Test with default WordPress theme
5. Disable other plugins temporarily

---

## Changelog

### Version 1.1.0 (December 2024)

**Added:**
- 5 new article post types (press releases, blog, awards, news, media coverage)
- Many-to-many relationships between talents and articles
- Tabbed article display on talent profiles
- AJAX article filtering
- Supabase real-time sync for articles
- Featured article marking
- Publication source attribution
- Archive pages for each article type
- Related articles functionality
- Admin dashboard for article management
- 4 new shortcodes for displaying articles

**Technical:**
- 14 new files (6 classes, 4 templates, 2 assets, 1 admin, 1 main)
- 2 new Supabase tables with RLS
- 1 new WordPress database table
- ~4,200 lines of production code
- Full responsive design support
- Complete error handling and logging

---

## Quick Reference

### File Structure

```
premierplug-talent-management/
├── includes/
│   ├── class-article-post-types.php
│   ├── class-article-relationships.php
│   ├── class-article-metaboxes.php
│   ├── class-article-supabase.php
│   ├── class-article-queries.php
│   └── class-article-shortcodes.php
├── admin/
│   └── class-articles-manager.php
├── templates/
│   ├── talent-articles-section.php
│   ├── article-card.php
│   ├── archive-articles.php
│   └── single-article.php
├── assets/
│   ├── css/articles.css
│   └── js/article-frontend.js
└── premierplug-talent-management.php (UPDATED)
```

### URLs

```
Admin Dashboard: /wp-admin/admin.php?page=talent-articles
Press Releases: /press-releases/
Blog: /blog/
Awards: /awards/
News: /news/
Media Coverage: /media-coverage/
Single Article: /press-releases/article-slug/
Talent Profile: /talents/talent-name/ (articles section included)
```

### Shortcodes

```
[talent_articles id="123" type="press_release" limit="10"]
[recent_articles limit="5" type="blog_article"]
[article_grid type="award" limit="12" columns="3"]
[featured_articles limit="3"]
```

---

## Success!

Your article management system is now installed and ready to use! 🎉

**What's Next?**

1. Create your first real article
2. Link it to talents
3. Check talent profile pages
4. Verify Supabase sync
5. Customize templates as needed
6. Add shortcodes to pages

**Questions?** Refer to the troubleshooting section above.

---

**Installation Guide Version:** 1.0
**Last Updated:** December 2024
**Plugin Version:** 1.1.0
**License:** GPL v2 or later
