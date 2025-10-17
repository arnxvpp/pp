# Phase 1 Verification Checklist

## Pre-Import Verification

### File Integrity Check

- [ ] `premierplug-content-import.sql` exists (824 KB)
- [ ] `extracted-images/` folder contains 24 image files
- [ ] `image-mapping.csv` exists (6.5 KB)
- [ ] `category-structure.json` exists (1.6 KB)
- [ ] `content-extraction-report.md` exists (3.3 KB)
- [ ] `extraction-log.txt` exists (9.5 KB)
- [ ] `IMPORT-INSTRUCTIONS.md` exists (6.2 KB)

### Content Verification

- [ ] SQL file opens without errors
- [ ] SQL file starts with `SET SQL_MODE`
- [ ] SQL file contains 5 category definitions
- [ ] SQL file contains 25 post/page definitions
- [ ] SQL file ends with `COMMIT;`
- [ ] All image files are valid JPEG format
- [ ] CSV file has proper headers and data
- [ ] JSON file is valid and parseable

## WordPress Import Checklist

### Pre-Import

- [ ] WordPress installed (version 5.0+)
- [ ] Database backed up
- [ ] phpMyAdmin or MySQL CLI access confirmed
- [ ] Fresh WordPress installation (recommended)
- [ ] Write permissions verified for uploads folder

### During Import

- [ ] SQL import completed without errors
- [ ] No duplicate key errors
- [ ] Transaction committed successfully
- [ ] Import log shows success

### Post-Import Verification

#### Database Tables Check

- [ ] `wp_posts` table has 25+ new entries (IDs 100-124)
- [ ] `wp_terms` table has 5 new categories (IDs 50-54)
- [ ] `wp_term_taxonomy` table has 5 new entries
- [ ] `wp_term_relationships` table has 25+ new entries
- [ ] `wp_postmeta` table has entries for each post

#### WordPress Admin Check

**Posts:**
- [ ] Navigate to Posts → All Posts
- [ ] Count shows 14 posts
- [ ] Posts are assigned to correct categories:
  - [ ] Enterprise Solutions: 6 posts
  - [ ] Research Services: 3 posts
  - [ ] Talent Services: 5 posts

**Pages:**
- [ ] Navigate to Pages → All Pages
- [ ] Count shows 11 pages
- [ ] All pages listed:
  - [ ] About Us
  - [ ] Careers
  - [ ] Client Privacy Notice
  - [ ] Contact Us
  - [ ] Entry Level Opportunities
  - [ ] Human Rights
  - [ ] Internships
  - [ ] Privacy Policy
  - [ ] Social Responsibility
  - [ ] Terms of Use
  - [ ] Home

**Categories:**
- [ ] Navigate to Posts → Categories
- [ ] 5 categories visible:
  - [ ] Company Information (10 posts)
  - [ ] Enterprise Solutions (6 posts)
  - [ ] Research Services (3 posts)
  - [ ] Talent Services (5 posts)
  - [ ] Home (1 post)

#### Frontend Check

- [ ] Visit homepage (should load)
- [ ] Click on any post (should display)
- [ ] Click on any page (should display)
- [ ] Check category archives (should list posts)
- [ ] No 404 errors
- [ ] Content displays (even if unstyled)

## Image Upload Checklist

### Media Library Upload

- [ ] Navigate to Media → Add New
- [ ] Upload all 24 images from `extracted-images/`
- [ ] All uploads successful
- [ ] Note media IDs for each image
- [ ] Images display correctly in library

### Featured Image Assignment

Manually assign featured images to posts:

**Company Information:**
- [ ] About Us → about-us.jpeg
- [ ] Careers → career.jpeg
- [ ] Client Privacy Notice → client-privacy-notice.jpeg
- [ ] Contact Us → contact-us.jpeg
- [ ] Entry Level Opportunities → entry-level-opportunities.jpeg
- [ ] Human Rights → human-rights.jpeg
- [ ] Internships → internship.jpeg
- [ ] Privacy Policy → client-privacy-notice.jpeg
- [ ] Social Responsibility → social-responsibility.jpeg
- [ ] Terms of Use → client-privacy-notice.jpeg

**Enterprise Solutions:**
- [ ] Brand Consulting → brand-consulting.jpeg
- [ ] Brand Studio → brand-studio.jpeg (+ 4 product images)
- [ ] Brand Management → brand-management.jpeg
- [ ] Marketing & IT → digital-media-roaster.jpeg
- [ ] Music Brand Partnerships → music-brand-partnership.jpeg
- [ ] Publishing → publishing.jpeg

