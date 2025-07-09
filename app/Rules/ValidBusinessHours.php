<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidBusinessHours implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($value as $day => $schedule) {
            if (isset($schedule['is_open']) && $schedule['is_open']) {
                if (empty($schedule['open_time']) || empty($schedule['close_time'])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Enter opening hours';
    }
}
