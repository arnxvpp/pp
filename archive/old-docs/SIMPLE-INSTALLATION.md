# PremierPlug Talent Management - Simple Installation

## Quick Start (3 Steps)

### 1. Upload Plugin
Upload the `premierplug-talent-management` folder to:
```
/wp-content/plugins/
```

### 2. Activate
Go to: **WordPress Admin → Plugins → PremierPlug Talent Management**
Click: **Activate**

### 3. Done!
That's it! No configuration needed.

---

## What Gets Created Automatically

When you activate, the plugin automatically:
- Creates the "Talent" post type
- Creates 5 article post types (Press Releases, Blog Articles, Awards, News, Media Coverage)
- Creates these talent categories:
  - Motion Pictures
  - Digital Media
  - Speakers
  - Television
  - Voiceovers
  - Music
- Creates database table for talent-article relationships
- Sets up all templates and styles

---

## Start Using It

### Add Talent
1. Go to: **Talent → Add New**
2. Fill in name, bio, contact info
3. Add featured image (headshot)
4. Choose category
5. Publish

### Add Articles
1. Go to any article type menu (e.g., **Press Releases → Add New**)
2. Write your content
3. Link to talent profiles in the sidebar
4. Publish

### Display on Pages
Use these shortcodes on any page:

**Talent:**
- `[talent_search]` - Searchable talent grid
- `[talent_grid]` - Simple talent grid
- `[talent_list]` - Talent list view

**Articles:**
- `[article_grid type="press_release"]` - Article grid
- `[article_list type="blog_article"]` - Article list
- `[talent_articles talent_id="123"]` - Articles for specific talent

---

## Technical Details

### What's Stored
- All data in WordPress database (standard `wp_posts` table)
- Relationships in custom `wp_talent_article_relationships` table
- No external services
- No API calls
- 100% self-contained

### Requirements
- WordPress 5.8+
- PHP 7.4+
- MySQL 5.6+
- Any hosting provider

### File Structure
```
premierplug-talent-management/
├── premierplug-talent-management.php  (Main plugin file)
├── includes/                          (Core classes)
├── admin/                             (Admin interface)
├── public/                            (Frontend display)
├── templates/                         (Display templates)
├── assets/                            (CSS & JavaScript)
└── README.txt                         (Plugin info)
```

---

## Troubleshooting

### Permalinks Not Working?
Go to: **Settings → Permalinks → Save Changes**

### Shortcodes Not Displaying?
Make sure plugin is activated and page is published.

### Styles Look Wrong?
Clear your cache (browser and any caching plugins).

### Database Table Error?
Deactivate and reactivate the plugin to recreate tables.

---

## Support

For questions or issues:
- Email: support@premierplug.org
- Website: https://premierplug.org

---

## No External Dependencies

This plugin:
- ✅ Works offline
- ✅ No API keys needed
- ✅ No external accounts
- ✅ No monthly fees
- ✅ No data sent outside WordPress
- ✅ Works with any hosting
- ✅ Standard WordPress backup compatible

Your data stays in YOUR WordPress database.
