# WordPress White Screen Fix Guide
**Site:** https://wp.premierplug.org/

## Common Causes & Solutions

### Issue 1: PHP Memory Limit
**Symptoms:** White screen, no error message
**Solution:**
```php
// Add to wp-config.php (before "That's all, stop editing!")
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
```

### Issue 2: Plugin/Theme Conflict
**Symptoms:** White screen after activating plugin
**Solution:**
1. Access site via FTP/SSH
2. Rename `/wp-content/plugins/` to `/wp-content/plugins-disabled/`
3. Check if site loads
4. If yes, rename back and disable plugins one by one
5. For themes: rename `/wp-content/themes/premierplug-theme/` temporarily

### Issue 3: PHP Errors Disabled
**Symptoms:** White screen, no visible errors
**Solution:**
```php
// Add to wp-config.php (at the top after <?php)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

// Check error log at: wp-content/debug.log
```

### Issue 4: Syntax Error in Code
**Symptoms:** Sudden white screen after file changes
**Solution:**
1. Check PHP error logs (usually in `/var/log/apache2/error.log` or `/var/log/nginx/error.log`)
2. Or check `wp-content/debug.log` if WP_DEBUG is enabled
3. Fix the syntax error in the reported file

### Issue 5: Corrupted .htaccess
**Symptoms:** White screen on all pages
**Solution:**
1. Access via FTP/SSH
2. Rename `.htaccess` to `.htaccess.bak`
3. Go to WordPress Admin → Settings → Permalinks
4. Click "Save Changes" (regenerates .htaccess)

### Issue 6: Exhausted PHP Execution Time
**Symptoms:** White screen on import/heavy operations
**Solution:**
```php
// Add to wp-config.php
set_time_limit(300);
ini_set('max_execution_time', 300);
```

## Immediate Diagnostic Steps

### Step 1: Enable Debug Mode
```bash
# SSH into server
ssh user@server

# Edit wp-config.php
nano /path/to/wp-config.php

# Add these lines after <?php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);

# Save and exit (Ctrl+X, Y, Enter)
```

### Step 2: Check Error Logs
```bash
# Check WordPress debug log
tail -f /path/to/wp-content/debug.log

# Check PHP error log
tail -f /var/log/php_errors.log

# Check Apache error log
tail -f /var/log/apache2/error.log

# Or Nginx error log
tail -f /var/log/nginx/error.log
```

### Step 3: Disable All Plugins
```bash
# Via SSH/FTP - rename plugins folder
cd /path/to/wp-content/
mv plugins plugins-disabled

# Or via database
mysql -u username -p database_name
UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'active_plugins';
```

### Step 4: Switch to Default Theme
```bash
# Via database
mysql -u username -p database_name
UPDATE wp_options SET option_value = 'twentytwentythree' WHERE option_name = 'template';
UPDATE wp_options SET option_value = 'twentytwentythree' WHERE option_name = 'stylesheet';
```

## Specific to PremierPlug

### Check Theme Installation
```bash
# Verify theme files exist
ls -la /path/to/wp-content/themes/premierplug-theme/

# Required files:
# - style.css (with theme header)
# - functions.php
# - index.php
# - header.php
# - footer.php
```

### Check Plugin Installation
```bash
# Verify plugin files exist
ls -la /path/to/wp-content/plugins/premierplug-talent-manager/

# Required file:
# - premierplug-talent-manager.php (with plugin header)
```

### Verify PHP Version
```bash
php -v
# Should be 7.4 or higher

# If too old, update PHP or contact hosting provider
```

### Check File Permissions
```bash
# Set correct permissions
cd /path/to/wordpress/
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 600 wp-config.php
```

## Emergency Recovery Steps

### Option 1: Fresh WordPress Install (Keep Database)
```bash
# 1. Backup database
mysqldump -u username -p database_name > backup.sql

# 2. Download fresh WordPress
wget https://wordpress.org/latest.zip
unzip latest.zip

# 3. Copy old wp-config.php
cp /old/wp-config.php /new/wordpress/

# 4. Copy themes and plugins
cp -r /old/wp-content/themes/* /new/wordpress/wp-content/themes/
cp -r /old/wp-content/plugins/* /new/wordpress/wp-content/plugins/
cp -r /old/wp-content/uploads/* /new/wordpress/wp-content/uploads/
```

