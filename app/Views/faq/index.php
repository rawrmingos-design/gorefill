<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'FAQ') ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="/public/assets/images/logo.png">
    <style>
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .accordion-content.active {
            max-height: 500px;
            transition: max-height 0.4s ease-in;
        }
        .accordion-icon {
            transition: transform 0.3s ease;
        }
        .accordion-icon.active {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                <i class="fas fa-question-circle text-blue-600 mr-3"></i>
                Pertanyaan Umum (FAQ)
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Temukan jawaban atas pertanyaan yang sering diajukan tentang layanan GoRefill
            </p>
        </div>

        <!-- FAQ Categories -->
        <?php foreach ($faqs as $index => $category): ?>
            <div class="mb-8">
                <!-- Category Header -->
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-<?= $category['color'] ?>-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas <?= $category['icon'] ?> text-<?= $category['color'] ?>-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($category['category']) ?></h2>
                </div>

                <!-- Questions in this category -->
                <div class="space-y-3">
                    <?php foreach ($category['questions'] as $qIndex => $qa): ?>
                        <?php $accordionId = "accordion-{$index}-{$qIndex}"; ?>
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200">
                            <!-- Question Button -->
                            <button 
                                onclick="toggleAccordion('<?= $accordionId ?>')"
                                class="w-full text-left px-6 py-4 flex items-center justify-between focus:outline-none group">
                                <span class="font-semibold text-gray-800 pr-4 group-hover:text-<?= $category['color'] ?>-600 transition">
                                    <?= htmlspecialchars($qa['question']) ?>
                                </span>
                                <i class="fas fa-chevron-down text-<?= $category['color'] ?>-600 accordion-icon" id="icon-<?= $accordionId ?>"></i>
                            </button>
                            
                            <!-- Answer Content -->
                            <div id="<?= $accordionId ?>" class="accordion-content">
                                <div class="px-6 pb-4 pt-2 text-gray-600 leading-relaxed">
                                    <?= nl2br(htmlspecialchars($qa['answer'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Still Have Questions? -->
        <div class="mt-12 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-8 text-center border-2 border-green-200">
            <i class="fas fa-envelope text-5xl text-green-600 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Masih Ada Pertanyaan?</h3>
            <p class="text-gray-600 mb-6">
                Jika Anda tidak menemukan jawaban yang Anda cari, jangan ragu untuk menghubungi kami!
            </p>
            <a href="?route=contact" 
               class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg transition shadow-md hover:shadow-lg">
                <i class="fas fa-paper-plane mr-2"></i>
                Hubungi Kami
            </a>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            
            // Close all other accordions
            document.querySelectorAll('.accordion-content').forEach(item => {
                if (item.id !== id && item.classList.contains('active')) {
                    item.classList.remove('active');
                }
            });
            
            document.querySelectorAll('.accordion-icon').forEach(item => {
                if (item.id !== 'icon-' + id && item.classList.contains('active')) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current accordion
            content.classList.toggle('active');
            icon.classList.toggle('active');
        }
    </script>
</body>
</html>
