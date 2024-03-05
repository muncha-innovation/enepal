<?php

namespace App\Services;

use Carbon\Carbon;

class DateFormatter
{

	public static function utcToJp(Carbon $date): string
	{
		return $date->timezone('Asia/Tokyo')->locale(app()->getLocale())->isoFormat('ll');
	}
}