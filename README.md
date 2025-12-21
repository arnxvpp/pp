# PremierPlug - WordPress Talent Management System

**Complete WordPress solution** for talent agencies and media management companies.

**Live Site**: https://wp.premierplug.org

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Install Theme
```
1. Download: packages/premierplug-theme-v1.0.1.zip
2. WordPress Admin → Appearance → Themes → Add New → Upload
3. Activate theme
4. Clear browser cache (Ctrl+Shift+R)
```

### Step 2: Install Plugin
```
1. Download: packages/premierplug-talent-management-v1.2.0.zip
2. WordPress Admin → Plugins → Add New → Upload
3. Activate plugin
```

### Step 3: Import Content
```
1. Upload premierplug-content-importer-v1.2-FIXED.php to WordPress root
2. Visit: https://your-site.com/premierplug-content-importer-v1.2-FIXED.php?key=premierplug_import_2024
3. Wait for completion message
4. Clear browser cache
5. Delete import file (security)
```

### Done!
Visit your site - should look professional with proper fonts, colors, spacing, and layout.

---

## 📦 What's Included

### 1. WordPress Theme v1.0.1 (CSS Fixed)
**File**: `packages/premierplug-theme-v1.0.1.zip` (222KB)

**Features**:
- Complete design system (717KB CSS)
- Brand fonts (pf_dintext_pro, Helvetica Neue)
- Responsive layout (mobile/tablet/desktop)
- 3-level navigation support
- Professional agency styling
- Print stylesheet

**Fixed Issues**:
- ✅ Font loading (no square boxes)
- ✅ Correct brand colors
- ✅ Proper spacing and layout
- ✅ All CSS files properly loaded

### 2. WordPress Plugin v1.2.0
**File**: `packages/premierplug-talent-management-v1.2.0.zip`

**Core Features**:
- Talent profile management
- Article system (5 types: press, blog, awards, news, media)
- Talent-article relationships
- Advanced search and filtering
- Multiple display templates
- Custom post types
- Taxonomies (categories, tags, locations)

**Growth Features** (Built-in):
- SEO Manager with Schema.org
- Google Analytics 4 integration
- Social Sharing (6 networks)
- Related Articles algorithm
- Ad Manager (5 zones)
- Email Capture forms
- Speed Optimizer

### 3. Content Importer v1.2-FIXED
**File**: `packages/premierplug-content-importer-v1.2-FIXED.php` (25KB)

**Features**:
- Imports 29 pages from original HTML site
- Creates 3-level navigation hierarchy
- Assigns 26 featured images
- Sets up primary and footer menus
- **CRITICAL FIX**: Strips Drupal CSS classes
- Creates clean WordPress-compatible HTML
- Can update existing pages safely

**What It Fixes**:
- ✅ Removes all Drupal CSS classes
- ✅ Removes inline styles
- ✅ Removes wrapper divs
- ✅ Creates semantic HTML
- ✅ Prevents CSS breaking after import

---

## 📋 Detailed Installation Guide

### Prerequisites
- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Admin access to WordPress

### Installation Steps

#### Step 1: Install Fixed Theme (v1.0.1)

**⚠️ CRITICAL**: Use v1.0.1, NOT v1.0.0

1. **Download** `packages/premierplug-theme-v1.0.1.zip`
2. **Login** to WordPress Admin
3. **Navigate** to Appearance → Themes
4. **Click** "Add New" → "Upload Theme"
5. **Choose** the ZIP file
6. **Click** "Install Now"
7. **Activate** the theme
8. **Clear** browser cache (Ctrl+Shift+R on Windows, Cmd+Shift+R on Mac)

