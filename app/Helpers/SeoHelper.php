<?php
/**
 * SEO Helper
 * 
 * Generates SEO meta tags for better search engine optimization
 */

class SeoHelper
{
    /**
     * Generate SEO meta tags
     * 
     * @param array $data SEO data (title, description, keywords, image, url, type)
     * @return string HTML meta tags
     */
    public static function generateMetaTags($data = [])
    {
        $defaults = [
            'title' => 'GoRefill - Layanan Isi Ulang Air Galon & LPG',
            'description' => 'Pesan air galon dan tabung gas LPG dengan mudah. Pengiriman cepat, harga terjangkau, dan layanan terpercaya di seluruh Indonesia.',
            'keywords' => 'air galon, gas LPG, isi ulang air, isi ulang gas, pengiriman air galon, pengiriman gas, GoRefill',
            'image' => '/public/assets/images/logo.png',
            'url' => self::getCurrentUrl(),
            'type' => 'website',
            'site_name' => 'GoRefill',
            'locale' => 'id_ID'
        ];
        
        $seo = array_merge($defaults, $data);
        
        // Sanitize data
        $seo['title'] = htmlspecialchars($seo['title'], ENT_QUOTES, 'UTF-8');
        $seo['description'] = htmlspecialchars($seo['description'], ENT_QUOTES, 'UTF-8');
        
        ob_start();
        ?>
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= $seo['description'] ?>">
    <meta name="keywords" content="<?= $seo['keywords'] ?>">
    <meta name="author" content="GoRefill">
    <link rel="canonical" href="<?= $seo['url'] ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= $seo['type'] ?>">
    <meta property="og:url" content="<?= $seo['url'] ?>">
    <meta property="og:title" content="<?= $seo['title'] ?>">
    <meta property="og:description" content="<?= $seo['description'] ?>">
    <meta property="og:image" content="<?= self::getAbsoluteUrl($seo['image']) ?>">
    <meta property="og:site_name" content="<?= $seo['site_name'] ?>">
    <meta property="og:locale" content="<?= $seo['locale'] ?>">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= $seo['url'] ?>">
    <meta property="twitter:title" content="<?= $seo['title'] ?>">
    <meta property="twitter:description" content="<?= $seo['description'] ?>">
    <meta property="twitter:image" content="<?= self::getAbsoluteUrl($seo['image']) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/public/assets/images/logo.png">
    <link rel="apple-touch-icon" href="/public/assets/images/logo.png">
    
    <!-- Additional SEO -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get current URL
     */
    public static function getCurrentUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        return $protocol . $host . $uri;
    }
    
    /**
     * Convert relative URL to absolute URL
     */
    private static function getAbsoluteUrl($url)
    {
        if (strpos($url, 'http') === 0) {
            return $url;
        }
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        return $protocol . $host . $url;
    }
    
    /**
     * Generate JSON-LD structured data
     */
    public static function generateStructuredData($data = [])
    {
        $type = $data['type'] ?? 'Organization';
        
        if ($type === 'Organization') {
            return self::generateOrganizationSchema($data);
        } elseif ($type === 'Product') {
            return self::generateProductSchema($data);
        } elseif ($type === 'BreadcrumbList') {
            // For breadcrumb schema, we only care about the items list
            $items = $data['items'] ?? [];
            if (!is_array($items) || empty($items)) {
                return '';
            }
            return self::generateBreadcrumbSchema($items);
        }
        
        return '';
    }
    
    /**
     * Generate Organization Schema
     */
    private static function generateOrganizationSchema($data = [])
    {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => $data['name'] ?? "GoRefill",
            "description" => $data['description'] ?? "Layanan isi ulang air galon dan LPG terpercaya",
            "url" => $data['url'] ?? self::getCurrentUrl(),
            "logo" => self::getAbsoluteUrl("/public/assets/images/logo.png"),
            "sameAs" => $data['social'] ?? []
        ];
        
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
    
    /**
     * Generate Product Schema
     */
    private static function generateProductSchema($data = [])
    {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $data['name'] ?? "",
            "description" => $data['description'] ?? "",
            "image" => $data['image'] ?? "",
            "offers" => [
                "@type" => "Offer",
                "price" => $data['price'] ?? 0,
                "priceCurrency" => "IDR",
                "availability" => "https://schema.org/InStock"
            ]
        ];
        
        if (isset($data['rating'])) {
            $schema['aggregateRating'] = [
                "@type" => "AggregateRating",
                "ratingValue" => $data['rating'],
                "reviewCount" => $data['review_count'] ?? 0
            ];
        }
        
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
    
    /**
     * Generate Breadcrumb Schema
     */
    private static function generateBreadcrumbSchema($items = [])
    {
        $itemListElement = [];
        $position = 1;
        
        foreach ($items as $item) {
            // Skip invalid entries to avoid runtime errors
            if (!is_array($item) || !isset($item['name'], $item['url'])) {
                continue;
            }
            
            $itemListElement[] = [
                "@type" => "ListItem",
                "position" => $position++,
                "name" => $item['name'],
                "item" => $item['url']
            ];
        }
        
        if (empty($itemListElement)) {
            return '';
        }
        
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $itemListElement
        ];
        
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}
