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

	const
		ELEMENT_OPEN_TAG_RE = '/^<(?<element>\S+)(?:[^>]+)?\/?>/',
		ELEMENT_CLOSE_TAG_RE = '/^<\/[^\>]+>/';


	public static function explodeString(string $string): array
	{
		return explode(' ', trim($string));
	}


	public static function getFirstWord(string $string): string
	{
		$keyword = strtok(trim($string), " ");

		return $keyword ? $keyword : '';
	}


	public static function getIndentation(string $string, string &$matches = NULL): bool
	{
		$matched = preg_match('/^\s+/', $string, $matches);
		$matches = $matched ? $matches[0] : '';

		return (bool) $matched;
	}


	public static function matchElementCloseTag(string $string, array &$matches = NULL, string $customTags = NULL): bool
	{
		return preg_match(self::ELEMENT_CLOSE_TAG_RE, $string, $matches)
			|| $customTags && preg_match('/' . $customTags . '/', $string, $matches);
	}


	public static function matchElementOpenTag(string $string, array &$matches = NULL, string $customTags = NULL): bool
	{
		return preg_match(self::ELEMENT_OPEN_TAG_RE, $string, $matches)
			|| $customTags && preg_match('/(?:' . $customTags . ')(?<element>\S+)/', $string, $matches);
	}


	public static function removeFirstWord(string $string): string
	{
		$string = strstr(trim($string), " ");

		return $string ? $string : '';
	}

}
