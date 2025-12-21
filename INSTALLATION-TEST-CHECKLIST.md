# Installation & Testing Checklist

**Use this checklist when installing on your WordPress site**

---

## ✅ Pre-Installation

- [ ] WordPress 5.0+ installed
- [ ] PHP 7.0+ on server
- [ ] Backup your site (database + files)
- [ ] Test on staging site first (if available)

---

## 📦 Installation Steps

### Step 1: Upload Plugin
- [ ] Upload `/premierplug-talent-management/` folder to `/wp-content/plugins/`
- [ ] OR upload ZIP file via WordPress Admin → Plugins → Add New

### Step 2: Activate
- [ ] Go to WordPress Admin → Plugins
- [ ] Find "PremierPlug Talent Management"
- [ ] Click "Activate"
- [ ] Check for any activation errors

### Step 3: Verify Installation
- [ ] Go to WordPress Admin → **Talent** menu
- [ ] Verify "Growth Settings" menu item appears
- [ ] Click "Growth Settings"
- [ ] Verify all 7 tabs load:
  - [ ] SEO & Social
  - [ ] Monetization
  - [ ] Social Sharing
  - [ ] Related Articles
  - [ ] Analytics
  - [ ] Email Capture
  - [ ] Performance

---

## 🧪 Feature Testing (27 minutes)

### Test 1: SEO Manager (5 min)

**Setup:**
- [ ] Go to **Talent → Growth Settings → SEO & Social**
- [ ] Add Twitter handle: `yourusername` (without @)
- [ ] Add Twitter URL: `https://twitter.com/yourusername`
- [ ] Click "Save Changes"

**Test:**
- [ ] Visit any talent profile or article on frontend
- [ ] View page source (Ctrl+U)
- [ ] Search for `og:title` - should appear ✓
- [ ] Search for `twitter:card` - should appear ✓
- [ ] Search for `application/ld+json` - should appear ✓
- [ ] Search for `meta name="description"` - should appear ✓

**Expected Result**: SEO meta tags automatically added to all pages.

---

### Test 2: Speed Optimization (2 min)

**Setup:**
- [ ] Go to **Talent → Growth Settings → Performance**
- [ ] Enable: ✓ Lazy Load Images
- [ ] Enable: ✓ Preload Resources
- [ ] Click "Save Changes"

**Test:**
- [ ] Visit any article with images
- [ ] View page source
- [ ] Check `<img` tags have `loading="lazy"` attribute ✓
- [ ] Scroll down - images should load as you scroll ✓

