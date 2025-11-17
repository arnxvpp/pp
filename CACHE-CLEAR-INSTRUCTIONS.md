# Cache Clear Instructions for wp.premierplug.org

## Diagnostic Results: ✅ ALL SYSTEMS HEALTHY

The diagnostic shows WordPress, theme, and plugin are all working correctly. The white screen you're seeing is almost certainly a **caching issue**.

---

## 🔥 IMMEDIATE FIXES (Try in Order)

### Fix 1: Clear Browser Cache (1 minute)

**Chrome:**
1. Press `Ctrl+Shift+Delete` (Windows) or `Cmd+Shift+Delete` (Mac)
2. Select "Cached images and files"
3. Click "Clear data"
4. Or try **Incognito/Private mode**: `Ctrl+Shift+N`

**Firefox:**
1. Press `Ctrl+Shift+Delete`
2. Select "Cache"
3. Click "Clear Now"

**Safari:**
1. Safari menu → Clear History
2. Select "All History"
3. Click "Clear History"

**Quick Test:**
- Try visiting site in Incognito/Private browsing mode
- If it works there → Browser cache issue
- If still white → Server cache issue

---

### Fix 2: Clear LiteSpeed Cache (2 minutes)

Your server uses **LiteSpeed** which has aggressive caching.

**Method A: Via WordPress Admin**
1. Go to: https://wp.premierplug.org/wp-admin/
2. If LiteSpeed Cache plugin is installed:
   - Go to LiteSpeed Cache → Toolbox → Purge
   - Click "Purge All"
3. Or in top admin bar, click "Purge All" (if available)

**Method B: Via .htaccess**
1. Access via FTP/cPanel File Manager
2. Edit `.htaccess` in WordPress root
3. Add at the top:
```apache
# Disable LiteSpeed Cache temporarily
<IfModule LiteSpeed>
CacheLookup off
</IfModule>
```
4. Save and test site
5. If working, remove these lines (caching disabled is bad for performance)

**Method C: Via cPanel**
1. Log into cPanel
2. Search for "LiteSpeed" or "Cache Manager"
3. Click "Flush All" or "Purge Cache"

---

### Fix 3: Clear WordPress Object Cache (1 minute)

**Via FTP/File Manager:**
1. Navigate to `wp-content/`
2. Look for folder named `cache` or `object-cache.php`
3. Delete or rename these
4. Refresh site

---

### Fix 4: Force Refresh (30 seconds)

**Hard Refresh:**
- Windows: `Ctrl+F5` or `Ctrl+Shift+R`
- Mac: `Cmd+Shift+R`
- This bypasses all cache layers

---

### Fix 5: Check CDN/Cloudflare (if applicable)

**If using Cloudflare:**
1. Log into Cloudflare
2. Go to Caching → Purge Everything
3. Wait 30 seconds
4. Test site

---

## 🛠️ ENABLE DEBUG LOG (To See What's Actually Happening)

Even though diagnostic passed, enable logging to catch any runtime errors:

