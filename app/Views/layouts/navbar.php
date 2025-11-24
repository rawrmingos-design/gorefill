<nav class="bg-white shadow-lg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <!-- Logo -->
      <div class="flex items-center">
        <a href="?route=home" class="flex items-center space-x-3 group">
          <img src="<?= asset('images/logo.png') ?>" alt="GoRefill Logo" class="h-12 w-12 transition-transform group-hover:scale-110">
          <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent group-hover:from-blue-700 group-hover:to-green-700 transition-all">
            GoRefill
          </span>
        </a>
      </div>

      <!-- Hamburger button (mobile) -->
      <div class="flex items-center md:hidden">
        <button id="menu-toggle" class="text-gray-700 hover:text-blue-600 focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Menu (desktop) -->
      <div class="hidden md:flex items-center space-x-4">
        <a href="?route=products" class="text-gray-700 hover:text-blue-600">Products</a>
        <a href="?route=faq" class="text-gray-700 hover:text-blue-600">
          <i class="fas fa-question-circle"></i> FAQ
        </a>
        <a href="?route=contact" class="text-gray-700 hover:text-blue-600">
          <i class="fas fa-envelope"></i> Contact
        </a>
        <a href="?route=cart" class="text-gray-700 hover:text-blue-600">
          🛒 Cart <span id="cart-badge" class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">0</span>
        </a>
        <a href="?route=favorites" class="text-gray-700 hover:text-blue-600">
          <i class="fas fa-heart text-red-500"></i> Favorit
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="?route=admin.dashboard"
              class="text-purple-600 hover:text-purple-800 font-semibold flex items-center">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Admin Panel
            </a>
          <?php endif; ?>
          <?php if ($_SESSION['role'] !== 'admin'): ?>
          <a href="?route=profile" class="text-gray-700 hover:text-blue-600 flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <?php echo e($_SESSION['name']); ?>
          </a>
          <?php endif; ?>
          <a href="?route=auth.logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
        <?php else: ?>
          <a href="?route=auth.login" class="text-blue-600 hover:text-blue-800">Login</a>
          <a href="?route=auth.register" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Menu (mobile) -->
  <div id="mobile-menu" class="md:hidden hidden px-4 pb-4 space-y-2 bg-white shadow">
    <a href="?route=products" class="block text-gray-700 hover:text-blue-600">Products</a>
    <a href="?route=faq" class="block text-gray-700 hover:text-blue-600">
      <i class="fas fa-question-circle"></i> FAQ
    </a>
    <a href="?route=contact" class="block text-gray-700 hover:text-blue-600">
      <i class="fas fa-envelope"></i> Contact
    </a>
    <a href="?route=cart" class="block text-gray-700 hover:text-blue-600">
      🛒 Cart <span id="cart-badge-mobile"
        class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">0</span>
    </a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="?route=favorites" class="block text-gray-700 hover:text-blue-600">
        <i class="fas fa-heart text-red-500"></i> Favorit
      </a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="?route=admin.dashboard"
          class="block text-purple-600 hover:text-purple-800 font-semibold">⚙️ Admin Panel</a>
      <?php endif; ?>
      <a href="?route=profile" class="block text-gray-700 hover:text-blue-600">👤 <?php echo e($_SESSION['name']); ?></a>
      <a href="?route=auth.logout" class="block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-center">Logout</a>
    <?php else: ?>
      <a href="?route=auth.login" class="block text-blue-600 hover:text-blue-800">Login</a>
      <a href="?route=auth.register"
        class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center">Register</a>
    <?php endif; ?>
  </div>
</nav>

<script>
  const toggleBtn = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');

  toggleBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });
</script>
