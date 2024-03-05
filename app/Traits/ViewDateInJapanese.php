<?php

namespace App\Traits;

use App\Services\DateFormatter;

trait ViewDateInJapanese
{
	protected function serializeDate($date)
	{
		return DateFormatter::utcToJp($date);
	}
}