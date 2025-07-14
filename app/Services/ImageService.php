<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Jobs\OptimizeImage;

class ImageService
{
    // Industry-standard aspect ratios
    public const ASPECT_RATIOS = [
        '16:9' => [16, 9],   // Widescreen - Primary images, cover images, banners
        '1:1' => [1, 1],     // Square - Thumbnails, profile pictures, listings
        '4:3' => [4, 3],     // Traditional - Standard photography
        '3:2' => [3, 2],     // Golden ratio - Professional photography
        '21:9' => [21, 9],   // Ultrawide - Cinematic banners
        '9:16' => [9, 16],   // Portrait - Mobile-first content
    ];

    // Image size configurations for different use cases
    public const IMAGE_SIZES = [
        'primary' => [
            'width' => 1920,
            'height' => 1080,
            'aspect_ratio' => '16:9',
            'use_case' => 'Primary images, cover images, banners'
        ],
        'listing' => [
            'width' => 400,
            'height' => 400,
            'aspect_ratio' => '1:1',
            'use_case' => 'Thumbnails, profile pictures, listings'
        ],
        'card' => [
            'width' => 600,
            'height' => 450,
            'aspect_ratio' => '4:3',
            'use_case' => 'Card images, featured content'
        ],
        'hero' => [
            'width' => 1920,
            'height' => 800,
            'aspect_ratio' => '21:9',
            'use_case' => 'Hero banners, wide displays'
        ],
        'portrait' => [
            'width' => 600,
            'height' => 800,
            'aspect_ratio' => '3:4',
            'use_case' => 'Portrait images, mobile content'
        ]
    ];

    /**
     * Store image with proper aspect ratio handling
     */
    public function store(UploadedFile $file, string $folder = 'images', string $type = 'primary', $disk = 'public'): string
    {
        if ($file == null) {
            throw new Exception('File is required to store. No file provided');
        }

        // Get size configuration for the specified type
        $sizeConfig = self::IMAGE_SIZES[$type] ?? self::IMAGE_SIZES['primary'];
        
        // Store original file
        $originalPath = $file->store($folder, ['disk' => $disk]);
        
        // Process image with proper aspect ratio
        $this->processImageWithAspectRatio($file, $originalPath, $sizeConfig, $disk, $folder);
        
        // Queue optimization
        OptimizeImage::dispatch(storage_path('app/public/' . $originalPath));
        
        return $originalPath;
    }

    /**
     * Process image with aspect ratio preservation
     */
    private function processImageWithAspectRatio(UploadedFile $file, string $originalPath, array $config, string $disk, string $folder): void
    {
        $image = Image::make($file);
        $uniqueName = Arr::last(explode('/', $originalPath));
        
        // Calculate target dimensions based on aspect ratio
        $targetWidth = $config['width'];
        $targetHeight = $config['height'];
        
        // Create different sizes for responsive display
        $sizes = [
            'large' => [$targetWidth, $targetHeight],
            'medium' => [800, intval(800 * ($targetHeight / $targetWidth))],
            'small' => [400, intval(400 * ($targetHeight / $targetWidth))],
            'thumbnail' => [200, intval(200 * ($targetHeight / $targetWidth))]
        ];
        
        foreach ($sizes as $sizeName => $dimensions) {
            $width = $dimensions[0];
            $height = $dimensions[1];
            
            // Clone the image for processing
            $processedImage = clone $image;
            
            // Resize maintaining aspect ratio and crop if necessary
            $processedImage = $processedImage->fit($width, $height, function ($constraint) {
                $constraint->upsize();
            });
            
            // Save processed image
            $processedPath = $folder . '/' . $sizeName . '/' . $uniqueName;
            Storage::disk($disk)->put($processedPath, $processedImage->stream());
        }
    }

    /**
     * Get image with specific size
     */
    public function getImageUrl(string $path, string $size = 'medium'): string
    {
        if (!$path) {
            return asset('images/profile/default.png');
        }
        
        $pathParts = explode('/', $path);
        $fileName = array_pop($pathParts);
        $basePath = implode('/', $pathParts);
        
        $sizePath = $basePath . '/' . $size . '/' . $fileName;
        
        if (Storage::disk('public')->exists($sizePath)) {
            return Storage::disk('public')->url($sizePath);
        }
        
        // Fallback to original if size doesn't exist
        return Storage::disk('public')->url($path);
    }

    /**
     * Get aspect ratio information
     */
    public static function getAspectRatioInfo(string $ratio): array
    {
        return [
            'ratio' => $ratio,
            'dimensions' => self::ASPECT_RATIOS[$ratio] ?? [16, 9],
            'recommended_use' => self::getRecommendedUse($ratio)
        ];
    }

    /**
     * Get recommended use for aspect ratio
     */
    private static function getRecommendedUse(string $ratio): string
    {
        return match ($ratio) {
            '16:9' => 'Primary images, cover images, banners, hero sections',
            '1:1' => 'Profile pictures, thumbnails, social media, listings',
            '4:3' => 'Traditional photography, card images, featured content',
            '3:2' => 'Professional photography, article images',
            '21:9' => 'Cinematic banners, ultrawide displays',
            '9:16' => 'Portrait content, mobile-first designs',
            default => 'General use'
        };
    }

    /**
     * Validate image aspect ratio
     */
    public function validateAspectRatio(UploadedFile $file, string $expectedRatio): bool
    {
        $image = Image::make($file);
        $width = $image->width();
        $height = $image->height();
        
        // Calculate actual ratio
        $gcd = $this->greatestCommonDivisor($width, $height);
        $actualRatio = ($width / $gcd) . ':' . ($height / $gcd);
        
        return $actualRatio === $expectedRatio;
    }

    /**
     * Get image dimensions and aspect ratio
     */
    public function getImageInfo(UploadedFile $file): array
    {
        $image = Image::make($file);
        $width = $image->width();
        $height = $image->height();
        
        $gcd = $this->greatestCommonDivisor($width, $height);
        $ratio = ($width / $gcd) . ':' . ($height / $gcd);
        
        return [
            'width' => $width,
            'height' => $height,
            'aspect_ratio' => $ratio,
            'is_landscape' => $width > $height,
            'is_portrait' => $height > $width,
            'is_square' => $width === $height
        ];
    }

    /**
     * Calculate greatest common divisor
     */
    private function greatestCommonDivisor(int $a, int $b): int
    {
        return $b === 0 ? $a : $this->greatestCommonDivisor($b, $a % $b);
    }

    /**
     * Get size recommendations for different use cases
     */
    public static function getSizeRecommendations(): array
    {
        return self::IMAGE_SIZES;
    }

    /**
     * Generate aspect ratio CSS
     */
    public static function generateAspectRatioCSS(string $ratio): string
    {
        $dimensions = self::ASPECT_RATIOS[$ratio] ?? [16, 9];
        $percentage = ($dimensions[1] / $dimensions[0]) * 100;
        
        return "aspect-ratio: {$dimensions[0]} / {$dimensions[1]}; padding-top: {$percentage}%;";
    }
} 