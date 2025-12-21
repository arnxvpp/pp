=== PremierPlug Talent Management ===
Contributors: PremierPlug Team
Tags: talent, portfolio, management, agency, roster
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.3.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete talent management system for agencies - manage talent profiles, categories, and showcase your roster.

== Description ==

PremierPlug Talent Management is a comprehensive plugin for managing talent rosters. Perfect for talent agencies, entertainment companies, and creative firms.

**Features:**

* Custom post type for talents
* Categories: Motion Pictures, Digital Media, Speakers, Television, Voiceovers, Music
* Skills taxonomy for talent expertise
* Availability status tracking
* Contact information management
* Professional details (experience, rates)
* Social media integration
* Dynamic custom post type manager
* Create unlimited article types via admin panel
* Create unlimited talent types/categories via admin panel
* Create unlimited service types via admin panel
* Built-in article types: press releases, industry insights, thought leadership, company news, case studies
* Talent-article relationships
* AJAX-powered search and filtering
* Multiple display options (grid, list, single)
* Responsive design
* Powerful shortcodes
* 100% WordPress-native (no external dependencies)

**Shortcodes:**

* `[talent_grid]` - Display talents in grid layout
* `[talent_list]` - Display talents in list layout
* `[talent_single id="123"]` - Display single talent profile
* `[talent_search]` - Display search form with live results
* `[talent_articles]` - Display articles for specific talent
* `[article_grid]` - Display article grid
* `[article_list]` - Display article list

== Installation ==

1. Upload `premierplug-talent-management` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu
3. Default categories are created automatically
4. Start adding talents and articles!
5. Use shortcodes to display content on your pages

No external services or configuration needed!

== Frequently Asked Questions ==

= How do I display talents on my site? =

Use shortcodes:
- Grid: `[talent_grid category="motion-pictures" limit="12" columns="3"]`
- List: `[talent_list category="speakers"]`
- Search: `[talent_search]`

= Do I need any external services? =

No! Everything is stored in your WordPress database. No external services, APIs, or accounts needed.

= Can I customize the templates? =

Yes! Copy templates from plugin folder to your theme's `premierplug-talent-management/` folder.

== Screenshots ==

1. Talent list in admin
2. Edit talent profile
3. Talent grid display
4. Single talent profile
5. Search and filter interface

== Changelog ==

= 1.3.0 =
* NEW: Dynamic custom post type manager
* NEW: Add unlimited article types from admin panel
* NEW: Add unlimited talent types/categories from admin panel
* NEW: Add unlimited service types from admin panel
* NEW: Admin interface for managing all custom types
* NEW: No code editing required to add new types
* Improved flexibility and extensibility
* Enhanced admin experience

= 1.2.0 =
* Removed all external dependencies
* Pure WordPress implementation
* Everything stored in WordPress database
* Improved performance and reliability
* Simplified installation
* Enhanced documentation

= 1.1.0 =
* Added article management system
* Added talent-article relationships
* Added 5 article post types
* Added featured articles
* Added article shortcodes

= 1.0.0 =
* Initial release
* Custom post type for talents
* Categories and skills taxonomies
* Search and filter functionality
* Multiple display templates
* Admin interface

== Upgrade Notice ==

= 1.3.0 =
Major update! Now you can add unlimited custom post types from the admin panel. No code editing required.

= 1.2.0 =
This version removes external dependencies. All data remains safely in your WordPress database.
