<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace Macdom;


final class Helpers
{

	public static function explodeString(string $string): array
	{
		return explode(' ', trim($string));
	}


	public static function getFirstWord(string $string): string
	{
		return preg_match('/^(?<firstWord>\S+)(?:(?=\S+)\s)?/', trim($string), $match)
			? $match['firstWord']
			: '';
	}


	public static function removeFirstWord(string $string): string
	{
		$string = preg_replace('/^\S+(?:(?=\S+)\s)?/', '', trim($string), 1);

		return $string ? $string : '';
	}

}
