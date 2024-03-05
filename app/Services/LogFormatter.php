<?php
namespace App\Services;

use App\Models\LogTypes;

class LogFormatter
{
    static function format($log)
    {
        $type = $log->getExtraProperty('log_type');
        if ($type == LogTypes::$AddProcessChildren) {
            $parentId = $log->getExtraProperty('parent_id');
            if ($parentId) {
                $log = self::extract_process_names($log->description, 'This process', 'was added to');
                return trans('processAddedToProcess', ['first' => $log['first'], 'second' => $log['second']]);
            } else {
                $log = self::extract_process_names($log->description, 'A child', 'was added to');
                return trans('childAddedToProcess', ['first' => $log['first'], 'second' => $log['second']]);
            }

        }
        if ($type == LogTypes::$AddProcessToProduct) {
            $subjectType = $log->subject_type;
            if (str_contains($subjectType, 'Process')) {
                $log = self::extract_process_names($log->description, 'The process', 'was added to product');
                return trans('processAddedToProduct', ['first' => $log['first'], 'second' => $log['second']]);
            } else if (str_contains($subjectType, 'Product')) {
                $log = self::extract_process_names($log->description, 'The product', 'was added with a child');
                return trans('productWasAddedWithChild', ['first' => $log['first'], 'second' => $log['second']]);
            }
        }
        return trans($log->description);
    }

    static private function extract_process_names($input_string, $prefix1, $prefix2)
    {
        $matches = array();
        $pattern = '/' . preg_quote($prefix1) . '(.+?)' . preg_quote($prefix2) . '(.+)/';
        preg_match($pattern, $input_string, $matches);

        if (isset($matches[1]) && isset($matches[2])) {
            return array('first' => trim($matches[1]), 'second' => trim($matches[2]));
        } else {
            return null;
        }

    }
}