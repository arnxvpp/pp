# PremierPlug Complete Installation Checklist

**Version:** 1.0.0
**Completion Status:** All 8 Phases Complete
**Ready for:** Production Deployment

---

## Pre-Installation Requirements

### Server Requirements
- [ ] WordPress 6.0 or higher installed
- [ ] PHP 7.4 or higher
- [ ] MySQL 5.7+ or MariaDB 10.3+
- [ ] HTTPS/SSL certificate installed
- [ ] Minimum 512MB RAM
- [ ] 1GB+ disk space available

### Access Requirements
- [ ] WordPress admin access
- [ ] FTP or file manager access
- [ ] Database access (phpMyAdmin or CLI)
- [ ] Supabase account (free tier works)
- [ ] Domain DNS configured

---

## Phase 1: Content Import (30 minutes)

### Step 1: Backup Current Site
```bash
# Backup database
mysqldump -u username -p database_name > backup-$(date +%Y%m%d).sql

# Backup files
tar -czf backup-files-$(date +%Y%m%d).tar.gz /path/to/wordpress
```
- [ ] Database backed up
- [ ] Files backed up
- [ ] Backups stored safely

### Step 2: Import SQL File
```bash
cd extraction-output/
mysql -u username -p database_name < premierplug-content-import.sql
```
- [ ] SQL file imported successfully
- [ ] No errors during import

### Step 3: Verify Content
- [ ] 25 posts/pages visible in WordPress admin
- [ ] 5 categories created
- [ ] No database errors

### Step 4: Upload Images
- [ ] Go to Media → Add New
- [ ] Upload all 24 files from `extracted-images/`
- [ ] All images uploaded successfully
- [ ] Note media IDs for featured images

### Step 5: Assign Featured Images
- [ ] About Us → about-us.jpeg
- [ ] Brand Consulting → brand-consulting.jpeg
- [ ] Brand Management → brand-management.jpeg
- [ ] (Continue for all 25 pages)

**Time:** 30 minutes
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 2: Theme Installation (20 minutes)

### Step 1: Prepare Theme Package
```bash
cd premierplug-theme/
zip -r premierplug-theme.zip *
```
- [ ] Theme zipped successfully

### Step 2: Upload Theme
- [ ] Go to Appearance → Themes
- [ ] Click "Add New" → "Upload Theme"
- [ ] Select premierplug-theme.zip
- [ ] Click "Install Now"
- [ ] Theme uploaded successfully

### Step 3: Activate Theme
- [ ] Click "Activate"
- [ ] Theme activated
- [ ] No errors displayed

### Step 4: Configure Settings
- [ ] Settings → Permalinks → Select "Post name" → Save
- [ ] Settings → Reading → Static page → Select "Home" → Save
- [ ] Appearance → Customize → Configure as needed

### Step 5: Create Menus
- [ ] Appearance → Menus → Create "Primary Menu"
- [ ] Add Research submenu (Social, Market, Data)
- [ ] Add For Talents submenu (Motion, Digital, Speakers, TV, Voiceovers)
- [ ] Add For Enterprise submenu (Brand services)
- [ ] Assign to "Primary Menu" location
- [ ] Create Footer Menu
- [ ] Add About, Careers, Contact, Privacy, Terms

### Step 6: Assign Templates
Edit each page and assign templates:
- [ ] Social Research → Template: Research Services
- [ ] Market Research → Template: Research Services
- [ ] Data Analysis → Template: Research Services
- [ ] Motion Pictures → Template: Talent Services
- [ ] Digital Media → Template: Talent Services
- [ ] Speakers → Template: Talent Services
- [ ] Television → Template: Talent Services
- [ ] Voiceovers → Template: Talent Services
- [ ] Brand Consulting → Template: Enterprise Solutions
- [ ] Brand Management → Template: Enterprise Solutions
- [ ] Brand Studio → Template: Enterprise Solutions
- [ ] About Us → Template: About Page
- [ ] Contact → Template: Contact Page

**Time:** 20 minutes
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 3: Plugin Installation (30 minutes)

### Talent Manager Plugin

#### Step 1: Install Plugin
- [ ] Upload plugin folder to `wp-content/plugins/`
- [ ] OR zip and upload via Plugins → Add New
- [ ] Activate plugin

#### Step 2: Configure Supabase
Add to `wp-config.php`:
```php
define('SUPABASE_URL', 'your_supabase_project_url');
define('SUPABASE_ANON_KEY', 'your_supabase_anon_key');
define('SUPABASE_SERVICE_KEY', 'your_supabase_service_key');
```
- [ ] Supabase credentials added
- [ ] Credentials tested (check plugin settings)

