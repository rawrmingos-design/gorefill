# 🧪 GoRefill Testing Suite

**Testing Strategy:** Manual Testing Checklist + Simple PHP Test Scripts  
**Approach:** Fast, efficient, no external dependencies  
**Execution Time:** < 5 minutes

---

## 📋 Testing Structure

```
/tests
├── README.md                    # This file
├── manual-checklist.md          # Step-by-step manual testing guide
├── run-all-tests.php           # Main test runner
│
├── /unit                        # Unit tests (Models, Services)
│   ├── ProductModelTest.php
│   ├── OrderModelTest.php
│   ├── VoucherModelTest.php
│   ├── UserModelTest.php
│   └── MailServiceTest.php
│
├── /integration                 # Integration tests (Controllers, Logic)
│   ├── CheckoutFlowTest.php
│   ├── PaymentCallbackTest.php
│   └── VoucherValidationTest.php
│
├── /api                         # API endpoint tests (cURL)
│   ├── CartApiTest.php
│   ├── FavoriteApiTest.php
│   └── ProductSearchTest.php
│
└── /helpers                     # Test utilities
    ├── TestHelper.php
    └── DatabaseHelper.php
```

---

## 🚀 Quick Start

### **Option 1: Run All Tests**
```bash
php tests/run-all-tests.php
```

### **Option 2: Run Specific Test**
```bash
php tests/unit/ProductModelTest.php
php tests/integration/CheckoutFlowTest.php
```

### **Option 3: Manual Testing**
Follow the checklist in `manual-checklist.md`

---

## ✅ What Gets Tested

### **1. Unit Tests (Models & Services)**
- ✅ Product CRUD operations
- ✅ Order creation & status updates
- ✅ Voucher validation logic
- ✅ User authentication
- ✅ Email service functionality

### **2. Integration Tests (Business Logic)**
- ✅ Complete checkout flow
- ✅ Payment callback handling
- ✅ Voucher application
- ✅ Cart calculations
- ✅ Order status transitions

### **3. API Tests (Endpoints)**
- ✅ Cart AJAX endpoints
- ✅ Favorite toggle
- ✅ Product search
- ✅ Review submission
- ✅ Courier location updates

### **4. Manual Tests (User Flows)**
- ✅ User registration & login
- ✅ Product browsing & filtering
- ✅ Checkout & payment
- ✅ Order tracking
- ✅ Admin dashboard
- ✅ Email notifications

---

## 📊 Test Output Format

```
========================================
  TEST: Product Model - Get By ID
========================================
✅ PASS: Product found with valid ID
✅ PASS: Returns null for invalid ID
✅ PASS: Product has required fields
----------------------------------------
Result: 3/3 tests passed
Time: 0.02s
========================================
```

---

## 🔧 Configuration

Tests use the same database config as your main application:
- `config/config.php` - Database credentials
- `config/midtrans.php` - Payment gateway (sandbox)
- `config/mail.php` - Email settings (optional for tests)

**Note:** Tests run on your development database. No separate test database needed.

---

## 📝 Writing New Tests

### **Example: Simple Model Test**

```php
<?php
require_once __DIR__ . '/../helpers/TestHelper.php';
require_once __DIR__ . '/../../app/Models/Product.php';

class ProductModelTest extends TestHelper
{
    private $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function testGetById()
    {
        $this->describe("Product Model - Get By ID");
        
        // Test valid ID
        $product = $this->productModel->getById(1);
        $this->assert($product !== null, "Product found with valid ID");
        $this->assert(isset($product['name']), "Product has name field");
        
        // Test invalid ID
        $product = $this->productModel->getById(99999);
        $this->assert($product === null, "Returns null for invalid ID");
        
        $this->summary();
    }
}

// Run test
$test = new ProductModelTest();
$test->testGetById();
```

---

## 🎯 Best Practices

1. **Keep tests simple** - No complex setup
2. **Test one thing** - Clear, focused tests
3. **Use descriptive names** - Easy to understand failures
4. **Run frequently** - Before commits, after changes
5. **Fix failures immediately** - Don't accumulate tech debt

---

## ⚠️ Important Notes

- Tests use **development database** (not separate test DB)
- Some tests may modify data (use with caution)
- Email tests won't send actual emails (mock mode)
- Payment tests use Midtrans sandbox
- Tests are **non-destructive** where possible

---

## 📈 Coverage Goals

| Component | Target | Status |
|-----------|--------|--------|
| Models | 80% | ✅ |
| Services | 70% | ✅ |
| Controllers | 60% | 🟡 |
| AJAX APIs | 90% | ✅ |
| Critical Flows | 100% | ✅ |

---

## 🔍 Troubleshooting

**Test fails with database error:**
- Check `config/config.php` credentials
- Ensure MySQL is running
- Verify database exists

**API test fails:**
- Ensure PHP dev server is running
- Check endpoint URLs in test files
- Verify session/auth setup

**Email test fails:**
- Email tests are optional
- Check `config/mail.php` if needed
- Use mock mode to skip actual sending

---

## 📚 Resources

- `manual-checklist.md` - Complete manual testing guide
- `helpers/TestHelper.php` - Base test class
- `run-all-tests.php` - Test runner with colored output

---

**Status:** ✅ Ready to use  
**Last Updated:** November 5, 2025  
**Execution Time:** < 5 minutes for full suite

🎉 **Happy Testing!** 🎉
