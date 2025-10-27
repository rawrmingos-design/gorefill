# 📅 WEEK 3: Maps Integration & Tracking (Days 11-15)

## Standard Context (Copy before each day's prompt)
```
PROJECT: GoRefill E-Commerce | PHP 8.x MVC | MySQL 8.x | TailwindCSS | Leaflet.js | Midtrans
RULES: Strict MVC, PDO prepared statements, Leaflet.js for ALL maps (not Google Maps)
DEPENDENCIES: Phase 1 MVP complete (auth, products, cart, payment working)
```

---

## 📅 DAY 11: Leaflet.js Address Picker

**Task:** Implement interactive map for address selection during checkout

**Dependencies:** Phase 1 complete

**Steps:**
1. Add Leaflet.js CDN to layout:
   ```html
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
   <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
   ```
2. Update `/app/Views/checkout/index.php`:
   - Add map container: `<div id="map" style="height: 400px;"></div>`
   - Add hidden inputs: latitude, longitude
3. Create `/public/assets/js/maps.js`:
   - Initialize Leaflet with OpenStreetMap tiles
   - Default center: Semarang (-6.9667, 110.4167), zoom 13
   - Click event to place marker and capture lat/lng:
     ```javascript
     const map = L.map('map').setView([-6.9667, 110.4167], 13);
     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
     let marker;
     map.on('click', (e) => {
       if (marker) map.removeLayer(marker);
       marker = L.marker(e.latlng).addTo(map);
       document.getElementById('latitude').value = e.latlng.lat;
       document.getElementById('longitude').value = e.latlng.lng;
     });
     ```
4. Update Address model to save lat/lng
5. Add "Use Current Location" button:
   - Use `navigator.geolocation.getCurrentPosition()`
   - Center map and place marker at user location
6. Style map container with TailwindCSS
7. Test: user clicks map → marker appears → lat/lng saved → address created

**Deliverables:** ✅ Leaflet.js integrated ✅ Interactive map ✅ Click to place marker ✅ Lat/lng saved ✅ Current location button ✅ TailwindCSS styling

**Use Context7:** Leaflet.js documentation, OpenStreetMap usage

---

## 📅 DAY 12: Courier Tracking Backend

**Task:** Build backend for courier location updates

**Dependencies:** Day 11 complete (Leaflet setup)

**Steps:**
1. Create `/app/Models/CourierLocation.php`:
   - updateLocation($courierId, $lat, $lng) - upsert with timestamp
   - getLocation($courierId) - latest location
   - getLocationHistory($courierId, $limit) - location trail
2. Create `/app/Controllers/CourierController.php`:
   - updateLocation() POST - save courier GPS data
     * Validate courier authentication
     * Save to courier_locations table
     * Return JSON success
   - getMyOrders() GET - orders assigned to courier
   - startDelivery($orderId) POST - update status to 'shipped'
   - completeDelivery($orderId) POST - update status to 'delivered'
3. Update `/app/Models/Order.php`:
   - assignCourier($orderId, $courierId)
   - getOrdersForCourier($courierId) - with WHERE courier_id=$id