**Verify**:
- Visit your site
- Should see fonts loading properly (no square boxes)
- Colors should match brand (#BC1F2F red)
- Layout should look professional

#### Step 2: Install Plugin (v1.2.0)

1. **Download** `packages/premierplug-talent-management-v1.2.0.zip`
2. **Navigate** to Plugins → Add New
3. **Click** "Upload Plugin"
4. **Choose** the ZIP file
5. **Click** "Install Now"
6. **Activate** the plugin

**Verify**:
- Should see "Talent" menu in WordPress Admin
- Should see "Articles" menu
- Should see "Settings" submenu

#### Step 3: Import Content (FIXED VERSION)

**⚠️ CRITICAL**: Use v1.2-FIXED, NOT the old importer

1. **Download** `packages/premierplug-content-importer-v1.2-FIXED.php`
2. **Upload** to WordPress root directory via FTP/SFTP
   - Same folder as `wp-config.php`
   - NOT in wp-content or any subdirectory
3. **Open browser** and visit:
   ```
   https://your-site.com/premierplug-content-importer-v1.2-FIXED.php?key=premierplug_import_2024
   ```
4. **Wait** for import to complete (shows progress bar)
5. **Review** results - should show "HTML: CLEANED" for each page
6. **Clear** browser cache
7. **Delete** the import file for security:
   ```bash
   rm premierplug-content-importer-v1.2-FIXED.php
   ```

**What Gets Imported**:
- 29 pages with clean HTML content
- 3-level navigation (Research → For Talents → For Enterprise)
- 26 featured images
- Primary menu (top navigation)
- Footer menu
- Homepage set to "About Us"

**Verify**:
- Visit homepage
- Check navigation menu works
- Test all pages load correctly
- Verify images display
- Check colors, spacing, fonts all correct

---

## 🐛 Troubleshooting

### Problem 1: Square Boxes Instead of Text

**Cause**: Theme v1.0.0 installed (wrong CSS) OR browser cache

**Solution**:
```
1. Verify theme version: WordPress Admin → Appearance → Themes
   - Should say "PremierPlug Theme 1.0.1"
   - If says 1.0.0, install v1.0.1
2. Hard refresh browser: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
3. Clear WordPress cache plugin if using one
4. Check browser console (F12) for CSS loading errors
```

### Problem 2: CSS Breaks After Content Import

**Cause**: Used old importer (imports Drupal CSS classes)

**Solution**:
```
1. Run FIXED importer: premierplug-content-importer-v1.2-FIXED.php?key=premierplug_import_2024
2. It will UPDATE existing pages with clean HTML
3. Clear browser cache
4. Site should be fixed
```

**Why This Happens**:
- Old HTML files from Drupal site contain Drupal CSS classes
- Old importer keeps these classes: `class="role--anonymous layout-container"`
- WordPress theme doesn't have CSS for Drupal classes
- Result: Complete CSS breakdown

**How FIXED Version Works**:
- Strips ALL CSS classes from imported content
- Removes inline styles
- Removes wrapper divs
- Creates clean HTML: `<div class="entry-content"><h1>Title</h1><p>Content</p></div>`
- WordPress theme CSS now works properly

### Problem 3: Fonts Not Loading

**Cause**: CSS files not loading or wrong paths

**Solution**:
```
1. Open browser DevTools (F12) → Network tab
2. Reload page
3. Look for CSS files:
   - main-design-system.css (633KB) ← Most important
   - system-ui.css (33KB)
   - layout.css (16KB)
4. If any show 404 errors, reinstall theme v1.0.1
5. If all load but fonts still broken, clear browser cache
```

### Problem 4: Navigation Menu Not Working

**Cause**: Menu locations not assigned

**Solution**:
```
1. WordPress Admin → Appearance → Menus
2. Should see "Primary Navigation" and "Footer Navigation"
3. Check "Display location" checkboxes:
   - Primary Navigation → "Primary"
   - Footer Navigation → "Footer"
4. Click "Save Menu"
```

### Problem 5: Pages Empty After Import

**Cause**: Import file path wrong or HTML files missing

**Solution**:
```
1. Verify import file uploaded to WordPress root (same folder as wp-config.php)
2. Check archive/old-site/archive/ folder exists with HTML files
3. Re-run import
```

### Problem 6: Images Not Displaying

**Cause**: Image paths incorrect or files missing

**Solution**:
```
1. Verify premierplug-theme/assets/images/ folder has all images
2. Check WordPress Media Library for uploaded images
3. If missing, re-run import (uploads images to Media Library)
```

---

## 🔧 Advanced Configuration

### Customizing Colors

Edit theme CSS in `premierplug-theme/assets/css/main-design-system.css`:

```css
/* Brand Colors */
--brand-red: #BC1F2F;      /* Primary brand color */
--brand-black: #000000;     /* Text color */
--brand-white: #FFFFFF;     /* Background color */
--brand-gray: #F5F5F5;      /* Light backgrounds */
```

### Adding Custom Menus

```
1. WordPress Admin → Appearance → Menus
2. Create new menu
3. Add pages/posts
4. Assign to location (Primary or Footer)
5. Save
```

### Shortcodes

Display talent profiles:
```
[premierplug_talent_list]
[premierplug_talent_search]
[premierplug_talent_grid columns="3"]
```

Display articles:
```
[premierplug_articles limit="5"]
[premierplug_articles type="press-release"]
[premierplug_article_grid]
```

Display related content:
```
[premierplug_related_articles]
[premierplug_talent_articles talent_id="123"]
```

---

## 📁 Project Structure

```
premierplug/
├── README.md                                    (This file - ONLY documentation)
│
├── packages/                                     (Ready to install)
│   ├── premierplug-theme-v1.0.1.zip            (✅ Use this - CSS fixed)
│   ├── premierplug-talent-management-v1.2.0.zip (✅ Latest version)
│   └── premierplug-content-importer-v1.2-FIXED.php (✅ Use this - strips Drupal classes)
│
├── premierplug-theme/                           (Theme source code)
│   ├── style.css                                (Theme header)
│   ├── functions.php                            (Theme functions - loads all CSS)
│   ├── header.php                               (Site header)
│   ├── footer.php                               (Site footer)
│   ├── index.php                                (Default template)
│   ├── page.php                                 (Page template)
│   ├── screenshot.png                           (Theme thumbnail)
│   ├── template-parts/
│   │   └── navigation-overlay.php               (Mobile menu)
│   └── assets/
│       ├── css/
│       │   ├── main-design-system.css          (633KB - Main CSS)
│       │   ├── system-ui.css                   (33KB - UI components)
│       │   ├── layout.css                      (16KB - Layout system)
│       │   ├── navigation-dropdown-fix.css     (2.7KB - Menu fix)
│       │   └── print.css                       (16KB - Print styles)
│       ├── js/
│       │   ├── main.js                         (Navigation logic)
│       │   ├── navigation-dropdown-fix.js      (Menu fixes)
│       │   └── vendor.js                       (Third-party libraries)
│       └── images/                             (26 images, 4.2MB total)
│
├── premierplug-talent-management/               (Plugin source code)
│   ├── premierplug-talent-management.php       (Main plugin file)
│   ├── README.txt                              (Plugin readme)
│   ├── admin/                                  (Admin functionality)
│   │   ├── class-admin.php                     (Admin interface)
│   │   ├── class-articles-manager.php          (Article management)
│   │   ├── class-custom-post-type-manager.php  (Dynamic post types)
│   │   └── class-settings.php                  (Settings page)
│   ├── includes/                               (Core functionality)
│   │   ├── class-post-type.php                 (Talent post type)
│   │   ├── class-taxonomies.php                (Categories/tags)
│   │   ├── class-metaboxes.php                 (Custom fields)
│   │   ├── class-shortcodes.php                (Display shortcodes)
│   │   ├── class-article-post-types.php        (Article types)
│   │   ├── class-article-metaboxes.php         (Article fields)
│   │   ├── class-article-relationships.php     (Talent-article links)
│   │   ├── class-article-queries.php           (Query logic)
│   │   ├── class-article-shortcodes.php        (Article display)
│   │   ├── class-related-articles.php          (Related content)
│   │   ├── class-seo-manager.php               (SEO/Schema)
│   │   ├── class-analytics.php                 (Google Analytics)
│   │   ├── class-social-sharing.php            (Social buttons)
│   │   ├── class-ad-manager.php                (Ad placements)
│   │   ├── class-email-capture.php             (Email forms)
│   │   └── class-speed-optimizer.php           (Performance)
│   ├── public/                                 (Frontend)
│   │   └── class-public.php                    (Public interface)
│   ├── templates/                              (Display templates)
│   │   ├── single-talent.php                   (Talent profile)
│   │   ├── archive-talent.php                  (Talent listing)
│   │   ├── talent-card.php                     (Talent card)
│   │   ├── single-article.php                  (Article detail)
│   │   ├── archive-articles.php                (Article listing)
│   │   └── article-card.php                    (Article card)
│   └── assets/                                 (Plugin assets)
│       ├── css/                                (Plugin styles)
│       └── js/                                 (Plugin scripts)
│
├── archive/                                     (Historical files - archived)
│   ├── old-docs/                               (Old documentation)
│   ├── old-site/                               (Original HTML site)
│   │   ├── archive/                            (HTML files for import)
│   │   └── images/                             (Original images)
│   └── backup/                                 (Archived versions)
│
└── docs/                                        (Developer documentation)
    ├── DYNAMIC-POST-TYPES-GUIDE.md             (Custom post types)
    ├── GROWTH-FEATURES.md                      (Growth features guide)
    └── README.md                               (Docs index)
```

---

## 🎯 Features Overview

### Talent Management
- Custom post type for talent profiles
- Featured image support
- Custom fields (bio, contact, social media)
- Categories and tags
- Search and filtering
- Multiple display layouts

### Article System
5 article types built-in:
1. **Press Releases** - Official company announcements
2. **Blog Posts** - Informal content and insights
3. **Awards** - Recognition and achievements
4. **News** - Industry news and updates
5. **Media Coverage** - External press mentions

Each article type has:
- Author attribution
- Publication date
- Featured image
- Categories and tags
- Talent relationships
- SEO metadata

### SEO Features
- Automatic Schema.org markup (Person, Organization, Article)
- Meta title and description
- Open Graph tags (Facebook/LinkedIn)
- Twitter Cards
- Canonical URLs
- XML sitemap support

### Social Sharing
Built-in sharing for:
- Facebook
- Twitter/X
- LinkedIn
- Pinterest
- Email
- WhatsApp

### Ad Management
5 placement zones:
- Header leaderboard (728x90)
- Sidebar (300x250)
- In-content (responsive)
- Footer
- Custom locations

### Analytics
- Google Analytics 4 integration
- Event tracking
- Page views
- User interactions
- Custom dimensions

### Email Capture
- Inline forms
- Popup forms
- Exit-intent detection
- Newsletter integration
- Custom styling

### Performance
- Asset minification
- Lazy loading images
- Browser caching headers
- Database query optimization
- CDN support

---

## ⚠️ Important Notes

### DO NOT Use These Files (Outdated)
- ❌ `premierplug-theme-v1.0.0.zip` - Has wrong CSS (SAML plugin code)
- ❌ `premierplug-content-importer.php` - Imports Drupal classes (breaks CSS)

### USE These Files (Current)
- ✅ `premierplug-theme-v1.0.1.zip` - Correct CSS (717KB from original site)
- ✅ `premierplug-talent-management-v1.2.0.zip` - Full feature set
- ✅ `premierplug-content-importer-v1.2-FIXED.php` - Clean HTML import

### Installation Order Matters
1. **First**: Install theme v1.0.1 (provides CSS)
2. **Second**: Install plugin v1.2.0 (adds functionality)
3. **Third**: Run FIXED importer (imports clean content)

### Re-running Importer is Safe
The FIXED importer can be run multiple times:
- Updates existing pages with clean HTML
- Doesn't create duplicates
- Fixes pages that were imported with old version
- Safe to use for cleanup

---

## 🔍 Technical Details

### Why Two Fixes Were Needed

**Problem 1: Theme CSS**
- Original theme v1.0.0 had 668KB of SAML plugin CSS
- Wrong CSS = fonts don't load, layout broken, wrong colors
- **Solution**: Created v1.0.1 with correct 717KB CSS from original site

**Problem 2: Content Import**
- Original HTML files contain Drupal CMS structure
- Old importer kept all Drupal CSS classes: `class="role--anonymous layout-container"`
- WordPress theme CSS has no rules for Drupal classes
- Result: Complete CSS breakdown after import
- **Solution**: Created FIXED importer that strips all Drupal classes

**Together**: Theme provides correct CSS + Importer creates compatible HTML = Perfect site

### What Gets Cleaned by FIXED Importer

**Removed Attributes**:
- All `class` attributes (except WordPress standard `.entry-content`)
- All `id` attributes
- All `style` attributes (inline CSS)
- All `data-*` attributes (Drupal JS hooks)
- Most `aria-*` attributes (Drupal navigation)
- All `role` attributes (Drupal roles)

**Removed Elements**:
- `<script>` tags (Drupal JS)
- `<style>` tags (embedded Drupal CSS)
- `<nav>` elements (Drupal navigation)
- `<header>` elements (Drupal header)
- `<footer>` elements (Drupal footer)
- `<svg>` elements (Drupal logos)
- Empty `<div>` and `<span>` tags

**HTML Transformation**:

Before (Drupal):
```html
<div class="layout-container role--anonymous">
  <div class="site-wrapper">
    <div class="main-content">
      <div class="region region-content">
        <h1 class="page-title">About Us</h1>
        <p class="lead-text body-text">We are...</p>
      </div>
    </div>
  </div>
</div>
```

After (WordPress):
```html
<div class="entry-content">
  <h1>About Us</h1>
  <p>We are...</p>
</div>
```

### CSS Files Explained

**main-design-system.css** (633KB)
- Original site's complete design system
- All fonts, colors, spacing, layout
- Drupal-specific CSS already stripped
- WordPress-compatible selectors

**system-ui.css** (33KB)
- UI components (buttons, forms, cards)
- Utility classes
- Responsive helpers

**layout.css** (16KB)
- Grid system
- Container widths
- Flexbox layouts
- Responsive breakpoints

**navigation-dropdown-fix.css** (2.7KB)
- 3-level menu support
- Dropdown positioning
- Mobile menu fixes
- Hover/focus states

**print.css** (16KB)
- Print-friendly layout
- Hidden navigation
- Optimized for paper

---

## 📊 Success Metrics

Your installation is successful when you see:

### Visual Check
- ✅ Professional fonts (pf_dintext_pro, Helvetica Neue)
- ✅ Brand colors (#BC1F2F red, #000000 black)
- ✅ Proper spacing and padding
- ✅ Clean layout on all devices
- ✅ Working 3-level navigation
- ✅ Images display correctly
- ✅ No square boxes or broken text
- ✅ Footer displays properly

### Technical Check (Browser DevTools F12)
- ✅ Console: No errors
- ✅ Network: All CSS files load (200 status)
  - main-design-system.css (633KB)
  - system-ui.css (33KB)
  - layout.css (16KB)
  - navigation-dropdown-fix.css (2.7KB)
  - print.css (16KB)
- ✅ Elements: Content wrapped in `<div class="entry-content">`
- ✅ Elements: NO Drupal classes (no `role--anonymous`, `layout-container`, etc.)

### Functional Check
- ✅ All 29 pages load correctly
- ✅ Navigation menu works (3 levels)
- ✅ Footer menu works
- ✅ Search works
- ✅ Images load fast
- ✅ Mobile responsive
- ✅ Forms work (if Contact Form 7 installed)

---

## 🚢 Deployment Checklist

### Pre-Launch
- [ ] Theme v1.0.1 installed and activated
- [ ] Plugin v1.2.0 installed and activated
- [ ] Content imported with FIXED importer
- [ ] All pages reviewed for content accuracy
- [ ] All images displaying correctly
- [ ] Navigation menus working (primary + footer)
- [ ] Search functionality tested
- [ ] Mobile responsive tested
- [ ] Browser compatibility tested (Chrome, Firefox, Safari, Edge)

### Post-Launch
- [ ] Delete import file (security)
- [ ] Set up backups
- [ ] Configure caching (if using cache plugin)
- [ ] Set up SSL certificate (HTTPS)
- [ ] Submit sitemap to Google Search Console
- [ ] Configure Google Analytics (if desired)
- [ ] Test contact forms
- [ ] Monitor site speed
- [ ] Check broken links

### Recommended Plugins
- **Contact Form 7** - For contact page forms
- **Yoast SEO** or **Rank Math** - Additional SEO features
- **WP Super Cache** or **W3 Total Cache** - Performance
- **Wordfence** or **Sucuri** - Security
- **UpdraftPlus** - Backups

---

## 📞 Support & Resources

### Documentation
- This README (complete guide)
- Plugin readme: `premierplug-talent-management/README.txt`
- Developer docs: `docs/` folder

### Live Demo
- Production site: https://wp.premierplug.org

### Common Tasks

**Add Talent Profile**:
```
1. WordPress Admin → Talent → Add New
2. Enter name, bio, contact info
3. Set featured image
4. Add categories/tags
5. Publish
```

**Add Article**:
```
1. WordPress Admin → Articles → Add New
2. Select article type (press, blog, awards, news, media)
3. Enter title and content
4. Link to talent profiles
5. Set featured image
6. Publish
```

**Customize Navigation**:
```
1. WordPress Admin → Appearance → Menus
2. Edit "Primary Navigation" or "Footer Navigation"
3. Add/remove items
4. Drag to reorder
5. Save
```

**Change Colors**:
```
1. Edit: premierplug-theme/assets/css/main-design-system.css
2. Find CSS variables (--brand-red, --brand-black, etc.)
3. Change color values
4. Save and clear cache
```

---

## 📝 Version History

**v1.2.0** (Current) - December 21, 2024
- ✅ Theme v1.0.1: Fixed CSS (717KB from original site)
- ✅ Plugin v1.2.0: Full feature set with growth tools
- ✅ Importer v1.2-FIXED: Strips Drupal classes, clean HTML
- ✅ Complete documentation consolidated to README.md
- ✅ All outdated files archived

**v1.1.0** - December 2024
- Theme v1.0.0 (had wrong CSS - archived)
- Plugin v1.2.0 (unchanged)
- Importer v1.1 (imported Drupal classes - archived)

**v1.0.0** - Initial Release
- Basic theme and plugin
- Simple content import

---

## ⚡ Quick Reference

### File Sizes
- Theme v1.0.1: 222KB (contains 717KB CSS)
- Plugin v1.2.0: ~500KB
- Content Importer v1.2-FIXED: 25KB

### Installation Time
- Theme install: 2 minutes
- Plugin install: 1 minute
- Content import: 5 minutes
- **Total: ~10 minutes**

### Key Files
```
packages/
├── premierplug-theme-v1.0.1.zip              ← Install this theme
├── premierplug-talent-management-v1.2.0.zip  ← Install this plugin
└── premierplug-content-importer-v1.2-FIXED.php ← Run this importer
```

### Important URLs
```
Live Site:        https://wp.premierplug.org
Import URL:       /premierplug-content-importer-v1.2-FIXED.php?key=premierplug_import_2024
WordPress Admin:  /wp-admin/
```

### Critical Commands
```bash
# Clear browser cache
Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)

# Delete import file after use
rm premierplug-content-importer-v1.2-FIXED.php

# Check file permissions (if upload fails)
chmod 755 premierplug-theme
chmod 755 premierplug-talent-management
```

---

**Last Updated**: December 21, 2024
**Version**: 1.2.0
**Status**: ✅ Production Ready
**Documentation**: Complete (Single README)
