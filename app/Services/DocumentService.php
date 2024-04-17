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

    public static function getFullPath(string $value): string
    {
        if (Storage::disk('public')->exists($value)) {
            return Storage::disk('public')->url($value);
        }
        return null;
    }


    public function getResizedFile($value, $type = 'medium')
    {
        $file = explode('/', $value);
        $path = Arr::first($file) . '/' . $type . '/' . Arr::last($file);

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
    }

    private function resizeAndStore($file, $uniqueName, $disk = 'public', $folder = 'images')
    {
        $resize = Image::make($file);
        $medium = $resize->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        })->stream();

        $thumbnail = $resize->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
        })->stream();
        Storage::disk($disk)->put($folder . '/medium/' . $uniqueName, $medium);
        Storage::disk($disk)->put($folder . '/thumbnail/' . $uniqueName, $thumbnail);
    }
}