**Research Services:**
- [ ] Data Analysis → data-analysis.jpeg
- [ ] Market Research → market-research.jpeg
- [ ] Social Research → social-research.jpeg

**Talent Services:**
- [ ] Digital Media → digital-media-roaster.jpeg
- [ ] Motion Pictures → motion-picture.jpeg
- [ ] Speakers → speakers.jpeg
- [ ] Television → motion-picture.jpeg
- [ ] Voiceovers → voiceover.jpeg

## Post-Import Configuration

### Settings

**Reading Settings:**
- [ ] Settings → Reading
- [ ] Set static page as homepage
- [ ] Select "Home" page as Front page
- [ ] Save changes

**Permalink Settings:**
- [ ] Settings → Permalinks
- [ ] Select "Post name" structure
- [ ] Save changes
- [ ] Test: visit a post, URL should be `/post-name/`

**Discussion Settings:**
- [ ] Settings → Discussion
- [ ] Configure comment settings as needed
- [ ] Save changes

### Menus

- [ ] Appearance → Menus
- [ ] Create Primary Menu
- [ ] Add pages:
  - [ ] Home
  - [ ] About Us
  - [ ] Careers
  - [ ] Contact Us
- [ ] Create Research submenu
- [ ] Create For Talents submenu
- [ ] Create For Enterprise submenu
- [ ] Assign menu to location
- [ ] Save menu

## Quality Assurance Testing

### Content Testing

- [ ] All posts display correctly
- [ ] All pages display correctly
- [ ] Headings render properly (H1-H6)
- [ ] Paragraphs have spacing
- [ ] Lists display correctly (ul, ol)
- [ ] No HTML tags visible in content
- [ ] Images display (if assigned)
- [ ] No broken HTML

### Functionality Testing

- [ ] Search works (if enabled)
- [ ] Categories filter correctly
- [ ] Archive pages work
- [ ] Pagination works (if applicable)
- [ ] Comments work (if enabled)
- [ ] Forms submit (if any)
- [ ] No JavaScript errors in console

### Performance Testing

- [ ] Pages load in < 3 seconds
- [ ] Images load properly
- [ ] No 500 errors
- [ ] No database errors
- [ ] Server resources normal

### Mobile Testing

- [ ] Test on mobile device or emulator
- [ ] Content readable
- [ ] Navigation works
- [ ] Images display
- [ ] Forms usable

## Known Issues to Address

### Low Content Pages (3)

- [ ] Fix Digital Media page (currently 11 words)
- [ ] Fix Marketing & IT page (currently 11 words)
- [ ] Fix Homepage (currently 11 words)

**Action:** Add proper content manually in WordPress editor

### Missing Images (6)

- [ ] Source 2 images for Human Rights page
- [ ] Source 4 images for Music Brand Partnerships page

**Action:** Download from original site or source separately

## Completion Criteria

### Must Have (Before Going Live)

- [ ] All posts/pages imported successfully
- [ ] All categories created
- [ ] Featured images assigned
- [ ] Menus configured
- [ ] Homepage set correctly
- [ ] Permalinks configured
- [ ] No critical errors
- [ ] Content reviewed for accuracy

### Should Have (Before Going Live)

- [ ] Low content pages fixed
- [ ] Missing images sourced and uploaded
- [ ] Contact forms working
- [ ] SEO plugin installed and configured
- [ ] Analytics configured
- [ ] Sitemap generated

### Nice to Have (Can do later)

- [ ] Custom theme applied
- [ ] Performance optimization
- [ ] Security hardening
- [ ] Backup system configured
- [ ] SSL certificate installed
- [ ] CDN configured

## Sign-Off

### Testing Completed By

- **Name:** _________________
- **Date:** _________________
- **Time Spent:** _________________

### Issues Found

| Issue | Severity | Status | Notes |
|-------|----------|--------|-------|
|       |          |        |       |
|       |          |        |       |
|       |          |        |       |

### Final Status

- [ ] Ready for Production
- [ ] Ready for Staging
- [ ] Needs Additional Work

### Notes

_________________________________________
_________________________________________
_________________________________________
_________________________________________

---

**Remember:** Always keep backups before making changes!

**Support:** Refer to IMPORT-INSTRUCTIONS.md for detailed guidance

**Generated:** 2025-10-17
