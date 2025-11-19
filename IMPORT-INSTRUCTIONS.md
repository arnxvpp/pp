# 🚀 Content Import Instructions

## ✅ What's Ready

I've created a complete automated import system that will:
- ✅ Import all 26 HTML pages
- ✅ Create 3-level navigation menus
- ✅ Assign all featured images
- ✅ Set up parent-child page relationships
- ✅ Sync everything to Supabase
- ✅ Generate detailed import report

**Time to complete: 5-10 minutes!**

## 📦 Files Created

1. **premierplug-content-importer.php** - Main import script
2. **supabase-pages-migration.sql** - Database schema
3. **premierplug-supabase-sync.php** - Auto-sync plugin

## 🚀 Step-by-Step Import Process

### Step 1: Upload Files to WordPress (2 minutes)

Upload these files to your WordPress root directory:

```
your-wordpress/
├── premierplug-content-importer.php  ← Upload this
├── premierplug-supabase-sync.php     ← Upload this
├── archive/                           ← Already there
│   ├── index.html
│   ├── about-us.html
│   └── [all 26 HTML files]
└── images/                            ← Already there
    ├── about-us.jpeg
    ├── career.jpeg
    └── [all 30 images]
```

**Via FTP:**
```
1. Connect to your server via FTP
2. Navigate to public_html/ or www/
3. Upload both PHP files
4. Ensure archive/ and images/ folders are present
```

**Via cPanel:**
```
1. Go to cPanel → File Manager
2. Navigate to public_html/
3. Click Upload
4. Select both PHP files
5. Upload
```

### Step 2: Set Up Supabase (3 minutes)

**2.1 Create Database Tables:**
```
1. Go to Supabase Dashboard
2. Click your project
3. Go to SQL Editor
4. Click "New Query"
5. Copy contents of: supabase-pages-migration.sql
6. Paste into editor
7. Click "Run"
8. Wait for "Success" message
```

**2.2 Verify Tables Created:**
```sql
-- Run this query to verify:
SELECT table_name
FROM information_schema.tables
WHERE table_schema = 'public'
AND table_name IN ('pages', 'menus', 'menu_items');
```

You should see 3 tables listed.

### Step 3: Run Import Script (2 minutes)

**3.1 Access Import Page:**
```
Visit: https://your-wordpress-site.com/premierplug-content-importer.php?key=premierplug_import_2024
```

**3.2 Watch Progress:**
- Script will show progress bar
- Each page will be listed as it's created
- Green checkmarks = success
- Red crosses = error (check permissions)

**3.3 Expected Output:**
```
✓ About Us - Created (ID: 2)
✓ Careers - Created (ID: 3)
✓ Contact - Created (ID: 4)
✓ Research - Created (ID: 5)
✓ Social Research - Created (ID: 6, Parent: research)
...
✓ Pages created: 26
✓ Primary menu created with 3 levels
✓ Footer menu created
✓ Featured images assigned
```

### Step 4: Activate Supabase Sync Plugin (1 minute)

**4.1 Install Plugin:**
```
1. Go to: WordPress Admin → Plugins → Add New
2. Click "Upload Plugin"
3. Select: premierplug-supabase-sync.php
4. Click "Install Now"
5. Click "Activate"
```

**4.2 Configure Settings:**
```
1. Go to: Settings → Supabase Sync
2. Enter Supabase URL (from .env file)
3. Enter Supabase Anon Key (from .env file)
4. Click "Save Settings"
```

**4.3 Run Manual Sync:**
```
1. On same page, click "Sync All Pages Now"
2. Wait for success message
3. Check Supabase dashboard to verify data
```

### Step 5: Verify Import (3 minutes)

**5.1 Check Homepage:**
```
1. Visit: https://your-site.com
2. Verify animated intro plays
3. Check hamburger menu opens
```

**5.2 Check Navigation:**
```
1. Click hamburger icon
2. Verify menu structure:
   ✓ Research (parent)
     ✓ Social Research
     ✓ Market Research
     ✓ Data Analysis
   ✓ For Talents (parent)
     ✓ Motion Pictures
     ✓ Digital Media
     ✓ Speakers
     ✓ Television
     ✓ Voiceovers
   ✓ For Enterprise (parent)
     ✓ Partnership Sales (sub-parent)
       ✓ Music Brand Partnerships
       ✓ Publishing
     ✓ Brand Solutions (sub-parent)
       ✓ Brand Consulting
       ✓ Brand Management
       ✓ Brand Studio
       ✓ Marketing & IT
```

**5.3 Check Pages:**
```
1. Click on "Social Research"
2. Verify:
   ✓ Hero image displays (social-research.jpeg)
   ✓ Page title shows
   ✓ Content displays properly
   ✓ Formatting preserved
```