#### Step 3: Run Database Migration
- [ ] Open Supabase SQL Editor
- [ ] Copy content from `supabase/migrations/20251016201452_create_talent_management_schema.sql`
- [ ] Execute migration
- [ ] Verify 9 tables created
- [ ] Check RLS policies enabled

#### Step 4: Test Plugin
- [ ] Create test talent profile
- [ ] Check Supabase sync
- [ ] Verify data synced correctly
- [ ] Delete test talent

**Time:** 15 minutes
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

### Essential Plugins

#### Rank Math SEO
- [ ] Install from WordPress.org
- [ ] Activate plugin
- [ ] Run Setup Wizard
- [ ] Connect Google Search Console
- [ ] Import config from `/config/rank-math-seo-config.json`

#### W3 Total Cache
- [ ] Install from WordPress.org
- [ ] Activate plugin
- [ ] Enable Page Cache
- [ ] Enable Browser Cache
- [ ] Enable Database Cache
- [ ] Test cache working

#### Smush Image Optimization
- [ ] Install from WordPress.org
- [ ] Activate plugin
- [ ] Run bulk optimization on existing images
- [ ] Enable auto-optimize for new uploads

#### Contact Form 7
- [ ] Install from WordPress.org
- [ ] Activate plugin
- [ ] Create main contact form
- [ ] Add shortcode to Contact page template

#### Wordfence Security
- [ ] Install from WordPress.org
- [ ] Activate plugin
- [ ] Run initial scan
- [ ] Configure firewall rules
- [ ] Enable two-factor authentication

#### UpdraftPlus Backup
- [ ] Install from WordPress.org
- [ ] Activate plugin
- [ ] Configure backup destination (Google Drive/Dropbox)
- [ ] Schedule daily backups
- [ ] Test backup/restore

**Time:** 45 minutes (all plugins)
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 4: SEO Configuration (1 hour)

### Step 1: Submit Sitemap
- [ ] Go to Google Search Console
- [ ] Add property: your-domain.com
- [ ] Verify ownership
- [ ] Submit sitemap: your-domain.com/sitemap_index.xml
- [ ] Submit to Bing Webmaster Tools

### Step 2: Optimize Meta Tags
For each page, set:
- [ ] Unique title (60 characters max)
- [ ] Unique meta description (155 characters max)
- [ ] Focus keyword
- [ ] Review in Rank Math
- [ ] Score 80+ (green)

### Step 3: Add Schema Markup
- [ ] Organization schema on homepage
- [ ] LocalBusiness schema on contact page
- [ ] Service schema on service pages
- [ ] Person schema on talent profiles
- [ ] Test with Google Rich Results Test

### Step 4: Create Location Pages (Optional)
- [ ] Create Mumbai page
- [ ] Create Delhi page
- [ ] Create Bangalore page
- [ ] Optimize for local keywords

**Time:** 1 hour
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 5: Performance Optimization (30 minutes)

### Step 1: Enable Caching
- [ ] W3 Total Cache enabled and configured
- [ ] Browser caching enabled
- [ ] Database caching enabled
- [ ] Object caching (if Redis/Memcached available)

### Step 2: Optimize Images
- [ ] Run Smush bulk optimization
- [ ] Verify lazy loading working
- [ ] Check image dimensions appropriate

### Step 3: Minification
- [ ] Enable CSS minification
- [ ] Enable JS minification
- [ ] Enable HTML minification
- [ ] Test site after each

### Step 4: CDN Setup (Optional)
- [ ] Sign up for Cloudflare (free)
- [ ] Add domain to Cloudflare
- [ ] Update DNS to Cloudflare nameservers
- [ ] Enable Cloudflare caching
- [ ] Test CDN working

**Time:** 30 minutes
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 6: Security Hardening (30 minutes)

### Step 1: SSL Configuration
- [ ] Force HTTPS in WordPress settings
- [ ] Add HTTPS redirect in .htaccess
- [ ] Test all pages load via HTTPS
- [ ] Fix mixed content warnings

### Step 2: Wordfence Configuration
- [ ] Run full scan
- [ ] Fix all critical issues
- [ ] Configure firewall (Learning Mode → Enabled)
- [ ] Enable two-factor authentication
- [ ] Configure email alerts

### Step 3: File Permissions
```bash
# Set correct permissions
find /path/to/wordpress -type d -exec chmod 755 {} \;
find /path/to/wordpress -type f -exec chmod 644 {} \;
chmod 600 wp-config.php
```
- [ ] Permissions set correctly
- [ ] wp-config.php protected

### Step 4: Hide WordPress Version
Add to functions.php:
```php
remove_action('wp_head', 'wp_generator');
```
- [ ] WordPress version hidden
- [ ] Test with security scanner

