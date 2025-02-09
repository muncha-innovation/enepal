<?php

use App\Models\GeneralSetting;
use App\Services\DateFormatter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
if(!function_exists('upload')) {
    function upload($dir, $format, $image)
    {
        if ($image) {
            $imageName = time() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . '/' . $imageName, file_get_contents($image));
            return $dir . '/' . $imageName;
        } else {
            return 'def.png';
        }
    }
}
if (!function_exists('getLanguageFromCode')) {
    function getLanguageFromCode($code)
    {

        return match ($code) {
            'en' => 'English',
            'np' => 'नेपाली',
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

if (!function_exists('getImage')) {
    function getImage(string $fileName = null, string $dir = null)
    {
        if ($fileName == null) {
            return asset('images/profile/default.png');
        }
        else if (str_starts_with($fileName, asset(''))) {
            return $fileName;
        }
        return Storage::disk('public')->url($fileName);
    }
}

if (!function_exists('getFormattedDate')) {
    function getFormattedDate($date, $format = 'Y-m-d H:i')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('getUploadedFileFromBase64')) {
    function getUploadedFileFromBase64(string $base64File): UploadedFile
    {
        $fileData = base64_decode(Arr::last(explode(',', $base64File)));

        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        file_put_contents($tempFilePath, $fileData);

        $tempFileObject = new File($tempFilePath);
        $file = new UploadedFile(
            $tempFileObject->getPathname(),
            $tempFileObject->getFilename(),
            $tempFileObject->getMimeType(),
            0,
            true
        );
        app()->terminating(function () use ($tempFile) {
            fclose($tempFile);
        });
        return $file;
    }
}


if (!function_exists('menuActive')) {

    function menuActive($routeName, $type = null, $param = null)
    {
        if ($type == 3) $class = 'side-menu--open';
        elseif ($type == 2) $class = 'sidebar-submenu__open';
        else $class = 'active';

        if (is_array($routeName)) {
            foreach ($routeName as $key => $value) {
                if (request()->routeIs($value)) return $class;
            }
        } elseif (request()->routeIs($routeName)) {
            if ($param) {
                $routeParam = array_values(@request()->route()->parameters ?? []);
                if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
                else return;
            }
            return $class;
        }
    }
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if (Arr::has([
        'mail_config',
        'sms_config',
        'global_placeholders' => 'object',
        'socialite_credentials',
        'firebase_config',
    ], $key)) {
        return (object) @$general->$key;
    }
    if ($key) return @$general->$key;
    return $general;
}
