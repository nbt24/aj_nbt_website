# NBT Website Optimization Guide
## Simple Steps for Non-Technical Teams

### PHASE 1: IMMEDIATE FIXES (5 minutes)

#### 1. Image Optimization (Biggest Impact!)
**Problem**: Large images slow down the website
**Simple Solution**:
- Before uploading ANY image, compress it first
- Use free online tools:
  - TinyPNG.com (easiest to use)
  - Compressor.io
  - Optimizilla.com

**Steps**:
1. Go to TinyPNG.com
2. Upload your image
3. Download the compressed version
4. Use the compressed image in admin panel
5. **Target**: Keep images under 500KB each

#### 2. Clean Browser Data Regularly
**Problem**: Old cached data slows things down
**Simple Solution**:
- Clear browser cache weekly
- Press Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac)
- Select "All time" and check all boxes
- Click "Clear data"

#### 3. Run Cache Cleanup Monthly
**Simple Solution**:
- Visit: yourwebsite.com/cache/cleanup.php
- Click refresh
- This removes old cached files automatically

### PHASE 2: CONTENT MANAGEMENT (15 minutes)

#### 4. Limit Number of Items Per Section
**Current Recommendations**:
- Team Members: Maximum 8-10 visible
- Courses: Maximum 6-8 per page
- Testimonials: Maximum 10-12 active
- Services: Maximum 6-8 featured

**Why**: Too many items = slower loading

#### 5. Optimize Text Content
**Simple Rules**:
- Keep descriptions under 150 words
- Use bullet points instead of long paragraphs
- Remove duplicate or outdated content

#### 6. Video Management
**Current Status**: Videos are optimized ✅
**Maintenance**: 
- Keep video descriptions short
- Remove videos that are no longer relevant

### PHASE 3: ADMIN PANEL BEST PRACTICES (10 minutes)

#### 7. Image Upload Guidelines
**For Team Photos**:
- Resolution: 400x400 pixels maximum
- Format: JPG (preferred) or PNG
- Size: Under 200KB each

**For Course Images**:
- Resolution: 600x400 pixels maximum
- Format: JPG preferred
- Size: Under 300KB each

**For Company Logos**:
- Resolution: 200x200 pixels maximum
- Format: PNG with transparent background
- Size: Under 100KB each

#### 8. Database Maintenance
**Simple Monthly Tasks**:
- Remove test entries
- Delete outdated testimonials
- Archive old courses (don't delete, just mark inactive)

### PHASE 4: MONITORING (5 minutes weekly)

#### 9. Performance Check
**Simple Steps**:
1. Visit: yourwebsite.com/performance_monitor.php
2. Check if numbers are GREEN
3. If RED numbers appear, run cache cleanup
4. If still RED, compress more images

**Good Numbers to See**:
- Execution Time: Under 100ms (GREEN)
- Memory Usage: Under 5MB (GREEN)
- Cache: Any number of files is good

#### 10. User Experience Check
**Weekly Test**:
1. Visit your website on mobile phone
2. Check if it loads in under 3 seconds
3. Test all major sections (About, Courses, Contact)
4. If slow, compress recent images you uploaded

### PHASE 5: SIMPLE ONGOING HABITS

#### 11. Image Upload Workflow
**New Process** (teach to all admin users):
1. Take/receive image
2. Go to TinyPNG.com
3. Compress image
4. Download result
5. THEN upload to admin panel

#### 12. Content Update Guidelines
**Best Practices**:
- Update 1-2 sections at a time (not everything at once)
- Test website speed after major updates
- Keep backup of working content before big changes

#### 13. Browser Compatibility
**Simple Check**:
- Test website in Chrome, Firefox, and Safari
- Test on mobile phone regularly
- If something looks broken, clear cache first

### EMERGENCY TROUBLESHOOTING

#### If Website Becomes Slow:
1. **First**: Run cache cleanup (/cache/cleanup.php)
2. **Second**: Check recent image uploads - compress them
3. **Third**: Clear your browser cache
4. **Fourth**: Check performance monitor

#### If Admin Panel is Slow:
1. Clear browser cache
2. Check if you uploaded large images recently
3. Compress and re-upload those images

#### If Images Don't Load:
1. Check image file size (should be under 500KB)
2. Try different image format (JPG instead of PNG)
3. Clear cache and refresh page

### TOOLS YOU NEED (All Free):

1. **TinyPNG.com** - Image compression
2. **Your website/performance_monitor.php** - Speed checking
3. **Your website/cache/cleanup.php** - Cache cleaning
4. **Browser Developer Tools** (F12 key) - Basic debugging

### MONTHLY CHECKLIST:

□ Run cache cleanup
□ Check performance monitor
□ Compress any large images
□ Remove outdated content
□ Test website on mobile
□ Clear team browser caches

### RED FLAGS (When to Get Help):

- Performance monitor shows RED numbers consistently
- Website takes more than 5 seconds to load
- Images frequently fail to display
- Admin panel becomes unresponsive
- Error messages appear regularly

### SAFE PRACTICES:

✅ Always compress images before upload
✅ Test changes on one section first
✅ Keep content concise and relevant
✅ Run monthly maintenance tasks
✅ Monitor performance weekly

❌ Don't upload images larger than 500KB
❌ Don't make multiple major changes at once
❌ Don't ignore RED performance warnings
❌ Don't let cache grow without cleanup
❌ Don't skip mobile testing

---

**Remember**: Small, consistent improvements are better than big complex changes!