4. Create `/app/Views/courier/dashboard.php`:
   - List assigned orders
   - "Start Delivery" button for each
   - "Complete Delivery" button when shipped
   - Map showing route (tomorrow's task)
5. Implement location update API:
   - Route: `/index.php?route=courier.updateLocation`
   - POST JSON: `{latitude: float, longitude: float}`
   - Validate courier role
   - Save with timestamp

**Deliverables:** ✅ CourierLocation.php ✅ CourierController.php ✅ Order courier methods ✅ Courier dashboard ✅ Location API working

**Use Context7:** PHP geolocation handling, upsert patterns

---

## 📅 DAY 13: Real-Time Courier Tracking UI

**Task:** Display courier location on map with real-time updates

**Dependencies:** Day 12 complete (courier backend ready)

**Steps:**
1. Create `/public/assets/js/tracking.js`:
   - Initialize Leaflet map
   - Fetch courier location every 5 seconds:
     ```javascript
     async function updateCourierLocation(courierId) {
       const res = await fetch(`/index.php?route=courier.getLocation&id=${courierId}`);
       const data = await res.json();
       updateMarker(data.latitude, data.longitude);
     }
     setInterval(() => updateCourierLocation(courierId), 5000);
     ```
   - Update marker position smoothly
   - Show delivery route (line from warehouse to customer)
2. Add to `/app/Controllers/CourierController.php`:
   - getLocation($courierId) GET - return latest lat/lng as JSON
3. Create `/app/Views/orders/track.php`:
   - Display map with courier marker
   - Show order details (items, address, status)
   - Real-time status updates
   - Estimated arrival time (optional)
4. Update user dashboard to show "Track Order" button for shipped orders
5. Implement auto-location sending from courier device:
   - In courier dashboard, use `navigator.geolocation.watchPosition()`
   - Send location every 30 seconds automatically
   - Stop when delivery completed
6. Add route polyline showing path from warehouse to customer
7. Test: courier starts delivery → location updates → user sees moving marker

**Deliverables:** ✅ tracking.js ✅ Real-time location updates ✅ orders/track.php ✅ Courier marker moving ✅ Auto GPS sending ✅ Route polyline ✅ User tracking view

**Use Context7:** Leaflet markers animation, JavaScript geolocation API

---

## 📅 DAY 14: Admin Courier Management

**Task:** Admin can assign couriers to orders

**Dependencies:** Day 13 complete (tracking UI ready)

**Steps:**
1. Update `/app/Controllers/AdminController.php`:
   - orders() GET - list all orders with pagination
   - orderDetail($id) GET - show order details
   - assignCourier($orderId) POST - assign courier to order
   - updateOrderStatus($orderId) POST - change status (packing/shipped/delivered)
2. Create `/app/Views/admin/orders/index.php`:
   - Table: order_id, customer, total, status, payment_status, actions
   - Filter by status (unpaid/packing/shipped/delivered)
   - "Assign Courier" button for each order
3. Create `/app/Views/admin/orders/detail.php`:
   - Order info: items, customer, address (show on map)
   - Status timeline: unpaid → paid → packing → shipped → delivered
   - Courier assignment dropdown (list of couriers from users table)
   - "Update Status" buttons
   - "View Tracking" link (opens tracking page)
4. Update User model:
   - getCouriers() - get all users with role='courier'
5. Implement order status workflow:
   - paid → packing (manual by admin)
   - packing → shipped (when courier starts delivery)
   - shipped → delivered (when courier completes)
6. Add SweetAlert confirmations for status changes
7. Test: admin assigns courier → courier sees order → starts delivery → admin tracks

**Deliverables:** ✅ Admin order list ✅ Order detail page ✅ Courier assignment ✅ Status update ✅ Status workflow ✅ SweetAlert confirms

**Use Context7:** Admin panel design patterns, order management UX

---

## 📅 DAY 15: Wishlist/Favorites Feature

**Task:** Allow users to save favorite products

**Dependencies:** Day 14 complete

**Steps:**
1. Create `/app/Models/Favorite.php`:
   - add($userId, $productId) - add to favorites
   - remove($userId, $productId) - remove from favorites
   - getByUserId($userId) - get user's favorites with product details
   - exists($userId, $productId) - check if product is favorited
2. Create `/app/Controllers/FavoriteController.php`:
   - add() POST - add to favorites (AJAX)
   - remove() POST - remove from favorites (AJAX)
   - index() GET - show user's favorites page
   - All return JSON for AJAX
3. Update `/app/Views/products/index.php` and `detail.php`:
   - Add heart icon button (outline if not favorited, filled if favorited)
   - Click to toggle favorite (AJAX, no page reload)
   - Show SweetAlert notification
4. Create `/app/Views/favorites/index.php`:
   - Grid layout showing favorited products
   - Same design as product listing
   - "Remove from Favorites" button
   - "Add to Cart" button
5. Create `/public/assets/js/favorites.js`:
   ```javascript
   async function toggleFavorite(productId) {
     const res = await fetch('/index.php?route=favorite.add', {
       method: 'POST',
       body: JSON.stringify({product_id: productId})
     });
     const data = await res.json();
     updateHeartIcon(productId, data.is_favorite);
   }
   ```
6. Add "Favorites" link to user navbar
7. Add unique constraint on favorites table (user_id, product_id)
8. Test: add to favorites → heart filled → remove → heart outline → view favorites page

**Deliverables:** ✅ Favorite.php model ✅ FavoriteController.php ✅ Heart icon toggle ✅ favorites/index.php ✅ favorites.js ✅ AJAX working ✅ Navbar link

**Use Context7:** Toggle button UX patterns, favorites implementation

---

## 🎯 WEEK 3 COMPLETION CHECKLIST
- [ ] Leaflet.js integrated for address selection
- [ ] Interactive map with click-to-place marker
- [ ] Courier location tracking backend
- [ ] Real-time courier tracking UI
- [ ] Auto GPS location sending
- [ ] Admin courier assignment
- [ ] Order status management
- [ ] Wishlist/favorites feature
- [ ] All features tested and working

**Next Week:** Product reviews, advanced vouchers, admin analytics, email notifications
