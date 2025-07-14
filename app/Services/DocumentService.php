<?php

namespace App\Services;

use App\Models\AppSettings;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use finfo;

class DocumentService
{
    public function store(UploadedFile $file, string $folder = 'documents', $disk = 'public', $resize = true): string
    {

        if ($file == null)
            throw new Exception('File is required to store. No file provided');

        $imageName = $file->store($folder, ['disk' => $disk]);

        $isImage = in_array(Arr::last(explode('.', $imageName)), AppSettings::$imageExtensions);

        if ($resize && $isImage) {
            $uniqueName = Arr::last(explode('/', $imageName));
            $this->resizeAndStore($file, $uniqueName, $disk, $folder);
        }

        return $imageName;
    }

    public static function getFullPath(string $value): ?string
    {
        if (Storage::disk('public')->exists($value)) {
            return Storage::disk('public')->url($value);
        }
        return null;
    }


    public function getResizedFile($value, $type = 'medium'): ?string
    {
        $file = explode('/', $value);
        $path = Arr::first($file) . '/' . $type . '/' . Arr::last($file);

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
        
        return null;
    }

    private function resizeAndStore($file, $uniqueName, $disk = 'public', $folder = 'images')
    {
        $image = Image::make($file);
        
        // Determine the aspect ratio category based on folder/use case
        $aspectRatioType = $this->determineAspectRatioType($folder);
        
        // Create responsive sizes based on aspect ratio type
        $sizes = $this->getSizesForAspectRatio($aspectRatioType);
        
        foreach ($sizes as $sizeName => $dimensions) {
            $width = $dimensions['width'];
            $height = $dimensions['height'];
            
            // Clone the image for processing
            $processedImage = clone $image;
            
            // Resize maintaining aspect ratio with proper cropping
            $processedImage = $processedImage->fit($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Save processed image
            $processedPath = $folder . '/' . $sizeName . '/' . $uniqueName;
            Storage::disk($disk)->put($processedPath, $processedImage->stream());
        }
    }
    
    /**
     * Determine aspect ratio type based on folder/use case
     */
    private function determineAspectRatioType($folder)
    {
        if (strpos($folder, 'profile') !== false || strpos($folder, 'logo') !== false) {
            return 'square'; // 1:1 for profiles and logos
        } elseif (strpos($folder, 'gallery') !== false || strpos($folder, 'cover') !== false || strpos($folder, 'posts') !== false) {
            return 'widescreen'; // 16:9 for gallery covers and posts
        } elseif (strpos($folder, 'products') !== false) {
            return 'square'; // 1:1 for products
        } else {
            return 'traditional'; // 4:3 for traditional content
        }
    }
    
    /**
     * Get responsive sizes based on aspect ratio type
     */
    private function getSizesForAspectRatio($type)
    {
        switch ($type) {
            case 'square': // 1:1 ratio
                return [
                    'large' => ['width' => 800, 'height' => 800],
                    'medium' => ['width' => 400, 'height' => 400],
                    'small' => ['width' => 200, 'height' => 200],
                    'thumbnail' => ['width' => 150, 'height' => 150]
                ];
                
            case 'widescreen': // 16:9 ratio
                return [
                    'large' => ['width' => 1920, 'height' => 1080],
                    'medium' => ['width' => 800, 'height' => 450],
                    'small' => ['width' => 640, 'height' => 360],
                    'thumbnail' => ['width' => 320, 'height' => 180]
                ];
                
            case 'traditional': // 4:3 ratio
                return [
                    'large' => ['width' => 1200, 'height' => 900],
                    'medium' => ['width' => 800, 'height' => 600],
                    'small' => ['width' => 400, 'height' => 300],
                    'thumbnail' => ['width' => 200, 'height' => 150]
                ];
                
            default:
                return [
                    'large' => ['width' => 1200, 'height' => 900],
                    'medium' => ['width' => 800, 'height' => 600],
                    'small' => ['width' => 400, 'height' => 300],
                    'thumbnail' => ['width' => 200, 'height' => 150]
                ];
        }
    }
}