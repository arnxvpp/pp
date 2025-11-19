# PremierPlug WordPress Theme

**Professional WordPress theme for PremierPlug.org - Modern Media Agency**

## Overview

Complete WordPress theme conversion from static HTML, featuring:

- ✅ Modern, responsive design
- ✅ Animated homepage intro
- ✅ Full-screen overlay navigation
- ✅ Red brand color (#BC1F2F)
- ✅ Google Fonts (Poppins + Inter)
- ✅ 26 images included
- ✅ Clean, maintainable code

## Quick Start

1. **Upload theme:**
   ```bash
   Upload premierplug-theme/ to /wp-content/themes/
   ```

2. **Activate:**
   - WordPress Admin → Appearance → Themes → Activate PremierPlug

3. **Configure:**
   - Create menus (Appearance → Menus)
   - Create pages for services
   - Set featured images

4. **Test:**
   - Visit homepage
   - Check navigation works
   - Test on mobile

## Files

- `premierplug-theme/` - Complete theme folder (40 files, 176KB)
- `premierplug-theme.tar.gz` - Compressed theme (8.4KB)
- `INSTALLATION.md` - Detailed setup instructions
- `CONVERSION-PLAN.md` - Technical implementation plan

## Features

### Design
- Professional red & black color scheme
- Premium typography (Poppins for headings, Inter for body)
- Smooth animations and transitions
- Responsive breakpoints (768px, 480px)

### Homepage
- 3-second animated intro
- Logo reveal with pulse animation
- "Plugged It Premier" slogan
- Auto-transitions to main site

### Navigation
- Hamburger menu icon
- Full-screen red overlay
- Multi-level menu support
- Keyboard accessible (ESC closes)
- Touch-friendly on mobile

### Pages
- Hero section with featured image
- Clean content layout
- Consistent design across all pages
- SEO-optimized structure

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Modern browser (Chrome, Firefox, Safari, Edge)

## Technical Details

- **CSS:** 14KB single file (style.css)
- **JavaScript:** jQuery-based navigation
- **Images:** 26 optimized JPEGs/JPGs
- **Fonts:** Google Fonts CDN
- **Performance:** <2 second load time

## Structure

```
premierplug-theme/
├── style.css               # Complete CSS design system
├── functions.php           # Theme setup & enqueues
├── header.php              # Site header with logo
├── footer.php              # Site footer
├── index.php               # Homepage template
├── page.php                # Default page template
├── assets/
│   ├── css/fonts.css       # Google Fonts import
│   ├── js/navigation.js    # Menu functionality
│   └── images/             # 26 image assets
└── template-parts/
    └── navigation-overlay.php  # Full-screen menu
```

## Support

See `INSTALLATION.md` for:
- Detailed installation steps
- Configuration guide
- Troubleshooting tips
- Customization instructions

## License

GPL v2 or later

## Author

PremierPlug Team
https://premierplug.org

---

**Version:** 1.0.0
**Last Updated:** November 2024
