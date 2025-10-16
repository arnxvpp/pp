# PremierPlug Talent Manager

Comprehensive talent management system for WordPress with Supabase integration. Manages talent profiles across Digital Media, Television, Voiceovers, Speakers, and Motion Pictures segments.

## Features

### Core Functionality
- **Custom Post Type**: Dedicated `pp_talent` post type for talent profiles
- **Taxonomies**: Hierarchical segments and non-hierarchical skills
- **Custom Fields**: Profile information, contact details, portfolio items, availability status
- **Supabase Integration**: Real-time data synchronization with Supabase database
- **Analytics Tracking**: Profile views and inquiry tracking

### Frontend Features
- **Talent Directory**: Searchable, filterable talent roster matching PremierPlug design
- **Single Talent Profiles**: Detailed profiles with portfolios and contact forms
- **AJAX Filtering**: Real-time search and filtering without page reload
- **Inquiry Forms**: Contact forms with Supabase persistence
- **Responsive Design**: Mobile-first design matching existing site patterns

### Admin Features
- **Enhanced Post Editor**: Custom meta boxes for profile, contact, portfolio, and settings
- **Analytics Dashboard**: View counts, inquiry counts, top performers
- **Custom Admin Columns**: Quick view of key talent information
- **Settings Page**: Configure plugin behavior and Supabase sync
- **Bulk Operations**: Quick edit for availability and featured status

## Installation

1. **Upload Plugin**
   ```
   Upload the `premierplug-talent-manager` folder to `/wp-content/plugins/`
   ```

2. **Activate Plugin**
   ```
   Go to WordPress Admin > Plugins > Activate "PremierPlug Talent Manager"
   ```

3. **Configure Supabase** (Optional but recommended)
   - Ensure `.env` file in WordPress root contains:
   ```
   VITE_SUPABASE_URL=your_supabase_url
   VITE_SUPABASE_SUPABASE_ANON_KEY=your_supabase_anon_key
   ```

4. **Set Permalinks**
   ```
   Go to Settings > Permalinks and click "Save Changes"
   ```

## Usage

### Creating Talents

1. Navigate to **Talents > Add New**
2. Fill in the talent name and biography
3. Set a featured image (profile photo)
4. Complete the profile information:
   - Headline/Tagline
   - Years of Experience
5. Add contact information (optional but recommended)
6. Upload portfolio items (images, videos, audio, documents)
7. Select segments and skills
8. Set availability status and featured flag
9. Publish the talent

### Managing Talent Roster

- **View All Talents**: Talents > All Talents
- **Filter by Segment**: Use taxonomy filters
- **Quick Edit**: Hover over talent and click "Quick Edit"
- **Bulk Actions**: Select multiple talents and apply bulk actions

### Settings

Navigate to **Talents > Settings** to configure:
- Talents per page on archive
- Enable/disable analytics
- Cache duration for Supabase queries
- Enable/disable automatic Supabase sync

### Analytics

Navigate to **Talents > Analytics** to view:
- Total talents count
- Featured talents count
- Total profile views
- Total inquiries
- Top viewed talents leaderboard

## Shortcodes

### [talent_grid]
Display a grid of talents
```
[talent_grid segment="digital-media" count="12" columns="3"]
```

Attributes:
- `segment` - Filter by segment slug (optional)
- `count` - Number of talents to show (default: 12)
- `columns` - Grid columns (default: 3)
- `featured` - Show only featured talents (default: false)

### [featured_talents]
Display featured talents only
```
[featured_talents count="6" columns="3"]
```

### [talent_segments]
Display list of talent segments
```
[talent_segments show_count="true"]
```

## Templates

The plugin includes custom templates that match the PremierPlug design:

- `public/templates/archive-talent.php` - Talent directory/roster page
- `public/templates/single-talent.php` - Individual talent profile page
- `public/partials/talent-card.php` - Talent card component

### Theme Override

To customize templates, copy them to your theme:
```
wp-content/themes/your-theme/premierplug-talent/archive-talent.php
wp-content/themes/your-theme/premierplug-talent/single-talent.php
```

## Supabase Integration

### Database Schema

The plugin creates the following tables in Supabase:
- `talents` - Core talent information
- `talent_segments` - Predefined talent categories
- `talent_skills` - Skills and specializations
- `talent_segment_relationships` - Many-to-many talent-segment relationships
- `talent_skill_relationships` - Many-to-many talent-skill relationships
- `talent_media` - Portfolio items
- `talent_contacts` - Contact information
- `talent_inquiries` - Form submissions
- `talent_analytics` - View and inquiry tracking

### Data Sync

When Supabase is configured:
- Talent data automatically syncs on save
- Contact information syncs to `talent_contacts`
- Portfolio items sync to `talent_media`
- Segments and skills maintain relationships
- Analytics track in real-time

## Design System

The plugin matches the existing PremierPlug design:

### Colors
- **Brand Red**: `#BC1F2F` - Primary actions, accents, badges
- **Black**: Hero sections, contact sections
- **Red**: Alternating content sections
- **White**: Main content areas

### Typography
- **Fonts**: pf_dintext_pro, Helvetica Neue
- **Headings**: `.h8`, `.h7`, `.h6`, `.h5`, `.h4`, `.h1`
- **Weights**: 200 (light), 400 (regular), 500 (medium), 600 (bold)

### Layout
- `.gutter-container` - Content width constraint
- `.pad-tb` - Vertical padding
- `.hero-container.full_vh.bg-black` - Hero sections
- `.jumbo-text.text-module.col-2` - Two-column content

### Components
- Buttons: `.btn`, `.btn-hero`, `.button.button--primary`
- Forms: `.form-text`, `.form-email`, `.form-select`
- Badges: `.talent-segment-badge`, `.skill-badge`

## Hooks and Filters

### Actions

```php
// After talent is saved
do_action('pptm_after_save_talent', $post_id, $post);

// Before talent is synced to Supabase
do_action('pptm_before_sync_talent', $post_id, $talent_data);

// After talent is synced to Supabase
do_action('pptm_after_sync_talent', $post_id, $supabase_id);
```

### Filters

```php
// Modify talent data before Supabase sync
$talent_data = apply_filters('pptm_talent_sync_data', $talent_data, $post_id);

// Modify talent query arguments
$args = apply_filters('pptm_talent_query_args', $args);

// Modify talents per page
$per_page = apply_filters('pptm_talents_per_page', 12);
```

## Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 7.4 or higher
- **Supabase**: Account with database configured (optional)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Support

For issues or questions:
1. Check the WordPress admin notices for configuration issues
2. Verify Supabase credentials in `.env` file
3. Ensure permalinks are set correctly
4. Check browser console for JavaScript errors

## Changelog

### Version 1.0.0
- Initial release
- Custom post type and taxonomies
- Supabase integration
- Analytics tracking
- AJAX filtering
- Inquiry forms
- Admin dashboard
- Responsive design matching PremierPlug aesthetic

## Credits

Developed for PremierPlug by the PremierPlug Team.

## License

GPL v2 or later
