<?php


use App\Services\DateFormatter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

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
if(!function_exists('getImage')) {
    function getImage(string $fileName = null, string $dir = null)
    {
        if($fileName==null) {
            return asset('images/profile/default.png');
        }
        if($dir!=null) {
            return Storage::disk('public')->url($dir.$fileName);
        } else {
            return Storage::disk('public')->url($fileName);
        }}
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
