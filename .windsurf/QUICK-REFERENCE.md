# 🚀 QUICK REFERENCE GUIDE

## 📌 How to Use This System

1. **Open** `AI-ORCHESTRATION-MASTER.md` for project overview
2. **Find** your current week file (WEEK-01 through WEEK-05)
3. **Copy** the specific day's prompt
4. **Paste** to your AI assistant
5. **Use Context7** for documentation: `@context7 <library> <topic>`

---

## 📋 Phases Overview

### Phase 1: MVP (Weeks 1-2)
Auth, Products, Cart, Payment

### Phase 2: Advanced (Weeks 3-4)
Maps, Tracking, Reviews, Analytics, Email

### Phase 3: Production (Week 5)
Security, Performance, Deployment

---

## 🔧 Essential Commands

```bash
# Start dev server
php -S localhost:8000 -t public/

# Install dependencies
composer install
composer require midtrans/midtrans-php
composer require phpmailer/phpmailer

# Database
mysql -u root -p gorefill_db < migrations/gorefill.sql
```

---

## 🎯 Daily Workflow

1. READ today's prompt
2. RESEARCH with Context7
3. IMPLEMENT features
4. TEST functionality
5. MARK deliverables ✅

---

## 🔑 Core Rules

- **MVC Architecture:** No SQL in Views, no HTML in Models
- **Security:** PDO prepared statements, password_hash(), htmlspecialchars()
- **Maps:** Leaflet.js only (not Google Maps)
- **Payment:** Midtrans API only
- **Auth:** Session-based ($_SESSION)

---

## 📦 Project Structure

```
/gorefill
├── /app (Controllers, Models, Views, Helpers, Services)
├── /config (config.php, midtrans.php, mail.php)
├── /public (index.php, /assets)
├── /uploads (/products)
├── /migrations (gorefill.sql)
└── .windsurf (AI orchestration files)
```

---

## 🐛 Quick Troubleshooting

**Database:** Check PDO connection in bootstrap.php
**Session:** Ensure session_start() called first
**Upload:** Check chmod 755 on /uploads
**Midtrans:** Verify server_key in config
**Leaflet:** Map container needs height CSS

---

## 📊 Progress Tracking

**Phase 1:** [ ] Week 1 | [ ] Week 2
**Phase 2:** [ ] Week 3 | [ ] Week 4
**Phase 3:** [ ] Week 5

---

**Files:** 5 weekly prompt files + master index + this guide
**Total Days:** 25 days structured development
**Use Context7:** For real-time documentation
