# PremierPlug Talent Manager - Installation Guide

## Quick Start

### 1. Plugin Installation

The plugin is already in place at:
```
/wp-content/plugins/premierplug-talent-manager/
```

**Activate the plugin:**
1. Log in to WordPress Admin
2. Navigate to **Plugins > Installed Plugins**
3. Find "PremierPlug Talent Manager"
4. Click **Activate**

### 2. Database Setup

The Supabase database schema has been automatically created with the following tables:
- ✅ `talent_segments` (5 default segments: Digital Media, Television, Voiceovers, Speakers, Motion Pictures)
- ✅ `talents` (core talent profiles)
- ✅ `talent_segment_relationships` (many-to-many relationships)
- ✅ `talent_skills` (skills and specializations)
- ✅ `talent_skill_relationships` (many-to-many relationships)
- ✅ `talent_media` (portfolio items)
- ✅ `talent_contacts` (contact information)
- ✅ `talent_inquiries` (form submissions)
- ✅ `talent_analytics` (tracking data)

### 3. Supabase Configuration

Your Supabase credentials are already configured in the `.env` file:
```
VITE_SUPABASE_URL=https://mdniuqoqqbcvlvldfskj.supabase.co
VITE_SUPABASE_SUPABASE_ANON_KEY=[your key]
```

The plugin automatically reads these credentials.

### 4. Permalink Setup

**IMPORTANT**: After activation, flush permalinks:
1. Go to **Settings > Permalinks**
2. Click **Save Changes** (no changes needed, just save)

This ensures the talent roster URL (`/talent-roster/`) works correctly.

### 5. First Talent Profile

Create your first talent:
1. Navigate to **Talents > Add New**
2. Enter the talent name
3. Add a biography in the content area
4. Upload a profile image (Set Featured Image)
5. Fill in the **Talent Profile Information** meta box:
   - Headline/Tagline
   - Years of Experience
6. Select segments: Digital Media, Television, etc.
7. Add skills (create new or select existing)
8. Set availability status in the **Talent Settings** box
9. Optionally check "Feature this talent" for homepage display
10. Click **Publish**

### 6. View Your Talent Roster

Visit the following URLs:
- **All Talents**: `yoursite.com/talent-roster/`
- **By Segment**: `yoursite.com/talent-segment/digital-media/`
- **Single Profile**: `yoursite.com/talent/john-doe/`

## Feature Configuration

### Settings Page

Navigate to **Talents > Settings** to configure:

- **Talents Per Page**: Number shown on archive (default: 12)
- **Enable Analytics**: Track profile views and inquiries (recommended: ON)
- **Cache Duration**: Supabase query caching in seconds (default: 900)
- **Supabase Sync**: Enable automatic data sync (recommended: ON)

### Analytics Dashboard

View performance metrics at **Talents > Analytics**:
- Total talents count
- Featured talents
- Total profile views
- Total inquiries
- Top viewed talents leaderboard

## Integration with Existing Pages

### Option 1: Update Existing Segment Pages

The plugin works seamlessly with your existing segment pages. To show talents on these pages, add the shortcode:

**Digital Media Page** (`/digital-media.html`):
```
[talent_grid segment="digital-media" count="12" columns="3"]
```

**Television Page** (`/television.html`):
```
[talent_grid segment="television" count="12" columns="3"]
```

**Voiceovers Page** (`/voiceovers.html`):
```
[talent_grid segment="voiceovers" count="12" columns="3"]
```

**Speakers Page** (`/speakers.html`):
```
[talent_grid segment="speakers" count="12" columns="3"]
```

**Motion Pictures Page** (`/motion-pictures.html`):
```
[talent_grid segment="motion-pictures" count="12" columns="3"]
```

### Option 2: Add to Navigation

Add "Browse Talent" links to your main navigation under "For Talents" section.

### Option 3: Homepage Featured Talents

Add featured talents to homepage:
```
[featured_talents count="6" columns="3"]
```

## Testing Checklist

### ✅ Plugin Activation
- [ ] Plugin activates without errors
- [ ] Talents menu appears in WordPress admin
- [ ] Settings page loads correctly

### ✅ Create Talent
- [ ] Can create new talent profile
- [ ] Featured image uploads correctly
- [ ] Meta boxes save data properly
- [ ] Taxonomies (segments/skills) work

### ✅ Frontend Display
- [ ] Talent archive page displays at `/talent-roster/`
- [ ] Talent cards show correctly with images
- [ ] Single talent page displays profile
- [ ] Portfolio items display correctly

