<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Helpers {

    /**
	 * Helper for parse API sorting mechanism using url query
	 *
	 * @param  string $sort_text
	 * @return array
	 */
	public static function parseSortingPattern($sort_text)
	{
		$sorting_order = 'asc';
		$sorting_order_symbol = '+';

		if (Str::startsWith($sort_text, '-')) {
			$sorting_order = 'desc';
			$sorting_order_symbol = '-';
		}

		$sorting_order_column = Str::replaceFirst($sorting_order_symbol, '', $sort_text);

		return [
			'order' => $sorting_order,
			'symbol' => $sorting_order_symbol,
			'column' => $sorting_order_column
		];
	}

}