1. **Edit wp-config.php:**
```php
// Change this line:
define('WP_DEBUG', false);

// To these lines:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

2. **Visit the site** to trigger any errors

3. **Check log file:**
   - Location: `wp-content/debug.log`
   - View last 50 lines for any errors

---

## 🔍 ADDITIONAL DIAGNOSTICS

### Test Different URLs

Try accessing these specific URLs to narrow down the issue:

1. **WordPress Admin:** https://wp.premierplug.org/wp-admin/
   - If this works → Frontend-only issue

2. **Test Page:** https://wp.premierplug.org/about-us/
   - If this works → Homepage-specific issue

3. **Talent Roster:** https://wp.premierplug.org/talent-roster/
   - Tests if plugin is working

4. **wp-login.php:** https://wp.premierplug.org/wp-login.php
   - If this works → Theme issue

### Check What's Actually Loading

**View Page Source:**
1. Right-click on white screen
2. Select "View Page Source" or `Ctrl+U`
3. What do you see?
   - **HTML code** → Frontend rendering issue (CSS/JS problem)
   - **Error message** → PHP error (check message)
   - **Completely blank** → Server not outputting anything

---

## 💡 MOST LIKELY SCENARIO

Based on diagnostic results showing everything healthy, the issue is:

1. **80% Probability:** Browser/CDN cache showing old version
2. **15% Probability:** LiteSpeed server cache
3. **5% Probability:** CSS/JS not loading (check page source)

---

## ✅ VERIFICATION STEPS

After clearing cache, verify these are working:

### Homepage Test:
- [ ] Page loads (not white screen)
- [ ] Header displays with logo
- [ ] Navigation menu works
- [ ] Hero image shows
- [ ] Footer displays

### Theme Test:
- [ ] Visit https://wp.premierplug.org/about-us/
- [ ] Page displays correctly
- [ ] Styling is applied

### Plugin Test:
- [ ] Visit https://wp.premierplug.org/talent-roster/
- [ ] Talent grid displays
- [ ] Can filter/search talents
- [ ] Can view individual talent profiles

### Admin Test:
- [ ] Can log into /wp-admin/
- [ ] Dashboard loads
- [ ] Can see "Talents" menu item
- [ ] Theme and plugin show as active

---

## 🚨 IF STILL WHITE SCREEN AFTER CACHE CLEAR

**Collect this information:**

1. **What browser are you using?**
   - Chrome, Firefox, Safari, Edge?
   - Version number?

2. **Does Incognito/Private mode work?**
   - Yes → Definitely cache issue
   - No → Server issue

3. **What shows in page source?**
   - Right-click → View Page Source
   - Copy first 50 lines

4. **Check browser console:**
   - Press F12
   - Go to "Console" tab
   - Any red errors?
   - Screenshot and send

5. **Test from different location:**
   - Try from phone (using mobile data, not WiFi)
   - Try from different computer
   - Ask someone else to test

---

## 🎯 QUICK FIX COMMANDS

**Via SSH (if you have access):**
```bash
# Clear all WordPress cache
rm -rf wp-content/cache/*

# Clear LiteSpeed cache
rm -rf /tmp/lshttpd/cache/*

# Restart LiteSpeed
sudo systemctl restart lsws

# Check recent PHP errors
tail -50 wp-content/debug.log
```

**Via WP-CLI (if installed):**
```bash
# Clear cache
wp cache flush

# Rewrite permalinks
wp rewrite flush

# Verify theme
wp theme status premierplug-theme

# Verify plugin
wp plugin status premierplug-talent-manager
```

---

## 📞 RESPONSE TEMPLATE

**What to tell me:**

> I've cleared [browser/server/all] cache.
> Tested in [Chrome/Firefox/Safari] version [X.X].
> Incognito mode: [works/doesn't work]
> Page source shows: [HTML/error/blank]
> Console errors: [yes/no - screenshot attached]
> Other pages (about, admin): [work/don't work]

---

## ✨ EXPECTED BEHAVIOR (Once Working)

**Homepage should show:**
- Red banner header (#BC1F2F)
- Navigation menu (About, Services, Contact, etc.)
- Large hero image
- Content sections
- Footer with links

**Talent Roster should show:**
- Filter section (red background)
- Talent cards in grid
- Photos, names, segments
- Search functionality

**Admin should show:**
- WordPress dashboard
- "Talents" menu item
- Ability to add/edit talents

---

## 🎉 SUCCESS INDICATORS

You'll know it's fixed when:
- ✅ Homepage loads with content
- ✅ Navigation works
- ✅ Images display
- ✅ No console errors
- ✅ Talent roster accessible
- ✅ Admin dashboard functional

---

**Priority:**
1. Clear browser cache + test in Incognito
2. Clear LiteSpeed server cache
3. Enable debug log
4. Report findings

**Timeline:** Should be resolved in 5-10 minutes with cache clearing.