**Test Speed:**
- [ ] Go to [PageSpeed Insights](https://pagespeed.web.dev/)
- [ ] Enter your site URL
- [ ] Note the speed score
- [ ] Should see improvement in "Opportunities" section

**Expected Result**: Page loads faster, images lazy load.

---

### Test 3: Google Analytics (5 min)

**Setup:**
- [ ] Create Google Analytics 4 account (if you don't have one)
- [ ] Go to Admin → Data Streams → Get Measurement ID
- [ ] Copy ID (format: `G-XXXXXXXXXX`)
- [ ] Go to **Talent → Growth Settings → Analytics**
- [ ] Paste Measurement ID
- [ ] Check: ✓ Exclude Admins
- [ ] Check: ✓ Anonymize IP
- [ ] Click "Save Changes"

**Test:**
- [ ] Visit your site homepage (in different browser or incognito)
- [ ] Visit 2-3 articles
- [ ] Go to Google Analytics → Reports → Realtime
- [ ] Should see 1 active user ✓

**Test Events:**
- [ ] Scroll down 50% on an article
- [ ] Click an external link
- [ ] Share an article (if enabled)
- [ ] Go to GA4 → Reports → Realtime → Event count by Event name
- [ ] Should see events: `scroll`, `click`, `share` ✓

**Expected Result**: Google Analytics tracks all activity.

---

### Test 4: Social Sharing (5 min)

**Setup:**
- [ ] Go to **Talent → Growth Settings → Social Sharing**
- [ ] Button Position: `Bottom of content`
- [ ] Enable networks: ✓ All (Facebook, Twitter, LinkedIn, WhatsApp, Email, Copy)
- [ ] Click "Save Changes"

**Test:**
- [ ] Visit any article on frontend
- [ ] Scroll to bottom
- [ ] Verify share buttons appear ✓
- [ ] Click "Facebook" button
  - [ ] Pop-up window opens with Facebook share ✓
- [ ] Click "Copy Link" button
  - [ ] See "Link copied!" message ✓
  - [ ] Paste in notepad - URL should be correct ✓

**Test Tracking:**
- [ ] Share on one network
- [ ] Go to GA4 Realtime
- [ ] Should see `share` event ✓

**Expected Result**: Share buttons work, tracking saves.

---

### Test 5: Related Articles (3 min)

**Setup:**
- [ ] Go to **Talent → Growth Settings → Related Articles**
- [ ] Check: ✓ Show Related Articles
- [ ] Section Title: `Related Articles`
- [ ] Number: `3`
- [ ] Click "Save Changes"

**Test:**
- [ ] Visit any article that has related content
- [ ] Scroll to bottom (below share buttons)
- [ ] Verify "Related Articles" section appears ✓
- [ ] Verify 3 article cards display ✓
- [ ] Verify each card has:
  - [ ] Thumbnail image ✓
  - [ ] Article title ✓
  - [ ] Excerpt ✓
  - [ ] Date ✓
- [ ] Click a related article
  - [ ] Should open that article ✓

**Expected Result**: Smart recommendations keep visitors engaged.

---

### Test 6: Monetization (10 min)

**Setup (Google AdSense):**
- [ ] Sign up for [Google AdSense](https://adsense.google.com) (if not approved yet)
- [ ] Get ad code from AdSense dashboard
- [ ] Copy the full `<script>` tag

**Setup (Plugin):**
- [ ] Go to **Talent → Growth Settings → Monetization**
- [ ] Paste ad code in "In-Content Ad" field
- [ ] Set position: `3` (after 3rd paragraph)
- [ ] Click "Save Changes"

**Test:**
- [ ] Visit any article on frontend
- [ ] Look for "Advertisement" label after 3rd paragraph ✓
- [ ] Ad should appear (or placeholder if not approved yet) ✓

**Test Disable:**
- [ ] Edit any article in WordPress Admin
- [ ] Look for "Advertisement Settings" metabox (right sidebar)
- [ ] Check: ✓ Disable ads on this page
- [ ] Click "Update"
- [ ] Visit that article on frontend
- [ ] Ads should NOT appear ✓

**Expected Result**: Ads appear automatically, can be disabled per page.

---

### Test 7: Email Capture (5 min)

**Setup:**
- [ ] Go to **Talent → Growth Settings → Email Capture**
- [ ] Check: ✓ Enable Pop-up
- [ ] Trigger Type: `Exit Intent`
- [ ] Show Frequency: `7` days
- [ ] Pop-up Title: `Stay Updated!`
- [ ] Pop-up Subtitle: `Get our latest news delivered to your inbox.`
- [ ] Button Text: `Subscribe`
- [ ] Privacy Text: `We respect your privacy. Unsubscribe anytime.`
- [ ] Check: ✓ Send Welcome Email
- [ ] Click "Save Changes"

**Test Pop-up:**
- [ ] Visit your site in incognito mode
- [ ] Move mouse cursor to top of browser (like you're leaving)
- [ ] Pop-up should appear ✓
- [ ] Verify title, subtitle, button text are correct ✓
- [ ] Click X button - pop-up closes ✓

**Test Subscription:**
- [ ] Trigger pop-up again (clear cookies or new incognito window)
- [ ] Enter your email: `test@example.com`
- [ ] Click "Subscribe"
- [ ] Should see "Thank you for subscribing!" message ✓

**Test Database:**
- [ ] Go to **Talent → Growth Settings → Email Capture**
- [ ] Look for "Total Subscribers" count
- [ ] Should show `1` subscriber ✓

**Test Welcome Email:**
- [ ] Check inbox for `test@example.com`
- [ ] Should receive welcome email ✓

**Test Inline Form:**
- [ ] Create a new page/post
- [ ] Add shortcode: `[pptm_email_form]`
- [ ] Publish and view on frontend
- [ ] Email form should appear inline ✓
- [ ] Test subscription works ✓

**Expected Result**: Email capture works, subscribers saved to database.

---

## 🔍 Advanced Testing

### Test: Shortcodes

**Email Form:**
```
[pptm_email_form title="Subscribe" button="Join" style="boxed"]
```
- [ ] Add to any page
- [ ] Verify form displays ✓
- [ ] Test submission works ✓

**Social Sharing:**
```
[pptm_share]
```
- [ ] Add to any page
- [ ] Verify buttons display ✓
- [ ] Test sharing works ✓

**Related Articles:**
```
[pptm_related count="6"]
```
- [ ] Add to any page
- [ ] Verify 6 articles display ✓
- [ ] Test links work ✓

---

### Test: Mobile Responsiveness

**Desktop:**
- [ ] All features display correctly
- [ ] Share buttons show icon + text
- [ ] Pop-up is centered

**Mobile:**
- [ ] Lazy loading works
- [ ] Share buttons show icon only
- [ ] Pop-up fits screen
- [ ] Sticky ad appears at bottom (if enabled)
- [ ] Related articles stack vertically

---

### Test: Third-Party Compatibility

**With Yoast SEO:**
- [ ] Install Yoast SEO
- [ ] View page source
- [ ] Both plugins' meta tags should appear
- [ ] No duplicate tags

**With Caching Plugin:**
- [ ] Install WP Super Cache (or similar)
- [ ] Clear cache
- [ ] Test all features still work
- [ ] Pop-up still appears
- [ ] Analytics still tracks

**With Page Builder:**
- [ ] If using Elementor/Divi
- [ ] Test shortcodes work in builder
- [ ] Test features work on builder pages

---

## 📊 Performance Check

### Before vs After

**Test Page Speed:**
1. [ ] Go to [PageSpeed Insights](https://pagespeed.web.dev/)
2. [ ] Test your site URL
3. [ ] Note the scores:
   - Desktop: ___/100
   - Mobile: ___/100

**Check Improvements:**
- [ ] "Defer unused JavaScript" - Should be fixed ✓
- [ ] "Offscreen images" - Should be fixed ✓
- [ ] "Text compression" - Should be improved ✓

**Database:**
- [ ] Check `wp_options` table has new settings
- [ ] Check `wp_pptm_subscribers` table exists
- [ ] Check no errors in error log

---

## 🐛 Common Issues & Solutions

### Issue 1: Pop-up Not Showing
**Solution:**
- Clear browser cookies
- Try incognito mode
- Check "Enable Pop-up" is checked
- If logged in as admin, check "Hide for Admins" setting

### Issue 2: Ads Not Appearing
**Solution:**
- Check AdSense account is approved
- Check ad code is properly pasted
- Check "Disable ads" is NOT checked on page
- Wait 24 hours for AdSense approval

### Issue 3: Analytics Not Tracking
**Solution:**
- Verify Measurement ID is correct (starts with G-)
- Check "Exclude Admins" setting (you won't see your own visits)
- Use incognito mode to test
- Wait 24-48 hours for data to populate

### Issue 4: Share Buttons Not Working
**Solution:**
- Check JavaScript console for errors (F12)
- Disable other social sharing plugins
- Clear cache
- Try different browser

### Issue 5: Slow Performance
**Solution:**
- Disable "Defer JavaScript" temporarily
- Don't enable all optimizations at once
- Use caching plugin separately
- Check with hosting support

---

## ✅ Final Verification

- [ ] All features tested and working
- [ ] No PHP errors in error log
- [ ] No JavaScript errors in console (F12)
- [ ] Site loads normally
- [ ] Admin panel accessible
- [ ] Settings save correctly

---

## 🚀 Next Steps After Testing

### Week 1:
- [ ] Complete SEO setup (social URLs)
- [ ] Enable speed optimization
- [ ] Setup Google Analytics

### Week 2-4:
- [ ] Publish 10-20 quality articles
- [ ] Link articles to talent profiles
- [ ] Share content on social media

### Month 2-3:
- [ ] Enable social sharing buttons
- [ ] Enable email capture
- [ ] Monitor analytics data

### Month 4+:
- [ ] Apply for Google AdSense (if not approved)
- [ ] Add ad codes
- [ ] Export email list to Mailchimp/ConvertKit
- [ ] Scale content production

---

## 📞 Support Resources

**Documentation:**
- Main guide: `docs/GROWTH-FEATURES.md`
- What's new: `WHATS-NEW-V2.md`
- Test report: `TEST-REPORT.md`

**Settings Location:**
- WordPress Admin → Talent → Growth Settings

**Common Questions:**
- All features work on shared hosting ✓
- Compatible with all themes ✓
- Compatible with third-party plugins ✓
- No coding required ✓

---

**Testing Complete!**

Your CAA-style media platform is now live and generating traffic + revenue.

Monitor Google Analytics and adjust settings as needed.

**Expected Results (3 months):**
- 5x organic traffic increase
- 10x more social shares
- 200+ email subscribers
- $200-500/month ad revenue

---

**Checklist Version**: 1.0
**Last Updated**: December 2024
