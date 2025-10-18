# PremierPlug Talent Manager - Deployment Guide for Live Hosting

## 📦 Deployment to Your Hosting

This guide will walk you through deploying the Talent Manager plugin to your live WordPress hosting.

---

## 🎯 Prerequisites

Before starting, ensure you have:
- ✅ Access to your hosting control panel (cPanel, Plesk, etc.)
- ✅ FTP/SFTP credentials OR File Manager access
- ✅ WordPress Admin login credentials
- ✅ Supabase credentials (already configured in your .env file)

---

## 📋 Deployment Methods

Choose the method that works best for your hosting setup:

### **Method 1: Upload via FTP/SFTP** (Recommended)
### **Method 2: Upload via Hosting File Manager**
### **Method 3: Upload as ZIP via WordPress Admin**

---

## 🚀 Method 1: Upload via FTP/SFTP (Recommended)

### Step 1: Connect to Your Hosting via FTP

**Using FileZilla (or any FTP client):**

1. Open FileZilla (download from https://filezilla-project.org if needed)
2. Enter your connection details:
   - **Host**: `ftp.yourdomain.com` or your hosting IP
   - **Username**: Your FTP username
   - **Password**: Your FTP password
   - **Port**: 21 (FTP) or 22 (SFTP - more secure)
3. Click **Quickconnect**

**Connection details are usually found in:**
- cPanel > FTP Accounts
- Hosting welcome email
- Contact your hosting provider support

### Step 2: Navigate to WordPress Plugins Directory

In FileZilla's **Remote Site** panel (right side):
```
Navigate to: /public_html/wp-content/plugins/
```

**Note**: The path might vary:
- `public_html/wp-content/plugins/`
- `www/wp-content/plugins/`
- `httpdocs/wp-content/plugins/`
- Ask your host if unsure

### Step 3: Upload the Plugin Folder

On your **Local Site** panel (left side):
```
Navigate to: /tmp/cc-agent/58701983/project/wp-content/plugins/
```

**Upload the entire folder:**
1. Right-click on `premierplug-talent-manager` folder
2. Select **Upload**
3. Wait for all 19 files to transfer (should take 10-30 seconds)

**Verify Upload:**
Check that these folders exist on the remote server:
```
/wp-content/plugins/premierplug-talent-manager/
  ├── includes/
  ├── admin/
  ├── public/
  └── assets/
```

### Step 4: Verify File Permissions

**Set correct permissions via FTP:**
- Folders: `755` (rwxr-xr-x)
- PHP Files: `644` (rw-r--r--)

**Right-click on `premierplug-talent-manager` folder > File Permissions:**
- Check: Read, Write, Execute for Owner
- Check: Read, Execute for Group
- Check: Read, Execute for Public
- Check: **Recurse into subdirectories**
- Click **OK**

### Step 5: Verify Supabase Configuration

Ensure your `.env` file exists in the WordPress root:

**Navigate to**: `/public_html/.env` (or `/www/.env`)

**Verify it contains:**
```
VITE_SUPABASE_URL=https://mdniuqoqqbcvlvldfskj.supabase.co
VITE_SUPABASE_SUPABASE_ANON_KEY=your_anon_key_here
```

**If `.env` doesn't exist**, create it via FTP:
1. Right-click in the WordPress root directory
2. Select **Create file**
3. Name it `.env`
4. Right-click > **Edit**
5. Paste the Supabase credentials
6. Save and upload

---

## 🚀 Method 2: Upload via Hosting File Manager

### Step 1: Access Your Hosting Control Panel

Log in to your hosting control panel:
- **cPanel**: Usually at `yourdomain.com/cpanel`
- **Plesk**: Usually at `yourdomain.com:8443`

### Step 2: Open File Manager

**In cPanel:**
1. Find **Files** section
2. Click **File Manager**
3. Navigate to `public_html/wp-content/plugins/`

**In Plesk:**
1. Go to **Files** > **File Manager**
2. Navigate to `httpdocs/wp-content/plugins/`

### Step 3: Create Plugin Directory

1. Click **+ Folder** or **New Folder**
2. Name it: `premierplug-talent-manager`
3. Click **Create**

### Step 4: Upload Plugin Files

**Option A: Upload as ZIP (Faster)**

1. First, create a ZIP file of the plugin on your local machine:
   ```bash
   cd /tmp/cc-agent/58701983/project/wp-content/plugins/
   zip -r premierplug-talent-manager.zip premierplug-talent-manager/
   ```

2. In File Manager, navigate to `wp-content/plugins/`
3. Click **Upload**
4. Select `premierplug-talent-manager.zip`
5. After upload, right-click the ZIP file
6. Select **Extract** or **Uncompress**
7. Delete the ZIP file after extraction

**Option B: Upload Individual Files (Slower)**

1. Navigate into `premierplug-talent-manager` folder
2. Click **Upload**
3. Select all plugin files and folders
4. Wait for upload to complete

### Step 5: Set Permissions

1. Right-click on `premierplug-talent-manager` folder
2. Select **Permissions** or **Change Permissions**
3. Set to `755`
4. Check **Recurse into subdirectories**
5. Click **Change**

---

## 🚀 Method 3: Upload as ZIP via WordPress Admin

### Step 1: Create Plugin ZIP File

On your development machine:

```bash
cd /tmp/cc-agent/58701983/project/wp-content/plugins/
zip -r premierplug-talent-manager.zip premierplug-talent-manager/
```

**Windows Users (PowerShell):**
```powershell
Compress-Archive -Path "premierplug-talent-manager" -DestinationPath "premierplug-talent-manager.zip"
```

**Or manually:**
1. Navigate to the `plugins` folder
2. Right-click `premierplug-talent-manager` folder
3. Select "Compress" or "Send to > Compressed folder"
4. Save as `premierplug-talent-manager.zip`

### Step 2: Upload via WordPress Admin

1. Log in to WordPress Admin: `yourdomain.com/wp-admin`
2. Navigate to **Plugins > Add New**
3. Click **Upload Plugin** (top of page)
4. Click **Choose File**
5. Select `premierplug-talent-manager.zip`
6. Click **Install Now**
7. Wait for upload and installation

**Note**: This method requires:
- PHP upload limit ≥ 8MB (plugin is ~1MB)
- If upload fails, increase `upload_max_filesize` in php.ini

---

## ✅ Activate the Plugin

### Step 1: Activate in WordPress Admin

1. Go to **Plugins > Installed Plugins**
2. Find **PremierPlug Talent Manager**
3. Click **Activate**

**Expected Result:**
- ✅ Plugin activates successfully
- ✅ "Talents" menu appears in admin sidebar
- ✅ No error messages

**If you see errors:**
- Check PHP version (requires 7.4+)
- Verify all files uploaded correctly
- Check server error logs

### Step 2: Flush Permalinks

**CRITICAL STEP - Don't skip this!**

1. Navigate to **Settings > Permalinks**
2. Don't change anything, just click **Save Changes**
3. This registers the new URL structure

**What this does:**
- Registers `/talent-roster/` URL
- Registers `/talent/john-doe/` URLs
- Registers `/talent-segment/digital-media/` URLs

### Step 3: Verify Supabase Connection

1. Navigate to **Talents > Settings**
2. Check the **Supabase Sync** section
3. You should see: **"✓ Supabase is configured and connected"**

**If you see a warning:**
- Verify `.env` file exists in WordPress root
- Check credentials are correct
- Ensure no extra spaces in `.env` file

---

## 🎨 Testing Your Installation

### Test 1: Create Your First Talent

1. Navigate to **Talents > Add New**
2. Fill in:
   - **Title**: John Doe
   - **Content**: Professional voice actor with 15 years experience...
   - **Featured Image**: Upload a profile photo
   - **Headline**: Award-Winning Voice Actor
   - **Experience**: 15 years
   - **Segments**: Check "Voiceovers"
   - **Skills**: Add "Voice Acting", "Dubbing", "Narration"
   - **Availability**: Available
3. Click **Publish**

**Expected Result:**
- ✅ Talent saves successfully
- ✅ No error messages
- ✅ Meta boxes save data

### Test 2: View Talent Roster Page

Visit: `https://yourdomain.com/talent-roster/`

**Expected Result:**
- ✅ Page loads without 404 error
- ✅ Talent card displays with photo
- ✅ Filter section appears
- ✅ Design matches your site

**If you see 404 error:**
- Go back to Settings > Permalinks > Save Changes
- Clear your browser cache
- Wait 5 minutes for DNS/cache to update

### Test 3: View Single Talent Profile

Click on the talent card or visit:
`https://yourdomain.com/talent/john-doe/`

**Expected Result:**
- ✅ Profile page loads
- ✅ Hero section with photo
- ✅ Biography displays
- ✅ Contact form appears
- ✅ Design is consistent

### Test 4: Test Search and Filtering

On `/talent-roster/` page:
1. Type in the search box
2. Check segment filters
3. Select availability

**Expected Result:**
- ✅ Results filter in real-time
- ✅ No page reload
- ✅ Loading indicator appears
- ✅ Results update correctly

### Test 5: Submit Inquiry Form

On a talent profile:
1. Scroll to contact section
2. Fill in the form
3. Click Submit

**Expected Result:**
- ✅ Form submits successfully
- ✅ Success message appears
- ✅ Email notification sent
- ✅ Data saved in Supabase

**Verify in Supabase:**
1. Go to Supabase Dashboard
2. Navigate to Table Editor
3. Check `talent_inquiries` table
4. Your submission should appear

### Test 6: Check Analytics

1. Go to **Talents > Analytics**

**Expected Result:**
- ✅ Dashboard displays
- ✅ Shows "1 Total Talents"
- ✅ Shows "1 Total Profile Views"
- ✅ Top talents list appears

---

## 🔧 Troubleshooting Common Issues

### Issue 1: 404 Error on Talent Pages

**Symptom**: `/talent-roster/` shows "Page Not Found"

**Solution:**
1. Go to **Settings > Permalinks**
2. Click **Save Changes** (don't change anything)
3. Clear browser cache (Ctrl+Shift+Delete)
4. Try again after 5 minutes

**If still not working:**
- Check that plugin is activated
- Verify `.htaccess` file exists in WordPress root
- Check if WordPress is in a subdirectory
- Contact hosting support about mod_rewrite

### Issue 2: Supabase Not Configured Warning

**Symptom**: Warning message about Supabase configuration

**Solution:**
1. Check `.env` file exists in WordPress root (not in wp-content)
2. Verify format is correct (no quotes, no extra spaces):
   ```
   VITE_SUPABASE_URL=https://mdniuqoqqbcvlvldfskj.supabase.co
   VITE_SUPABASE_SUPABASE_ANON_KEY=eyJhbGc...
   ```
3. Make sure variable names are exact (case-sensitive)
4. Try adding credentials directly in wp-config.php:
   ```php
   define('VITE_SUPABASE_URL', 'https://mdniuqoqqbcvlvldfskj.supabase.co');
   define('VITE_SUPABASE_SUPABASE_ANON_KEY', 'your_key_here');
   ```

### Issue 3: Images Not Displaying

**Symptom**: Placeholder icons instead of photos

**Solution:**
1. Ensure featured images are set on talents
2. Check uploads folder permissions: `/wp-content/uploads/` should be `755`
3. Verify PHP GD library is installed (for image processing)
4. Check hosting disk space isn't full
5. Try re-uploading images

### Issue 4: Filtering Not Working

**Symptom**: Filter buttons don't do anything

**Solution:**
1. Check browser console for JavaScript errors (F12 > Console)
2. Verify jQuery is loaded (check page source)
3. Clear browser cache
4. Disable other plugins temporarily to check for conflicts
5. Check if theme has jQuery in footer (required)

### Issue 5: Forms Not Submitting

**Symptom**: Inquiry form doesn't submit or shows errors

**Solution:**
1. Check AJAX URL is correct (should be `/wp-admin/admin-ajax.php`)
2. Verify nonces are being generated
3. Check PHP error logs for server errors
4. Disable caching plugins temporarily
5. Check Supabase connection is working

### Issue 6: Plugin Won't Activate

**Symptom**: Error on activation or white screen

**Solution:**
1. Check PHP version: Should be 7.4 or higher
   - cPanel > Select PHP Version
   - Check PHP info
2. Increase PHP memory limit in wp-config.php:
   ```php
   define('WP_MEMORY_LIMIT', '256M');
   ```
3. Enable WordPress debug mode:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
4. Check `/wp-content/debug.log` for errors
5. Deactivate all other plugins, activate this one, then reactivate others

### Issue 7: Slow Page Loading

**Symptom**: Talent pages load slowly

**Solution:**
1. Enable caching in plugin settings:
   - **Talents > Settings**
   - Set Cache Duration to 900 seconds (15 minutes)
2. Install a caching plugin (WP Super Cache, W3 Total Cache)
3. Enable CDN for images
4. Optimize images before uploading (use TinyPNG, ImageOptim)
5. Consider upgrading hosting if on shared hosting

---

## 🎯 Post-Deployment Checklist

After deploying, verify:

- [ ] Plugin activated successfully
- [ ] Permalinks flushed (Settings > Permalinks > Save)
- [ ] `/talent-roster/` page loads
- [ ] Can create new talent profiles
- [ ] Featured images upload correctly
- [ ] Segments and skills work
- [ ] Filtering works on talent roster
- [ ] Single talent pages load
- [ ] Inquiry forms submit successfully
- [ ] Email notifications arrive
- [ ] Supabase shows "Connected" status
- [ ] Analytics dashboard displays data
- [ ] Mobile responsive design works
- [ ] Browser compatibility (Chrome, Firefox, Safari, Edge)

---

## 🚀 Next Steps After Deployment

### 1. Create Your Initial Talent Roster

**Digital Media Talents:**
- Navigate to **Talents > Add New**
- Add 3-5 talents for Digital Media segment
- Upload professional photos
- Fill in complete profiles

**Television Talents:**
- Create profiles for TV personalities
- Add portfolio videos if available

**Voiceovers, Speakers, Motion Pictures:**
- Continue adding talents for each segment
- Aim for at least 2-3 per segment initially

### 2. Configure Email Notifications

Edit the inquiry notification recipient:
1. Go to **Talents > Settings**
2. Or add this to `wp-config.php`:
   ```php
   define('PPTM_NOTIFICATION_EMAIL', 'talent@premierplug.org');
   ```

### 3. Add Shortcodes to Existing Pages

**Digital Media Page** (`/digital-media/`):
Add this shortcode to show Digital Media talents:
```
[talent_grid segment="digital-media" count="12" columns="3"]
```

**Homepage**:
Add featured talents:
```
[featured_talents count="6" columns="3"]
```

**How to add shortcodes:**
1. Edit the page in WordPress
2. Add a Shortcode block (or paste directly if using Classic Editor)
3. Paste the shortcode
4. Update the page

### 4. Update Navigation Menu

**Add Talent Links to Menu:**
1. Go to **Appearance > Menus**
2. Find "Talents" in the left panel
3. Check "Talent Roster" (archives page)
4. Click **Add to Menu**
5. Or add custom links:
   - `/talent-segment/digital-media/` - Digital Media Talents
   - `/talent-segment/television/` - Television Talents
   - `/talent-segment/voiceovers/` - Voiceover Talents
   - `/talent-segment/speakers/` - Speakers
   - `/talent-segment/motion-pictures/` - Motion Picture Talents

### 5. Set Up Google Analytics (Optional)

Add tracking to talent pages:
1. Install Google Analytics plugin
2. Or add GA code to `wp_head` hook
3. Track these events:
   - Talent profile views
   - Inquiry form submissions
   - Filter usage

### 6. Configure Caching

**Install a caching plugin:**
- WP Super Cache (recommended for beginners)
- W3 Total Cache (advanced users)
- WP Rocket (premium, best performance)

**Configure:**
1. Enable page caching
2. Enable browser caching
3. Enable GZIP compression
4. Exclude `/wp-admin/` from cache

### 7. Backup Your Site

**Set up automated backups:**
- UpdraftPlus (free)
- BackupBuddy (premium)
- VaultPress (Jetpack)

**Backup includes:**
- Database (includes all talent data)
- WordPress files
- Uploads folder (talent photos)

### 8. Monitor Performance

**Track these metrics:**
1. Page load speed (aim for <3 seconds)
   - Use GTmetrix.com
   - Use Google PageSpeed Insights
2. Talent profile views (via Analytics dashboard)
3. Inquiry conversion rate
4. Top performing talents

### 9. Content Strategy

**Plan regular updates:**
- Add 2-3 new talents per week
- Update existing profiles monthly
- Refresh portfolio items quarterly
- Feature different talents on homepage

### 10. SEO Optimization

**Optimize talent pages:**
1. Install Yoast SEO or Rank Math
2. Set focus keywords for talent profiles
3. Write meta descriptions
4. Add alt text to images
5. Create XML sitemap
6. Submit to Google Search Console

---

## 📞 Getting Help

### Plugin Documentation
- **README.md** - Complete feature documentation
- **INSTALLATION.md** - Setup instructions
- **This file** - Deployment guide

### Check These Resources
1. WordPress Admin Notices (for configuration warnings)
2. Browser Console (F12 > Console for JavaScript errors)
3. WordPress Debug Log (`/wp-content/debug.log`)
4. Supabase Dashboard (for database issues)
5. Hosting error logs (via cPanel or contact support)

### Common Log Locations
- **cPanel**: Home > Errors > Error Log
- **Plesk**: Logs > Error Log
- **WordPress**: `/wp-content/debug.log` (if WP_DEBUG enabled)
- **Server**: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`

### Need More Help?

If you encounter issues:
1. Enable WP_DEBUG to see detailed errors
2. Check if PHP version is 7.4+
3. Verify all files uploaded correctly
4. Ensure file permissions are correct (755/644)
5. Test with all other plugins deactivated
6. Try with a default WordPress theme (Twenty Twenty-Three)

---

## 🎉 Congratulations!

Your PremierPlug Talent Manager is now live on your hosting!

**Quick Wins:**
1. Create 5 talent profiles (one per segment)
2. Test the filtering system
3. Submit a test inquiry
4. Share the talent roster link with your team
5. Monitor the analytics dashboard

**Your talent management system is now:**
- ✅ Live and accessible to visitors
- ✅ Integrated with Supabase for scalability
- ✅ Tracking analytics and inquiries
- ✅ Matching your site's beautiful design
- ✅ Ready to showcase your amazing talent roster!

---

**Deployment Date**: [Current Date]
**Plugin Version**: 1.0.0
**WordPress**: 6.0+
**PHP**: 7.4+
**Status**: 🚀 Production Ready