### Option 2: Access Via phpMyAdmin
If you can't access the site but have database access:
```sql
-- Deactivate all plugins
UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'active_plugins';

-- Switch to default theme
UPDATE wp_options SET option_value = 'twentytwentythree' WHERE option_name = 'template';
UPDATE wp_options SET option_value = 'twentytwentythree' WHERE option_name = 'stylesheet';

-- Increase memory limit in database
UPDATE wp_options SET option_value = '256M' WHERE option_name = 'wp_memory_limit';
```

### Option 3: FTP Recovery Mode
WordPress 5.2+ has recovery mode:
1. Trigger an error to get recovery link
2. Check your admin email for recovery link
3. Access via link to disable problematic plugin

## Hosting-Specific Solutions

### For Shared Hosting (cPanel)
1. Log into cPanel
2. Go to "Error Log" - check recent errors
3. Go to "File Manager" - enable "Show Hidden Files"
4. Edit `.htaccess` or `wp-config.php`
5. Use "PHP Version" tool to check/update PHP

### For VPS/Cloud Hosting
```bash
# Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php7.4-fpm

# Check server logs
journalctl -xe
```

### For WordPress.com / Managed WordPress
1. Contact support (they have access to detailed logs)
2. Use their built-in debugging tools
3. May need to wait for them to investigate

## Deployment Checklist (To Prevent White Screen)

### Before Uploading Theme/Plugin

1. **Test Locally First**
   - Install WordPress locally (XAMPP/MAMP/Local)
   - Test theme and plugin
   - Ensure no PHP errors

2. **Check Requirements**
   - PHP 7.4+ ✓
   - WordPress 5.8+ ✓
   - MySQL 5.7+ ✓
   - Required PHP extensions (curl, json, mbstring) ✓

3. **Validate Files**
   ```bash
   # Check all PHP files for syntax errors
   find . -name "*.php" -exec php -l {} \;
   ```

### During Upload

1. **Upload in Correct Order**
   - Upload theme files first
   - Upload plugin files second
   - Don't activate until all files uploaded

2. **Set Permissions**
   ```bash
   # Before activating
   chmod 644 *.php
   chmod 755 assets/
   ```

3. **Enable Debugging First**
   - Add WP_DEBUG to wp-config.php
   - Monitor debug.log during activation

### After Upload

1. **Activate Theme**
   - Go to Appearance → Themes
   - Activate PremierPlug theme
   - Check homepage loads

2. **Activate Plugin**
   - Go to Plugins
   - Activate PremierPlug Talent Manager
   - Check for errors

3. **Test Core Features**
   - View homepage
   - Create test talent
   - View talent roster
   - Test AJAX filtering

## Quick Recovery Commands

```bash
# One-liner to disable all plugins and switch to default theme
mysql -u root -p wordpress -e "UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'active_plugins'; UPDATE wp_options SET option_value = 'twentytwentythree' WHERE option_name = 'template';"

# One-liner to enable WordPress debug mode
sed -i "s/define('WP_DEBUG', false);/define('WP_DEBUG', true);\ndefine('WP_DEBUG_LOG', true);/" wp-config.php
```

## Contact Support With This Info

If contacting hosting support, provide:
1. **Error Log Contents** (last 50 lines)
2. **PHP Version** (from phpinfo or command line)
3. **WordPress Version**
4. **Recently Installed Plugins/Themes**
5. **Recent Changes Made**

## Expected Results After Fix

✓ Homepage loads correctly
✓ Admin dashboard accessible
✓ No PHP errors in logs
✓ Theme displays properly
✓ Plugins function correctly

---

**Need More Help?**
1. Check WordPress.org support forums
2. Contact your hosting provider
3. Hire a WordPress developer if issue persists

**Quick Support Info:**
- Theme: PremierPlug (custom)
- Plugin: PremierPlug Talent Manager v1.0.0
- PHP Required: 7.4+
- WordPress Required: 5.8+
