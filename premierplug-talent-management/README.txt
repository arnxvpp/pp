=== PremierPlug Talent Management ===
Contributors: PremierPlug Team
Tags: talent, portfolio, management, agency, roster
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.0.0
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
* Supabase database sync
* AJAX-powered search and filtering
* Multiple display options (grid, list, single)
* Responsive design
* 4 powerful shortcodes

**Shortcodes:**

* `[talent_grid]` - Display talents in grid layout
* `[talent_list]` - Display talents in list layout
* `[talent_single id="123"]` - Display single talent profile
* `[talent_search]` - Display search form with live results

== Installation ==

1. Upload `premierplug-talent-management` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu
3. Go to Talents → Settings to configure
4. Start adding talents!

== Frequently Asked Questions ==

= How do I display talents on my site? =

Use shortcodes:
- Grid: `[talent_grid category="motion-pictures" limit="12" columns="3"]`
- List: `[talent_list category="speakers"]`
- Search: `[talent_search]`

= Does this work with Supabase? =

Yes! The plugin automatically syncs all talent data to Supabase when configured.

= Can I customize the templates? =

Yes! Copy templates from plugin folder to your theme's `premierplug-talent-management/` folder.

== Screenshots ==

1. Talent list in admin
2. Edit talent profile
3. Talent grid display
4. Single talent profile
5. Search and filter interface

== Changelog ==

= 1.0.0 =
* Initial release
* Custom post type for talents
* Categories and skills taxonomies
* Supabase integration
* Search and filter functionality
* Multiple display templates
* 4 shortcodes
* Admin interface

== Upgrade Notice ==

= 1.0.0 =
Initial release
