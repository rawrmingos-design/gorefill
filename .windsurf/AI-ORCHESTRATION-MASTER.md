# 🤖 AI ORCHESTRATION SYSTEM - GoRefill Project

## 📖 MASTER INDEX & PROJECT CONTEXT

### How to Use This System
1. **Start with this file** to understand project context
2. **Navigate to weekly prompt files** for detailed day-by-day instructions
3. **Copy the specific day's prompt** and paste to your AI assistant
4. **AI will understand** exactly what to build, dependencies, and deliverables

---

## 🎯 PROJECT OVERVIEW

**GoRefill** - PHP Native E-Commerce for Refill Services (Water, LPG, Household Products)

### Tech Stack
- **Backend:** PHP 8.x Native (MVC Architecture)
- **Database:** MySQL 8.x
- **Frontend:** TailwindCSS, Vanilla JavaScript
- **Maps:** Leaflet.js + OpenStreetMap
- **Payment:** Midtrans API (Snap.js)
- **Tools:** SweetAlert2, Fetch API, PHPMailer

### Core Architecture
```
/gorefill
├── /app
│   ├── /Controllers (Business logic)
│   ├── /Models (Database operations)
│   ├── /Views (UI templates)
│   └── bootstrap.php (App initialization)
├── /config (Database, Midtrans config)
├── /public (Front controller, assets)
├── /uploads (User-uploaded content)
└── /migrations (Database schema)
```

### Essential Rules
1. **Strict MVC** - No SQL in Views, no HTML in Models
2. **PDO Prepared Statements** - All queries must use prepared statements
3. **Leaflet.js Only** - For all map features (not Google Maps)
4. **Midtrans Only** - For all payment processing
5. **Session-Based Auth** - Use `$_SESSION` for authentication and cart
6. **Security First** - password_hash(), htmlspecialchars(), filter_input()
7. **SweetAlert** - For all UI notifications

---

## 📅 DEVELOPMENT PHASES

### ✅ PHASE 1: MVP FOUNDATION (2 Weeks)
**Goal:** Core authentication, product catalog, cart, basic checkout with payment

**Week 1:** Foundation & Authentication
- Day 1-2: Setup, database, routing
- Day 3-4: Authentication system (backend + UI)
- Day 5: Admin product CRUD (backend)

**Week 2:** Shopping & Payment
- Day 6: Product catalog pages
- Day 7: Shopping cart (session-based)
- Day 8-9: Checkout flow + Midtrans integration
- Day 10: Testing & bug fixes

**Deliverables:**
- ✅ User registration/login
- ✅ Product browsing with filters
- ✅ Shopping cart with AJAX
- ✅ Checkout with Midtrans payment
- ✅ Admin product management

📄 **Detailed Prompts:** `./WEEK-01-PROMPTS.md` & `./WEEK-02-PROMPTS.md`

---

### 🔄 PHASE 2: ADVANCED FEATURES (2 Weeks)
**Goal:** Maps integration, wishlist, order tracking, voucher system

**Week 3:** Maps & Tracking
- Day 11: Leaflet address picker for checkout
- Day 12-13: Courier tracking backend + frontend
- Day 14: Admin courier assignment
- Day 15: Wishlist/favorites feature

**Week 4:** Enhancement & Polish
- Day 16: Product reviews & ratings
- Day 17: Advanced voucher management
- Day 18: Admin dashboard analytics
- Day 19: Email notifications (PHPMailer)
- Day 20: Testing & optimization

**Deliverables:**
- ✅ Interactive maps for address selection
- ✅ Real-time courier tracking
- ✅ Wishlist functionality
- ✅ Product reviews
- ✅ Admin analytics dashboard

📄 **Detailed Prompts:** `./WEEK-03-PROMPTS.md` & `./WEEK-04-PROMPTS.md`

---

### 🚀 PHASE 3: OPTIMIZATION & DEPLOYMENT (1 Week)
**Goal:** Performance optimization, security hardening, deployment

**Week 5:** Production Ready
- Day 21: Code review & refactoring
- Day 22: Security audit & fixes
- Day 23: Performance optimization (caching, indexes)
- Day 24: Mobile responsiveness testing
- Day 25: Deployment setup & documentation

**Deliverables:**
- ✅ Optimized codebase
- ✅ Security vulnerabilities fixed
- ✅ Production-ready deployment
- ✅ Complete documentation

📄 **Detailed Prompts:** `./WEEK-05-PROMPTS.md`

---

## 🎯 STANDARD PROMPT TEMPLATE

When working with AI, always include this context block:

