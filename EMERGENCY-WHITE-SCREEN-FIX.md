# ⚠️ EMERGENCY: White Screen Fix for wp.premierplug.org

**Status:** Site showing white screen
**Urgency:** High
**Time to Fix:** 5-15 minutes

---

## 🚨 IMMEDIATE ACTION REQUIRED

The white screen is likely caused by one of these issues:
1. Plugin conflict
2. Theme error
3. PHP memory limit
4. Syntax error in code
5. Database connection issue

## 🔥 FASTEST FIX (Choose One Method)

### Method 1: Via cPanel/Hosting Control Panel (5 min)

1. **Log into your hosting control panel** (cPanel, Plesk, etc.)

2. **Go to File Manager**
   - Navigate to your WordPress root directory
   - Enable "Show Hidden Files"

3. **Enable WordPress Debug Mode**
   - Find and edit `wp-config.php`
   - Look for: `define('WP_DEBUG', false);`
   - Replace with:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   @ini_set('display_errors', 0);
   ```
   - Save file

4. **Check Debug Log**
   - Navigate to `wp-content/debug.log`
   - View the file - it will show the exact error
   - **Send me the last 20 lines of this file**

### Method 2: Disable All Plugins (3 min)

1. **Via FTP/File Manager**
   - Go to `wp-content/`
   - Rename `plugins` folder to `plugins-disabled`
   - Refresh site - does it load now?

2. **Via phpMyAdmin** (if File Manager doesn't work)
   - Log into phpMyAdmin
   - Select your WordPress database
   - Click "SQL" tab
   - Run this query:
   ```sql
   UPDATE wp_options
   SET option_value = 'a:0:{}'
   WHERE option_name = 'active_plugins';
   ```
   - Refresh site

### Method 3: Switch to Default Theme (3 min)

1. **Via phpMyAdmin**
   - Log into phpMyAdmin
   - Select your WordPress database
   - Click "SQL" tab
   - Run this query:
   ```sql
   UPDATE wp_options
   SET option_value = 'twentytwentythree'
   WHERE option_name = 'template';

   UPDATE wp_options
   SET option_value = 'twentytwentythree'
   WHERE option_name = 'stylesheet';
   ```
   - Refresh site

### Method 4: Upload Diagnostic File (2 min)

1. **Download diagnostic-check.php** from project folder
2. **Upload to WordPress root** via FTP/File Manager
3. **Visit:** https://wp.premierplug.org/diagnostic-check.php
4. **Read the results** - it will show exactly what's wrong
5. **DELETE the file immediately after** (security risk)

---

## 📋 MOST LIKELY ISSUES & FIXES

### Issue: "Fatal error: Allowed memory size exhausted"

**Quick Fix via wp-config.php:**
```php
// Add after <?php at the top
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
```

### Issue: "Parse error: syntax error"

**Fix:** Check the file mentioned in error
- Use FTP to download the file
- Fix the syntax error (usually missing semicolon or bracket)
- Re-upload the fixed file

### Issue: "Database connection error"

**Fix via wp-config.php:**
- Check these lines are correct:
```php
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASSWORD', 'your_database_password');
define('DB_HOST', 'localhost'); // or your host
```

### Issue: ".htaccess corrupted"

**Quick Fix:**
1. Rename `.htaccess` to `.htaccess.backup`
2. Create new `.htaccess` with:
```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
```

---

## 🔍 DIAGNOSTIC CHECKLIST

Run through these checks:

### 1. Can you access WordPress admin?
- Try: https://wp.premierplug.org/wp-admin/
- If YES: Plugin/theme issue on frontend only
- If NO: Server or WordPress core issue

### 2. Can you access phpMyAdmin?
- If YES: Database is working
- If NO: Contact hosting provider

### 3. Do you have FTP/File Manager access?
- If YES: You can fix most issues
- If NO: Request from hosting provider

### 4. Check hosting provider's error logs
- Most hosts show last errors in control panel
- Look for PHP errors from today

---

## 📞 WHAT TO TELL YOUR HOSTING PROVIDER

If you need to contact support, say:

> "My WordPress site at wp.premierplug.org is showing a white screen. Can you please:
> 1. Check the PHP error logs for today
> 2. Enable WordPress debug mode in wp-config.php
> 3. Send me the contents of wp-content/debug.log
> 4. Confirm PHP version is 7.4 or higher
> 5. Confirm memory_limit is at least 256M"

---

## ✅ ONCE SITE LOADS AGAIN

After you fix the white screen:

1. **Re-enable plugins one by one** (if you disabled them)
2. **Check which plugin caused the issue**
3. **Keep debug mode ON** until stable
4. **Test all pages:**
   - Homepage: /
   - About: /about-us/
   - Contact: /contact/
   - Services pages

---

## 🛠️ PROPER DEPLOYMENT (To Prevent Future Issues)

### Step 1: Prepare Hosting Environment
```bash
# Make sure these exist:
wp-content/themes/     ← Upload premierplug-theme here
wp-content/plugins/    ← Upload premierplug-talent-manager here
wp-content/uploads/    ← Should be writable (755)
```

### Step 2: Upload Theme
1. **Zip the theme:**
   - Go to `premierplug-theme/` folder
   - Zip all contents (not the folder itself)

2. **Upload via WordPress Admin:**
   - Go to Appearance → Themes → Add New → Upload
   - Choose the zip file
   - Click "Install Now"
   - **DON'T activate yet**

### Step 3: Upload Plugin
1. **Zip the plugin:**
   - Go to `wp-content/plugins/premierplug-talent-manager/`
   - Zip all contents

2. **Upload via WordPress Admin:**
   - Go to Plugins → Add New → Upload
   - Choose the zip file
   - Click "Install Now"
   - **DON'T activate yet**

### Step 4: Upload .env File
1. **Via FTP/File Manager:**
   - Upload `.env` file to WordPress root
   - File contents:
   ```
   VITE_SUPABASE_URL=https://mdniuqoqqbcvlvldfskj.supabase.co
   VITE_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
   ```

### Step 5: Import Database Content
1. **Upload SQL file:**
   - Go to `database/location-pages-and-blogs.sql`
   - Import via phpMyAdmin or WordPress plugin

### Step 6: Activate Theme
1. Go to Appearance → Themes
2. Click "Activate" on PremierPlug theme
3. **Check homepage loads** before continuing

### Step 7: Activate Plugin
1. Go to Plugins
2. Click "Activate" on PremierPlug Talent Manager
3. **Check no errors appear**

### Step 8: Flush Permalinks
1. Go to Settings → Permalinks
2. Click "Save Changes"
3. **Required for talent-roster URL to work**

---

## 🆘 STILL NOT WORKING?

**Option 1: Fresh Start**
- Backup current site
- Install fresh WordPress
- Upload theme and plugin
- Import database

**Option 2: Contact Me**
- Send screenshot of error
- Send debug.log contents
- Send phpinfo() output
- I'll help diagnose

**Option 3: Professional Help**
- Your hosting provider support
- WordPress developer
- Emergency support services

---

## 📝 QUICK COMMAND REFERENCE

```bash
# SSH Commands (if you have SSH access)

