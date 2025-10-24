<?php
/**
 * Image Helper for GoRefill Application
 * 
 * Handles flexible image sources:
 * - Local uploads (uploads/products/...)
 * - External URLs (Unsplash, etc.)
 */

class ImageHelper
{
    /**
     * Check if image is external URL (from Unsplash)
     * 
     * @param string|null $image
     * @return bool
     */
    public static function isExternalUrl($image)
    {
        if (empty($image)) {
            return false;
        }
        
        // Check if it's a URL (starts with http:// or https://)
        return filter_var($image, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Check if image is from Unsplash
     * 
     * @param string|null $image
     * @return bool
     */
    public static function isUnsplashUrl($image)
    {
        if (empty($image)) {
            return false;
        }
        
        // Check if URL contains unsplash.com or images.unsplash.com
        return (
            str_contains($image, 'unsplash.com') || 
            str_contains($image, 'images.unsplash.com')
        );
    }
    
    /**
     * Get full image path/URL
     * Returns either local path or external URL
     * 
     * @param string|null $image
     * @return string
     */
    public static function getImageUrl($image)
    {
        if (empty($image)) {
            return '';
        }
        
        // If it's external URL, return as is
        if (self::isExternalUrl($image)) {
            return $image;
        }
        
        // If it's local file, return relative path
        return '../uploads/products/' . $image;
    }
    
    /**
     * Render image tag with fallback
     * 
     * @param string|null $image
     * @param string $alt
     * @param string $class
     * @return string HTML img tag or fallback div
     */
    public static function renderProductImage($image, $alt = 'Product Image', $class = '')
    {
        if (empty($image)) {
            // Return fallback
            return '<div class="' . htmlspecialchars($class) . ' bg-gray-200 flex items-center justify-center">
                        <span class="text-6xl">📦</span>
                    </div>';
        }
        
        $imageUrl = self::getImageUrl($image);
        
        return '<img src="' . htmlspecialchars($imageUrl) . '" 
                     alt="' . htmlspecialchars($alt) . '" 
                     class="' . htmlspecialchars($class) . '"
                     onerror="this.onerror=null; this.parentElement.innerHTML=\'<div class=\'' . htmlspecialchars($class) . ' bg-gray-200 flex items-center justify-center\'><span class=\\\'text-6xl\\\'>📦</span></div>\';">';
    }
    
    /**
     * Validate image input
     * 
     * @param string $image
     * @param string $type ('url' or 'file')
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateImage($image, $type)
    {
        if ($type === 'url') {
            // Validate URL
            if (!filter_var($image, FILTER_VALIDATE_URL)) {
                return [
                    'valid' => false,
                    'message' => 'Invalid URL format'
                ];
            }
            
            // Must be from Unsplash
            if (!self::isUnsplashUrl($image)) {
                return [
                    'valid' => false,
                    'message' => 'Image URL must be from Unsplash (unsplash.com)'
                ];
            }
            
            return [
                'valid' => true,
                'message' => 'Valid Unsplash URL'
            ];
        }
        
        // Type is 'file' - validation handled by upload process
        return [
            'valid' => true,
            'message' => 'File upload'
        ];
    }
    
    /**
     * Get Unsplash image with specific size
     * Modifies Unsplash URL to request specific dimensions
     * 
     * @param string $url
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function getUnsplashImageWithSize($url, $width = 800, $height = 600)
    {
        if (!self::isUnsplashUrl($url)) {
            return $url;
        }
        
        // Add size parameters to Unsplash URL
        $separator = strpos($url, '?') !== false ? '&' : '?';
        return $url . $separator . "w={$width}&h={$height}&fit=crop";
    }
}
