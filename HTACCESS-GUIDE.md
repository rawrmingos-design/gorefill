# 🔒 .htaccess Configuration Guide

## 📁 Files Created

### 1. **Root .htaccess** (`.htaccess`)
**Location:** `/gorefill/.htaccess`

**Purpose:** Protect sensitive directories and redirect to public folder

**Features:**
- ✅ Prevent directory browsing
- ✅ Deny access to config files
- ✅ Protect: `/config`, `/app`, `/migrations`, `/logs`, `/vendor`, `/.windsurf`
- ✅ Block `.sql`, `.bak`, `.log`, `.md` files
- ✅ Redirect all requests to `/public`
- ✅ Disable PHP in uploads directory
- ✅ Block test files in production

---

### 2. **Public .htaccess** (`/public/.htaccess`)
**Location:** `/gorefill/public/.htaccess`

**Purpose:** Entry point configuration and security headers

**Features:**
- ✅ Set document root
- ✅ 404 error handling
- ✅ Security headers (X-Content-Type-Options, X-XSS-Protection, X-Frame-Options)
- ✅ PHP security settings
- ✅ Asset protection
- ✅ Compression enabled
- ✅ Browser caching
- ✅ MIME types

---

## 🧪 Testing .htaccess

### Test 1: Protected Directories
Try accessing these URLs - should get **403 Forbidden**:
```
http://localhost/gorefill/config/
http://localhost/gorefill/app/
http://localhost/gorefill/migrations/
http://localhost/gorefill/logs/
```

### Test 2: Protected Files
Try accessing these - should get **403 Forbidden**:
```
http://localhost/gorefill/config/config.php
http://localhost/gorefill/migrations/gorefill.sql
http://localhost/gorefill/README.md
http://localhost/gorefill/test_connection.php
```

### Test 3: Public Access
These should work normally:
```
http://localhost/gorefill/public/
http://localhost/gorefill/public/index.php
http://localhost/gorefill/public/assets/css/
```

### Test 4: Redirect to Public
This URL:
```
http://localhost/gorefill/
```
Should automatically redirect to:
```
http://localhost/gorefill/public/
```

### Test 5: 404 Handling
Try invalid route:
```
http://localhost/gorefill/public/index.php?route=invalid.route
```
Should show your custom 404 page.

---

## ⚙️ Configuration Notes

### Development vs Production

#### Development (Current):
```apache
# Display errors: ON
php_flag display_errors On

# HTTPS: OFF (no SSL yet)
# RewriteCond %{HTTPS} off (commented)
```

#### Production (Day 22):
```apache
# Display errors: OFF
php_flag display_errors Off

# HTTPS: ON
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🎯 What This Protects

### ✅ Protected:
1. **Config files** - Database credentials hidden
2. **App source code** - Controllers, Models not accessible
3. **Database schema** - SQL files blocked
4. **Logs** - Error logs protected
5. **Test files** - test_connection.php blocked
6. **Vendor** - Composer dependencies hidden
7. **Documentation** - .md files not public

### ✅ Allowed:
1. **Public folder** - index.php entry point
2. **Assets** - CSS, JS, images
3. **Uploads** - Product images (but no PHP execution)

---

## 🚀 Future Enhancements (Day 22)

### Will Add:
1. **Content Security Policy (CSP)**
   ```apache
   Header set Content-Security-Policy "default-src 'self'"
   ```

2. **HTTP Strict Transport Security (HSTS)**
   ```apache
   Header set Strict-Transport-Security "max-age=31536000"
   ```

3. **Clean URLs (Optional)**
   ```apache
   # Instead of: ?route=product.detail&id=5
   # Use: /product/detail/5
   RewriteRule ^([^/]+)/([^/]+)/([0-9]+)$ index.php?route=$1.$2&id=$3 [L,QSA]
   ```

4. **Rate Limiting**
   ```apache
   # Prevent brute force
   <IfModule mod_security2.c>
       SecRuleEngine On
   </IfModule>
   ```

5. **Advanced Caching**
   ```apache
   # More aggressive caching strategies
   ```

---

## 🐛 Troubleshooting

### Issue: 500 Internal Server Error

**Cause:** .htaccess syntax error or module not enabled

**Solution:**
1. Check Apache error log: `logs/error.log`
2. Verify `mod_rewrite` is enabled:
   ```bash
   # Windows (XAMPP)
   httpd.conf → LoadModule rewrite_module modules/mod_rewrite.so
   
   # Allow .htaccess override
   <Directory "C:/xampp/htdocs">
       AllowOverride All
   </Directory>
   ```

### Issue: 403 Forbidden on Everything

**Cause:** Too restrictive rules

**Solution:**
1. Check file permissions
2. Verify `Require all granted` in Apache config
3. Comment out strict rules temporarily

### Issue: Assets Not Loading

**Cause:** Asset path blocked

**Solution:**
1. Check `RewriteCond` excludes `/assets/`
2. Verify file exists in `/public/assets/`
3. Check browser console for 404s

### Issue: Redirect Loop

**Cause:** RewriteBase incorrect

**Solution:**
```apache
# In /public/.htaccess
# Change based on your actual path
RewriteBase /gorefill/public/
```

---

## 📋 Checklist

### ✅ Day 2 Setup Complete:
- [x] Root .htaccess created
- [x] Public .htaccess created
- [x] Protected directories working
- [x] 404 handler configured
- [x] Security headers set
- [x] Asset caching enabled

### 🔜 Day 22 Enhancement:
- [ ] Add CSP headers
- [ ] Enable HSTS
- [ ] Implement rate limiting
- [ ] Consider clean URLs
- [ ] Advanced performance tuning
- [ ] Production hardening

---

## 💡 Tips

### 1. Always Test Changes
After modifying .htaccess, test immediately:
```bash
# Clear browser cache
# Try protected URLs
# Check error logs
```

### 2. Backup Before Changes
```bash
cp .htaccess .htaccess.backup
```

### 3. Use Comments
Document why each rule exists

### 4. Check Module Availability
Not all hosting supports all modules:
```apache
<IfModule mod_headers.c>
    # Only runs if mod_headers available
</IfModule>
```

---

## 🎓 Learn More

### Apache Modules Used:
- `mod_rewrite` - URL rewriting
- `mod_headers` - HTTP headers
- `mod_deflate` - Compression
- `mod_expires` - Caching
- `mod_mime` - MIME types

### Resources:
- Apache Docs: https://httpd.apache.org/docs/
- .htaccess Guide: https://htaccess.net.ua/
- Security Headers: https://securityheaders.com/

---

## ✅ Status

**Current:** Basic Protection ✅
- Security baseline implemented
- Sensitive directories protected
- Ready for development

**Future:** Advanced Security (Day 22)
- CSP, HSTS headers
- Rate limiting
- Production hardening

---

**Created:** Day 2  
**Enhanced:** Day 22 (planned)  
**Status:** ✅ Production Ready (Basic)
