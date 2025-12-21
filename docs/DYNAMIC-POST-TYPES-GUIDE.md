# Dynamic Post Types Manager - User Guide

## Overview

Version 1.3.0 introduces a powerful dynamic post type manager that lets you create unlimited custom types directly from the WordPress admin panel - no code editing required!

## What Can You Create?

### 1. Article Types
Create different types of content articles:
- Press Releases
- Industry Insights
- Thought Leadership
- Company News
- Case Studies
- Blog Posts
- White Papers
- Research Reports
- And anything else you need!

### 2. Talent Types
Create categories to organize your talent:
- Actors
- Musicians
- Speakers
- Voice Artists
- Models
- Influencers
- And any other talent categories!

### 3. Service Types
Create categories for your services:
- Consulting
- Production
- Management
- Marketing
- And any other service categories!

## How to Access

1. Log into WordPress admin
2. Navigate to **Talents → Custom Types** in the sidebar menu
3. You'll see three sections: Article Types, Talent Types, and Service Types

## Adding a New Custom Type

### Article Type Example

Let's add a "White Paper" article type:

1. Click **"Add New Article Type"** button
2. Fill in the form:
   - **ID**: `white_paper` (auto-generated from label)
   - **Plural Label**: `White Papers`
   - **Singular Label**: `White Paper`
   - **URL Slug**: `white-papers` (auto-generated)
   - **Dashicon**: `dashicons-media-document` (see [Dashicons](https://developer.wordpress.org/resource/dashicons/))
3. Click **"Save Custom Type"**

That's it! Your new article type is now available in the admin menu.

### Talent Type Example

Let's add a "Voice Artists" talent category:

1. Click **"Add New Talent Type"** button
2. Fill in the form:
   - **ID**: `voice_artists`
   - **Plural Label**: `Voice Artists`
   - **Singular Label**: `Voice Artist`
   - **URL Slug**: `voice-artists`
   - **Dashicon**: `dashicons-microphone`
3. Click **"Save Custom Type"**

Now you can assign talents to the "Voice Artists" category!

### Service Type Example

Let's add a "Brand Consulting" service:

1. Click **"Add New Service Type"** button
2. Fill in the form:
   - **ID**: `brand_consulting`
   - **Plural Label**: `Brand Consulting`
   - **Singular Label**: `Brand Consulting`
   - **URL Slug**: `brand-consulting`
   - **Dashicon**: `dashicons-lightbulb`
3. Click **"Save Custom Type"**

Perfect! Now you can categorize talents by the services they offer.

## Field Explanations

### ID (internal)
- Used internally by WordPress
- Auto-generated from your label
- Use lowercase with underscores: `my_custom_type`
- Cannot be changed after creation

### Plural Label
- Displayed in admin menus and listings
- Examples: "Case Studies", "White Papers", "Voice Artists"

### Singular Label
- Used for single items
- Examples: "Case Study", "White Paper", "Voice Artist"

### URL Slug
- Used in website URLs
- Auto-generated from your label
- Use lowercase with hyphens: `my-custom-type`
- Examples: `/white-papers/`, `/voice-artists/`

### Dashicon
- Icon displayed in admin menu
- Browse available icons: [Dashicons Library](https://developer.wordpress.org/resource/dashicons/)
- Format: `dashicons-icon-name`
- Popular choices:
  - `dashicons-media-document` (documents)
  - `dashicons-star-filled` (featured content)
  - `dashicons-portfolio` (case studies)
  - `dashicons-microphone` (audio/voice)
  - `dashicons-video-alt3` (video)
  - `dashicons-awards` (awards/recognition)

## Managing Custom Types

### Viewing Custom Types
All your custom types are listed in tables showing:
- Label (display name)
- Slug (URL component)
- Icon (visual identifier)
- Status (enabled/disabled)
- Actions (delete button)

### Deleting a Custom Type
1. Click the **"Delete"** button next to the custom type
2. Confirm the deletion
3. The custom type is removed (existing content is preserved)

**Warning**: Deleting a custom type doesn't delete the content. The content will still exist but won't be accessible through the admin menu.

## Default Article Types

The plugin includes 5 default article types:
1. **Press Releases** (`/press-releases/`)
2. **Industry Insights** (`/industry-insights/`)
3. **Thought Leadership** (`/thought-leadership/`)
4. **Company News** (`/company-news/`)
5. **Case Studies** (`/case-studies/`)

You can delete these if you don't need them.

## Technical Details

### How It Works
- Custom types are stored in WordPress options table
- Post types are registered dynamically on every page load
- No database migrations needed
- All data is safely stored in WordPress

### Post Type Naming
- Article types: `article_{id}` (e.g., `article_white_paper`)
- Talent taxonomies: `talent_{id}` (e.g., `talent_voice_artists`)
- Service taxonomies: `service_{id}` (e.g., `service_brand_consulting`)

### Rewrite Rules
After adding or deleting custom types, WordPress automatically flushes rewrite rules to ensure URLs work correctly.

## Use Cases

### For Talent Agencies
- Create article types for different PR materials
- Create talent types for different performance categories
- Create service types for offerings (booking, management, consulting)

### For Creative Agencies
- Article types: Case Studies, Client Stories, Research Papers
- Talent types: Designers, Developers, Copywriters
- Service types: Branding, Web Design, Marketing

### For Consulting Firms
- Article types: White Papers, Research Reports, Industry Analysis
- Talent types: Consultants, Analysts, Specialists
- Service types: Strategy, Implementation, Training

## Best Practices

1. **Plan Your Structure**: Think about your content organization before creating types
2. **Use Clear Labels**: Make labels descriptive and user-friendly
3. **Consistent Naming**: Use consistent naming patterns across types
4. **Choose Appropriate Icons**: Pick icons that visually represent the content type
5. **Test URLs**: After creating a type, test that URLs work correctly
6. **Don't Over-Categorize**: Only create types you'll actually use

## Troubleshooting

### URLs Not Working After Adding Type
**Solution**: Go to Settings → Permalinks and click "Save Changes" to flush rewrite rules.

### Custom Type Not Appearing in Menu
**Solution**: Check that the type is enabled (green status). Try refreshing the page.

### Icon Not Displaying
**Solution**: Verify you're using a valid Dashicons class name (starts with `dashicons-`).

### Can't Delete Default Types
**Solution**: Default types can be deleted like any other type. Make sure you want to remove them first.

## Need Help?

For additional support or feature requests, please contact the PremierPlug team.

---

**Version**: 1.3.0
**Last Updated**: December 2024
