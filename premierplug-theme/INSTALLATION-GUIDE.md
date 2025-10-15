# PremierPlug WordPress Theme - Installation & Setup Guide

## Prerequisites

- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher

## Installation Steps

### 1. Upload Theme

**Option A: Via WordPress Admin**
1. Log into your WordPress admin dashboard
2. Navigate to `Appearance > Themes`
3. Click `Add New` then `Upload Theme`
4. Choose the `premierplug-theme.zip` file
5. Click `Install Now`
6. Click `Activate` once installation completes

**Option B: Via FTP/File Manager**
1. Upload the `premierplug-theme` folder to `/wp-content/themes/`
2. Log into WordPress admin
3. Navigate to `Appearance > Themes`
4. Find "PremierPlug" and click `Activate`

### 2. Configure Basic Settings

#### Set Permalinks
1. Go to `Settings > Permalinks`
2. Select "Post name" structure
3. Click `Save Changes`

#### Configure Reading Settings
1. Go to `Settings > Reading`
2. Set "Your homepage displays" to "A static page"
3. Choose your homepage from "Homepage" dropdown
4. Click `Save Changes`

### 3. Create Navigation Menus

#### Create Primary Menu (Overlay Navigation)

1. Go to `Appearance > Menus`
2. Create a new menu called "Primary Menu"
3. Build the following structure:

```
Research (Custom Link: #)
├── Social Research (Page)
├── Market Research (Page)
└── Data Analysis (Page)

For Talents (Custom Link: #)
├── Motion Pictures (Page)
├── Digital Media (Page)
├── Speakers (Page)
├── Television (Page)
└── Voiceovers (Page)

For Enterprise (Custom Link: #)
├── Partnership Sales (Custom Link: #)
│   ├── Music Brand Partnerships (Page)
│   ├── Publishing (Page)
│   ├── Licensing (Page)
│   ├── Music & Comedy Touring (Page)
│   └── Merchandising (Page)
└── Brand Solutions (Custom Link: #)
    ├── Brand Consulting (Page)
    ├── Brand Management (Page)
    ├── Brand Studio (Page)
    ├── Production Studio (Page)
    └── Marketing & IT (Page)
```

4. Check the box for "Primary Menu (Overlay)" under Menu Settings
5. Click `Save Menu`

#### Create Footer Menu

1. Create a new menu called "Footer Menu"
2. Add the following pages:
   - About
   - Careers
   - Contact
3. Check the box for "Footer Menu" under Menu Settings
4. Click `Save Menu`

### 4. Create Required Pages

Create the following pages with these slugs:

**Research Section:**
- Social Research (slug: `social-research`)
- Market Research (slug: `market-research`)
- Data Analysis (slug: `data-analysis`)

**For Talents Section:**
- Motion Pictures (slug: `motion-pictures`)
- Digital Media (slug: `digital-media`)
- Speakers (slug: `speakers`)
- Television (slug: `television`)
- Voiceovers (slug: `voiceovers`)

**For Enterprise - Partnership Sales:**
- Music Brand Partnerships (slug: `music-brand-partnerships`)
- Publishing (slug: `publishing`)

**For Enterprise - Brand Solutions:**
- Brand Consulting (slug: `brand-consulting`)
- Brand Management (slug: `brandmanagement`)
- Brand Studio (slug: `brand-studio`)
- Marketing & IT (slug: `marketing-it`)

**Other Pages:**
- About Us (slug: `about-us`)
- Careers (slug: `careers`)
- Contact (slug: `contact`)

### 5. Configure Theme Customizer

1. Go to `Appearance > Customize`
2. Navigate to `Homepage Settings`:
   - Enable/Disable Intro Animation (checked by default)
   - Customize Homepage Slogan (default: "Plugged It Premier.")
3. Navigate to `Site Identity`:
   - Upload custom logo (optional - theme includes built-in SVG logo)
   - Set Site Title and Tagline
4. Click `Publish` to save changes

### 6. Set Featured Images

For each service page:
1. Edit the page
2. Set a Featured Image (recommended size: 1920x1080px for hero images)
3. Click `Update`

### 7. Configure Homepage

1. Create or edit your homepage
2. Leave content blank if using the animated intro
3. Or add content that will display when animation is disabled
4. Set this page as your homepage in `Settings > Reading`

## Recommended Plugins

### Essential Plugins

**Contact Form 7** or **WPForms**
- For contact page functionality
- Install: `Plugins > Add New > Search "Contact Form 7"`

**Yoast SEO** or **Rank Math**
- For search engine optimization
- Install: `Plugins > Add New > Search "Yoast SEO"`

### Optional but Recommended

**Advanced Custom Fields (ACF)**
- For additional content customization
- Allows you to add custom fields to pages

**WP Rocket** or **W3 Total Cache**
- For caching and performance optimization

**Wordfence Security**
- For security hardening

**Regenerate Thumbnails**
- To regenerate image thumbnails in proper sizes

## Content Migration from Static Site

### Automated Approach

1. Install the **WP All Import** plugin
2. Export content from HTML pages
3. Import into WordPress with proper mapping

### Manual Approach

1. Open each HTML file from the static site
2. Copy the main content (between `<main>` tags)
3. Create corresponding WordPress page
4. Paste and format content using WordPress editor
5. Add featured image
6. Publish

## Troubleshooting

### Menu Not Displaying
- Ensure you've assigned the menu to the correct location
- Clear browser cache and check again

### Animation Not Working
- Check that jQuery is loading properly
- Verify JavaScript files are enqueued correctly
- Check browser console for errors

### Styles Not Loading
- Clear WordPress cache (if using caching plugin)
- Clear browser cache
- Verify CSS files are in `/assets/css/` directory

### Images Not Showing
- Check that images are in `/assets/images/` directory
- Regenerate thumbnails using plugin
- Verify file permissions are correct (644 for files, 755 for directories)

## Performance Optimization

### Image Optimization
1. Install **Smush** or **ShortPixel** plugin
2. Compress all images in Media Library
3. Enable lazy loading

### Caching
1. Install **WP Rocket** or **W3 Total Cache**
2. Enable page caching
3. Enable browser caching
4. Minify CSS and JavaScript

### CDN Setup (Optional)
1. Sign up for Cloudflare (free plan available)
2. Configure CDN settings
3. Update DNS records

## Browser Compatibility

Theme is tested and compatible with:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Support

For additional support:
- Documentation: https://premierplug.org/docs
- Support Forum: https://premierplug.org/support
- Email: support@premierplug.org

## Security Best Practices

1. Keep WordPress core, theme, and plugins updated
2. Use strong passwords
3. Install security plugin (Wordfence recommended)
4. Enable two-factor authentication
5. Regular backups (use UpdraftPlus or similar)
6. Limit login attempts
7. Use SSL certificate (HTTPS)

## Maintenance Checklist

### Weekly
- [ ] Check for WordPress updates
- [ ] Check for plugin updates
- [ ] Review security logs
- [ ] Test contact forms

### Monthly
- [ ] Full site backup
- [ ] Review analytics
- [ ] Check broken links
- [ ] Test site speed
- [ ] Update content

### Quarterly
- [ ] Review and update plugins
- [ ] Security audit
- [ ] Performance optimization review
- [ ] Content audit

## Additional Resources

- WordPress Codex: https://codex.wordpress.org/
- WordPress Support Forums: https://wordpress.org/support/
- Theme Developer Documentation: https://developer.wordpress.org/themes/

## Credits

Theme developed by PremierPlug Team
Original static site converted to WordPress theme
Version 1.0.0