**5.4 Check Footer:**
```
1. Scroll to bottom
2. Click "About" in footer menu
3. Verify page loads
4. Check "Careers" and "Contact" links work
```

**5.5 Check Supabase:**
```
1. Go to Supabase Dashboard
2. Go to Table Editor
3. Select "pages" table
4. Verify 26 rows present
5. Check data looks correct
6. Select "menus" table
7. Verify 2 menus (Primary, Footer)
8. Select "menu_items" table
9. Verify all menu items present
```

### Step 6: Clean Up (1 minute)

**6.1 Delete Import Script:**
```
⚠️ IMPORTANT FOR SECURITY!

Delete this file from your server:
- premierplug-content-importer.php

Via FTP or cPanel File Manager
```

**6.2 Keep Sync Plugin:**
```
✓ Keep premierplug-supabase-sync.php active
✓ It will auto-sync future page changes
```

## ✅ Verification Checklist

After import, verify these:

### Pages (26 total)
- [ ] All pages created in WordPress
- [ ] No duplicate pages
- [ ] Content displays correctly
- [ ] Images load properly
- [ ] No broken formatting

### Navigation
- [ ] Hamburger menu opens/closes
- [ ] 3-level menu structure works
- [ ] All menu items clickable
- [ ] Submenus expand correctly
- [ ] Footer menu works

### Images
- [ ] All featured images assigned
- [ ] Images display on pages
- [ ] No broken image links
- [ ] Correct image for each page

### Hierarchy
- [ ] Parent pages have children
- [ ] "Research" parent of 3 pages
- [ ] "For Talents" parent of 5 pages
- [ ] "For Enterprise" has 2 sub-parents
- [ ] "Partnership Sales" has children
- [ ] "Brand Solutions" has children

### Supabase
- [ ] Pages table populated (26 rows)
- [ ] Menus table has 2 menus
- [ ] Menu items table populated
- [ ] Data matches WordPress
- [ ] No sync errors in logs

## 🐛 Troubleshooting

### Import Script Won't Run

**Problem:** "Access denied" message
```
Solution:
1. Check URL has correct key parameter
2. Ensure logged in as WordPress admin
3. Try logging out and back in
```

**Problem:** "No such file" error
```
Solution:
1. Verify archive/ folder exists
2. Check HTML files are inside archive/
3. Check file permissions (755 for folders, 644 for files)
```

### Pages Not Creating

**Problem:** Import shows errors
```
Solution:
1. Check WordPress file permissions
2. Ensure you're logged in as admin
3. Check PHP error logs
4. Verify database connection
```

**Problem:** "Permission denied"
```
Solution:
1. Set correct file permissions:
   - Folders: 755
   - Files: 644
2. Check WordPress user can create pages
3. Verify hosting allows wp_insert_post()
```

### Images Not Showing

**Problem:** Featured images missing
```
Solution:
1. Verify images/ folder exists
2. Check images are .jpeg/.jpg files
3. Ensure file names match exactly
4. Check image file permissions (644)
5. Try re-uploading images
```

**Problem:** Broken image links in content
```
Solution:
Images in content should auto-fix, but if not:
1. Edit page in WordPress
2. Search for: src="images/
3. Replace with full URL or update image path
```

### Menus Not Working

**Problem:** Menu items don't show
```
Solution:
1. Go to: Appearance → Menus
2. Verify "Primary Navigation" exists
3. Check menu assigned to "Primary" location
4. Verify "Footer Navigation" exists
5. Check assigned to "Footer" location
```

**Problem:** Submenu structure wrong
```
Solution:
1. Go to: Appearance → Menus
2. Drag menu items to correct positions
3. Indent children under parents
4. Click "Save Menu"
```

### Supabase Not Syncing

**Problem:** No data in Supabase
```
Solution:
1. Check Supabase URL and key are correct
2. Go to: Settings → Supabase Sync
3. Verify credentials saved
4. Click "Sync All Pages Now"
5. Check Supabase error logs
```

**Problem:** "Connection failed" error
```
Solution:
1. Verify Supabase project is running
2. Check URL format: https://xxxxx.supabase.co
3. Verify anon key is correct (starts with eyJ...)
4. Check RLS policies allow inserts
5. Try from Supabase SQL Editor first
```

### General Issues

**Problem:** "Maximum execution time" error
```
Solution:
1. Increase PHP max_execution_time
2. In php.ini or .htaccess:
   php_value max_execution_time 300
3. Or import pages in batches
```

**Problem:** "Memory limit" error
```
Solution:
1. Increase PHP memory_limit
2. In wp-config.php:
   define('WP_MEMORY_LIMIT', '256M');
```