# Enable debug mode
sed -i "s/define('WP_DEBUG', false);/define('WP_DEBUG', true);/" wp-config.php

# Check recent errors
tail -50 wp-content/debug.log

# Disable all plugins
cd wp-content && mv plugins plugins-disabled

# Check file permissions
ls -la wp-content/

# Restart PHP (if needed)
sudo systemctl restart php7.4-fpm
```

```sql
-- SQL Commands (via phpMyAdmin)

-- Disable all plugins
UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'active_plugins';

-- Switch to default theme
UPDATE wp_options SET option_value = 'twentytwentythree' WHERE option_name = 'template';

-- Check site URL
SELECT * FROM wp_options WHERE option_name IN ('siteurl', 'home');
```

---

## ⏱️ EXPECTED TIMELINE

- **Identify issue:** 5 minutes (with diagnostic file)
- **Apply fix:** 2-5 minutes
- **Test site:** 5 minutes
- **Deploy properly:** 30 minutes
- **Total:** ~45 minutes to fully working site

---

**Priority Actions:**
1. ✓ Enable debug mode
2. ✓ Check debug.log
3. ✓ Upload diagnostic-check.php
4. ✓ Report findings back

**DO NOT:**
- ❌ Delete WordPress core files
- ❌ Change database credentials randomly
- ❌ Upload files without backing up first
- ❌ Leave diagnostic file on server after use

---

**Need immediate help? Send me:**
1. Screenshot of white screen
2. Last 50 lines of wp-content/debug.log
3. Hosting provider name
4. PHP version (from hosting control panel)