### ✅ Filtering
- [ ] Search box filters talents
- [ ] Segment checkboxes filter results
- [ ] Availability dropdown filters
- [ ] Clear filters button works

### ✅ Inquiry Form
- [ ] Form appears on single talent page
- [ ] Form submits successfully
- [ ] Email notifications sent
- [ ] Data saved to Supabase

### ✅ Supabase Sync
- [ ] Check Supabase dashboard shows data
- [ ] Talents table populates
- [ ] Segments relationships work
- [ ] Portfolio items sync

### ✅ Analytics
- [ ] Profile views tracked
- [ ] Inquiry count increases
- [ ] Analytics dashboard shows data

## Troubleshooting

### Permalinks Not Working
**Symptoms**: 404 error on talent pages
**Solution**: Go to Settings > Permalinks and click Save Changes

### Supabase Not Syncing
**Symptoms**: Warning message about Supabase configuration
**Solution**:
1. Check `.env` file exists in WordPress root
2. Verify credentials are correct
3. Go to Talents > Settings and check Supabase status

### Images Not Displaying
**Symptoms**: Placeholder icons instead of photos
**Solution**:
1. Ensure featured images are set
2. Check file permissions on uploads folder
3. Verify image URLs are correct

### No Talents Showing
**Symptoms**: Empty archive page
**Solution**:
1. Create at least one published talent
2. Check talent is set to "Published" status
3. Verify permalink structure is saved

### AJAX Filtering Not Working
**Symptoms**: Page reloads instead of filtering
**Solution**:
1. Check JavaScript console for errors
2. Ensure jQuery is loaded
3. Verify nonces are being generated

## Advanced Configuration

### Custom Templates

To customize templates, copy to your theme:
```
wp-content/themes/premierplug/
  └── premierplug-talent/
      ├── archive-talent.php
      ├── single-talent.php
      └── talent-card.php
```

### Custom CSS

Add custom styles to your theme:
```css
/* Additional talent styling */
.pptm-talent-card {
    /* Your custom styles */
}
```

### Hooks and Filters

Use WordPress hooks to extend functionality:
```php
// Modify talent data before Supabase sync
add_filter('pptm_talent_sync_data', function($data, $post_id) {
    // Your modifications
    return $data;
}, 10, 2);

// After talent is saved
add_action('pptm_after_save_talent', function($post_id, $post) {
    // Your custom actions
}, 10, 2);
```

## Support Resources

### Plugin Files Structure
```
premierplug-talent-manager/
├── premierplug-talent-manager.php (Main plugin file)
├── README.md (Full documentation)
├── INSTALLATION.md (This file)
├── includes/ (Core classes)
│   ├── class-supabase-client.php
│   ├── class-talent-post-type.php
│   ├── class-talent-taxonomies.php
│   ├── class-talent-meta-boxes.php
│   ├── class-talent-sync.php
│   ├── class-talent-ajax.php
│   └── class-talent-shortcodes.php
├── admin/ (Admin interface)
│   └── class-talent-admin.php
├── public/ (Frontend)
│   ├── class-talent-public.php
│   ├── templates/
│   │   ├── archive-talent.php
│   │   └── single-talent.php
│   └── partials/
│       └── talent-card.php
└── assets/ (CSS/JS)
    ├── css/
    │   ├── public.css
    │   └── admin.css
    └── js/
        ├── public.js
        └── admin.js
```

### Key URLs
- **Talent Roster**: `/talent-roster/`
- **Digital Media**: `/talent-segment/digital-media/`
- **Television**: `/talent-segment/television/`
- **Voiceovers**: `/talent-segment/voiceovers/`
- **Speakers**: `/talent-segment/speakers/`
- **Motion Pictures**: `/talent-segment/motion-pictures/`

### Database Tables
All tables are prefixed in Supabase. Check your Supabase dashboard to view:
- talent_segments
- talents
- talent_segment_relationships
- talent_skills
- talent_skill_relationships
- talent_media
- talent_contacts
- talent_inquiries
- talent_analytics

## Next Steps

1. ✅ **Create talent profiles** for each segment
2. ✅ **Upload portfolio items** (images, videos, audio)
3. ✅ **Test filtering** on the archive page
4. ✅ **Submit test inquiry** form
5. ✅ **Check analytics** dashboard
6. ✅ **Add shortcodes** to existing pages
7. ✅ **Feature top talents** for homepage
8. ✅ **Monitor Supabase** data synchronization

## Success!

Your PremierPlug Talent Manager is now installed and configured. The system is ready to manage your talent roster with full Supabase integration, analytics tracking, and a beautiful frontend matching your existing design.

For detailed documentation, see the README.md file.
