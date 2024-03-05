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
