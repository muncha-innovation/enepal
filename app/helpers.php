<?php


use App\Services\DateFormatter;

if (!function_exists('getLanguageFromCode')) {
    function getLanguageFromCode($code)
    {

        return match ($code) {
            'en' => 'English',
            'ja' => '日本語',
            default => trans('English'),
        };
    }
}

if (!function_exists('getColorClassForLog')) {
    function getColorClassForLog($logType)
    {
        return match ($logType) {
            'restored' => 'bg-green-500',
            'deleted' => 'bg-red-500',
            'search' => 'bg-cyan-500',
            'download' => 'bg-amber-500',
            'created' => 'bg-emerald-500',
            default => 'bg-violet-500'
        };
    }
}

if (!function_exists('formatDateToJp')) {
    function formatDateToJp($date)
    {
        return DateFormatter::utcToJp($date);
    }
}

if (!function_exists('file_path')) {
    /**
     * Get the full file path given the folder path and file name.
     *
     * @param string $path
     * @param string $filename
     * @param string $folder The folder inside the path
     * @return string
     */
    function file_path($path, $filename, $folder = null)
    {
        return rtrim($path, '/') . ($folder ? "/{$folder}/" : '/') . $filename;
    }
}
if(!function_exists('upload')) {
    function upload(string $dir, string $format, $image = null)
    {
        if ($image != null) {
            $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->putFileAs($dir, $image, $imageName);
        } else {
            $imageName = 'def.png';
        }
    
        return $imageName;
    }
}