**Time:** 30 minutes
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 7: Testing (1-2 hours)

### Automated Testing
```bash
cd testing/
chmod +x automated-tests.sh
SITE_URL=https://your-domain.com ./automated-tests.sh
```
- [ ] All automated tests passing
- [ ] Fix any failures

### Manual Testing

#### Functionality Tests
- [ ] All 25 pages load without errors
- [ ] Navigation menu works (all links)
- [ ] Search functionality works
- [ ] Contact form submits successfully
- [ ] Talent inquiry form works (if using)

#### Visual Tests
- [ ] Homepage displays correctly
- [ ] Hero images load properly
- [ ] All images have correct aspect ratios
- [ ] No layout breaks
- [ ] Fonts loading correctly (no FOUT)

#### Mobile Tests
- [ ] Test on iPhone (Safari)
- [ ] Test on Android (Chrome)
- [ ] Test on tablet (iPad)
- [ ] Navigation menu works on mobile
- [ ] Forms usable on mobile

#### Browser Tests
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

#### Performance Tests
- [ ] Run PageSpeed Insights (target: 90+ desktop, 85+ mobile)
- [ ] Run GTmetrix (target: A grade)
- [ ] Check Core Web Vitals
- [ ] Load time < 3 seconds

**Time:** 1-2 hours
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Phase 8: Go Live (30 minutes)

### Pre-Launch Checklist
- [ ] All content reviewed and proofread
- [ ] No lorem ipsum or placeholder text
- [ ] All links working (no 404s)
- [ ] Forms tested and working
- [ ] Email notifications configured
- [ ] SSL certificate valid
- [ ] Analytics installed (Google Analytics)
- [ ] Search Console configured
- [ ] Backups configured and tested
- [ ] Security scan passed
- [ ] Performance targets met

### DNS Configuration
- [ ] Point domain to hosting
- [ ] Wait for DNS propagation (24-48 hours)
- [ ] Verify site accessible via domain
- [ ] Verify www and non-www working
- [ ] HTTPS redirect working

### Post-Launch Monitoring (First 7 Days)
- [ ] Monitor uptime (use UptimeRobot)
- [ ] Check error logs daily
- [ ] Monitor search console for errors
- [ ] Track initial traffic
- [ ] Respond to any issues immediately

**Time:** 30 minutes + 24-48 hours DNS
**Status:** ☐ Not Started | ☐ In Progress | ☐ Complete

---

## Post-Installation Tasks (Week 1)

### Content
- [ ] Write first blog post
- [ ] Add company photos to About page
- [ ] Add team member bios
- [ ] Create case studies (3-5)

### Marketing
- [ ] Announce launch on social media
- [ ] Send email to existing contacts
- [ ] Submit to business directories
- [ ] Create Google Business Profile

### Optimization
- [ ] Review analytics data
- [ ] Check search console for issues
- [ ] Monitor page speed daily
- [ ] Fix any emerging issues

---

## Support Resources

### Documentation
- `/FINAL-DELIVERY-SUMMARY.md` - Complete overview
- `/COMPLETE-IMPLEMENTATION-GUIDE.md` - Detailed guide
- `/extraction-output/IMPORT-INSTRUCTIONS.md` - Content import
- `/database/DATABASE-ARCHITECTURE.md` - Database docs
- `/seo/seo-implementation-checklist.md` - SEO guide
- `/testing/automated-tests.sh` - Testing script

### Quick Help
- Content issues: See `/extraction-output/`
- Theme issues: See `/premierplug-theme/`
- Plugin issues: See `/wp-content/plugins/premierplug-talent-manager/`
- SEO help: See `/seo/`
- Testing: Run `/testing/automated-tests.sh`

---

## Time Summary

| Phase | Time Estimate | Priority |
|-------|--------------|----------|
| Phase 1: Content Import | 30 min | Critical |
| Phase 2: Theme Install | 20 min | Critical |
| Phase 3: Plugins | 30 min | High |
| Phase 4: SEO Config | 1 hour | High |
| Phase 5: Performance | 30 min | High |
| Phase 6: Security | 30 min | Critical |
| Phase 7: Testing | 1-2 hours | Critical |
| Phase 8: Go Live | 30 min | Critical |
| **TOTAL** | **5-6 hours** | |

**Plus DNS propagation: 24-48 hours**

---

## Success Criteria

✅ **All 8 phases completed**
✅ **All automated tests passing**
✅ **Performance score 90+ (desktop)**
✅ **Security scan passed**
✅ **All functionality tested**
✅ **Site accessible via HTTPS**
✅ **Backups configured**
✅ **Analytics tracking**

---

**Status:** Ready for Production Deployment ✅
**Last Updated:** 2025-10-17
**Version:** 1.0.0