## 📊 What Gets Imported

### Complete Page List

| # | Page | Parent | Image | Menu |
|---|------|--------|-------|------|
| 1 | About Us | - | about-us.jpeg | Footer |
| 2 | Careers | - | career.jpeg | Footer |
| 3 | Contact | - | contact-us.jpeg | Footer |
| 4 | Research | - | - | Primary |
| 5 | Social Research | Research | social-research.jpeg | Primary → Research |
| 6 | Market Research | Research | market-research.jpeg | Primary → Research |
| 7 | Data Analysis | Research | data-analysis.jpeg | Primary → Research |
| 8 | For Talents | - | - | Primary |
| 9 | Motion Pictures | For Talents | motion-picture.jpeg | Primary → For Talents |
| 10 | Digital Media | For Talents | digitalmedia.jpg | Primary → For Talents |
| 11 | Speakers | For Talents | speakers.jpeg | Primary → For Talents |
| 12 | Television | For Talents | - | Primary → For Talents |
| 13 | Voiceovers | For Talents | voiceover.jpeg | Primary → For Talents |
| 14 | For Enterprise | - | - | Primary |
| 15 | Partnership Sales | For Enterprise | - | Primary → For Enterprise |
| 16 | Music Brand Partnerships | Partnership Sales | music-brand-partnership.jpeg | Primary → For Enterprise → Partnership Sales |
| 17 | Publishing | Partnership Sales | publishing.jpeg | Primary → For Enterprise → Partnership Sales |
| 18 | Brand Solutions | For Enterprise | - | Primary → For Enterprise |
| 19 | Brand Consulting | Brand Solutions | brand-consulting.jpeg | Primary → For Enterprise → Brand Solutions |
| 20 | Brand Management | Brand Solutions | brand-management.jpeg | Primary → For Enterprise → Brand Solutions |
| 21 | Brand Studio | Brand Solutions | brand-studio.jpeg | Primary → For Enterprise → Brand Solutions |
| 22 | Marketing & IT | Brand Solutions | - | Primary → For Enterprise → Brand Solutions |
| 23 | Privacy Policy | - | - | - |
| 24 | Terms of Use | - | - | - |
| 25 | Client Privacy Notice | - | client-privacy-notice.jpeg | - |
| 26 | Human Rights | - | human-rights.jpeg | - |
| 27 | Social Responsibility | - | social-responsibility.jpeg | - |
| 28 | Entry Level Opportunities | Careers | entry-level-opportunities.jpeg | - |
| 29 | Internships | Careers | internship.jpeg | - |

**Total:** 29 pages (26 HTML + 3 parent placeholders)

## 🎯 Post-Import Tasks

### 1. Add Contact Form (5 minutes)

**Install Contact Form 7:**
```
1. Go to: Plugins → Add New
2. Search: "Contact Form 7"
3. Click Install → Activate
4. Go to: Contact → Contact Forms
5. Edit default form or create new
6. Copy shortcode
7. Edit "Contact" page
8. Paste shortcode
9. Update page
```

### 2. Review Content (15 minutes)

```
Go through each page and:
1. Check content displays correctly
2. Fix any formatting issues
3. Update outdated information
4. Add any missing content
5. Check internal links work
```

### 3. Set Up Permalinks

```
1. Go to: Settings → Permalinks
2. Select: "Post name"
3. Click "Save Changes"
4. Test all pages load correctly
```

### 4. Install Recommended Plugins

```
SEO:
- Yoast SEO or Rank Math

Performance:
- WP Rocket or W3 Total Cache

Security:
- Wordfence Security

Backup:
- UpdraftPlus
```

## 📞 Support

### Common Questions

**Q: How long does import take?**
A: 2-5 minutes for all 26 pages

**Q: Will existing pages be affected?**
A: No, only creates new pages

**Q: Can I re-run import?**
A: Yes, but delete imported pages first

**Q: Does it work with page builders?**
A: Yes, compatible with Elementor, Gutenberg, etc.

**Q: What about custom fields?**
A: Script focuses on core content; custom fields need manual setup

### Need Help?

Check these files for more info:
- `CONTENT-IMPORT-PLAN.md` - Detailed plan
- `PLUGIN-FIX-SUMMARY.md` - Plugin documentation
- `THEME-ENHANCEMENTS.md` - Theme info

## 🎉 Success!

Once complete, you'll have:
- ✅ 26+ WordPress pages with content
- ✅ Complete 3-level navigation menu
- ✅ Footer navigation menu
- ✅ All images assigned and working
- ✅ Perfect page hierarchy
- ✅ Everything synced to Supabase
- ✅ Auto-sync for future changes

**Your site is now live and fully functional!** 🚀