```
# CONTEXT: GoRefill - Day [X] (Phase [Y], Week [Z])

## PROJECT: GoRefill E-Commerce System
- **Language:** PHP 8.x Native (MVC)
- **Database:** MySQL 8.x
- **Frontend:** TailwindCSS + Vanilla JS
- **Maps:** Leaflet.js (OpenStreetMap)
- **Payment:** Midtrans API

## CORE RULES:
1. Strict MVC architecture (no SQL in Views)
2. All queries use PDO prepared statements
3. Leaflet.js for all maps (not Google Maps)
4. Midtrans for all payments
5. Session-based auth and cart
6. Security: password_hash(), htmlspecialchars(), filter_input()
7. UI: TailwindCSS + SweetAlert2

## PROJECT STRUCTURE:
/app (Controllers, Models, Views)
/config (config.php, midtrans.php)
/public (index.php, assets)
/migrations (gorefill.sql)

## REFERENCE FILES:
- Schema: /migrations/gorefill.sql
- Business Logic: README.md
- MCP Config: .windsurf/mcp.yaml

## TODAY'S TASK: [Task Title]
[Detailed instructions go here]

**Dependencies:** [What must be completed first]
**Steps:** [Numbered steps]
**Deliverables:** [Checklist of what to create]
**Use Context7 for:** [Topics to research]
```

---

## 📚 REFERENCE DOCUMENTATION

### Business Logic Flows

**User Journey:**
1. Register/Login → Browse Products → Add to Cart
2. Apply Voucher → Checkout (Select Address via Map)
3. Pay with Midtrans → Track Courier Location
4. Receive Order → Leave Review

**Admin Journey:**
1. Login → Dashboard (Sales Overview)
2. CRUD Products, Users, Vouchers
3. Manage Orders → Assign Couriers
4. View Reports & Analytics

**Courier Journey:**
1. Login → View Assigned Orders
2. Start Delivery → Auto-send Location (GPS)
3. Complete Delivery → Update Status

### Database Schema Summary

**Core Tables:**
- `users` - Multi-role (admin/user/courier)
- `products` - Catalog with eco_badge
- `orders` - With payment_status & delivery status
- `order_items` - Order line items
- `addresses` - With lat/lng for maps
- `vouchers` - Discount codes
- `favorites` - Wishlist
- `product_reviews` - Ratings 1-5
- `courier_locations` - GPS tracking data

### API Integration Points

**Midtrans Payment:**
- Snap Token Generation: `\Midtrans\Snap::getSnapToken()`
- Webhook Callback: `/index.php?route=payment.callback`
- Status: unpaid → paid → packing → shipped → delivered

**Leaflet Maps:**
- Address Selection: User clicks map → save lat/lng
- Courier Tracking: Update marker position every 5 seconds
- Route Display: Show path from warehouse to customer

---

## 🛠️ DEVELOPMENT WORKFLOW

### Daily Workflow
1. **Read the day's prompt** from weekly file
2. **Use Context7** to fetch relevant documentation
3. **Implement the features** following the steps
4. **Test thoroughly** before marking complete
5. **Update progress** in this file

### Code Quality Checklist
- [ ] Follows MVC architecture
- [ ] Uses prepared statements
- [ ] Input is sanitized
- [ ] Errors are handled gracefully
- [ ] Code is commented
- [ ] UI is responsive (mobile-first)
- [ ] AJAX operations have loading states
- [ ] Success/error messages use SweetAlert

### Testing Checklist
- [ ] Feature works on Chrome, Firefox, Safari
- [ ] Mobile responsiveness verified
- [ ] No console errors
- [ ] Database operations successful
- [ ] Edge cases handled
- [ ] Security vulnerabilities checked

---

## 📝 PROGRESS TRACKING

### Phase 1 Progress
- [ ] Week 1 Complete (Days 1-5)
- [ ] Week 2 Complete (Days 6-10)

### Phase 2 Progress
- [ ] Week 3 Complete (Days 11-15)
- [ ] Week 4 Complete (Days 16-20)

### Phase 3 Progress
- [ ] Week 5 Complete (Days 21-25)

---

## 🔗 QUICK LINKS

- **Weekly Prompts:** 
  - [Week 1 Prompts](./WEEK-01-PROMPTS.md) (Days 1-5)
  - [Week 2 Prompts](./WEEK-02-PROMPTS.md) (Days 6-10)
  - [Week 3 Prompts](./WEEK-03-PROMPTS.md) (Days 11-15)
  - [Week 4 Prompts](./WEEK-04-PROMPTS.md) (Days 16-20)
  - [Week 5 Prompts](./WEEK-05-PROMPTS.md) (Days 21-25)

- **Project Docs:**
  - [README.md](../README.md) - Complete project documentation
  - [MCP Config](./.windsurf/mcp.yaml) - AI context configuration
  - [Database Schema](../migrations/gorefill.sql) - Full schema

---

## 💡 TIPS FOR WORKING WITH AI

1. **Always copy the full context block** when starting a new chat
2. **Reference specific files** when asking questions (e.g., "Check ProductController.php")
3. **Use Context7** for up-to-date library documentation
4. **Test incrementally** - don't build everything at once
5. **Ask for code reviews** after completing each day
6. **Update this file** with any deviations from the plan

---

**Created by:** Fahmi Aksan Nugroho
**Last Updated:** 2025-10-23
**Project Status:** Phase 1 - Planning Complete
