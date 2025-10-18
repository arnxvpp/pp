# 🚀 Quick Deployment Checklist

## Before You Start
- [ ] Have FTP/hosting access ready
- [ ] Know your WordPress admin login
- [ ] Have Supabase credentials ready

---

## Upload Plugin (Choose ONE Method)

### ⭐ Option A: FTP Upload (Easiest)
1. [ ] Connect to your hosting via FTP (FileZilla)
2. [ ] Navigate to `/public_html/wp-content/plugins/`
3. [ ] Upload entire `premierplug-talent-manager` folder
4. [ ] Wait for all files to transfer (19 files)
5. [ ] Set folder permissions to 755

### Option B: File Manager (cPanel/Plesk)
1. [ ] Log in to hosting control panel
2. [ ] Open File Manager
3. [ ] Navigate to `public_html/wp-content/plugins/`
4. [ ] Create folder: `premierplug-talent-manager`
5. [ ] Upload all plugin files inside
6. [ ] Set permissions to 755

### Option C: ZIP Upload (WordPress)
1. [ ] Create ZIP of `premierplug-talent-manager` folder
2. [ ] WordPress Admin > Plugins > Add New > Upload Plugin
3. [ ] Choose file and upload
4. [ ] Click Install Now

---

## Verify Supabase Config
- [ ] Check `.env` file exists in WordPress root (not in wp-content)
- [ ] Verify it contains:
  ```
  VITE_SUPABASE_URL=https://mdniuqoqqbcvlvldfskj.supabase.co
  VITE_SUPABASE_SUPABASE_ANON_KEY=your_key
  ```
- [ ] No quotes, no extra spaces

---

## Activate Plugin
1. [ ] Go to WordPress Admin > Plugins
2. [ ] Find "PremierPlug Talent Manager"
3. [ ] Click **Activate**
4. [ ] See "Talents" menu in sidebar
5. [ ] No error messages

---

## CRITICAL: Flush Permalinks
1. [ ] Go to **Settings > Permalinks**
2. [ ] Click **Save Changes** (don't change anything)
3. [ ] This makes URLs work!

---

## Verify Installation

### Check Settings
- [ ] Go to **Talents > Settings**
- [ ] See: "✓ Supabase is configured and connected"

### Create Test Talent
- [ ] Go to **Talents > Add New**
- [ ] Enter name: "Test Talent"
- [ ] Add biography text
- [ ] Upload featured image
- [ ] Fill in headline
- [ ] Select segment: "Digital Media"
- [ ] Click **Publish**
- [ ] No errors

### Test Frontend
- [ ] Visit: `yoursite.com/talent-roster/`
- [ ] Page loads (no 404 error)
- [ ] See your test talent
- [ ] Filters appear
- [ ] Design looks good

### Test Single Profile
- [ ] Click on talent card
- [ ] Profile page loads
- [ ] Biography displays
- [ ] Contact form shows
- [ ] Everything styled correctly

### Test Filtering
- [ ] Go back to `/talent-roster/`
- [ ] Type in search box
- [ ] Check segment filter
- [ ] Results update without page reload

### Test Form Submission
- [ ] On talent profile, scroll to contact form
- [ ] Fill in all required fields
- [ ] Click Submit
- [ ] See success message
- [ ] Check email for notification

### Check Analytics
- [ ] Go to **Talents > Analytics**
- [ ] See dashboard with stats
- [ ] Shows "1 Total Talents"
- [ ] Shows profile views

---

## If Something Doesn't Work

### 404 Error on talent pages?
→ Go to Settings > Permalinks > Save Changes

### "Supabase not configured" warning?
→ Check .env file format and location

### Images not showing?
→ Check featured images are set and uploads folder permissions

### Filters not working?
→ Press F12, check Console for errors, disable other plugins

### Form not submitting?
→ Check Supabase connection, verify jQuery loaded

### Plugin won't activate?
→ Check PHP version (needs 7.4+), check error logs

---

## Post-Deployment Tasks

### Content
- [ ] Create 3-5 talents for Digital Media
- [ ] Create 3-5 talents for Television
- [ ] Create 3-5 talents for Voiceovers
- [ ] Create 3-5 talents for Speakers
- [ ] Create 3-5 talents for Motion Pictures

### Integration
- [ ] Add shortcode to Digital Media page: `[talent_grid segment="digital-media"]`
- [ ] Add shortcode to homepage: `[featured_talents count="6"]`
- [ ] Update navigation menu with talent links
- [ ] Link from service pages to talent segments

### Optimization
- [ ] Install caching plugin (WP Super Cache)
- [ ] Set up automated backups (UpdraftPlus)
- [ ] Add Google Analytics tracking
- [ ] Optimize images before uploading
- [ ] Test on mobile devices

---

## Success Indicators ✅

Your deployment is successful when:
- ✅ Plugin activated without errors
- ✅ `/talent-roster/` page loads
- ✅ Can create talent profiles
- ✅ Filtering works in real-time
- ✅ Forms submit successfully
- ✅ Supabase shows "connected"
- ✅ Analytics tracking works
- ✅ Design matches your site perfectly

---

## Quick Reference URLs

After deployment, these pages work:
- All Talents: `/talent-roster/`
- Digital Media: `/talent-segment/digital-media/`
- Television: `/talent-segment/television/`
- Voiceovers: `/talent-segment/voiceovers/`
- Speakers: `/talent-segment/speakers/`
- Motion Pictures: `/talent-segment/motion-pictures/`
- Single Talent: `/talent/john-doe/`

---

## Need Help?

1. Read DEPLOYMENT-GUIDE.md (detailed troubleshooting)
2. Check WordPress Admin notices
3. Enable WP_DEBUG to see errors
4. Check browser Console (F12)
5. Verify Supabase dashboard shows data

---

## 🎉 Done!

When all checkboxes are complete, your talent management system is live!

**Total Time:** 15-30 minutes
**Difficulty:** Easy to Medium
**Result:** Professional talent roster on your site

---

**Quick Start Command (after upload):**
1. Activate plugin
2. Settings > Permalinks > Save
3. Create first talent
4. Visit /talent-roster/
5. Success! 🎉
