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

namespace Macdom\Macros;

use Macdom\Register;
use Macdom\Engine;
use function Sodium\add;


final class CoreMacros extends AbstractMacrosManager
{

	public static function install(Register $register)
	{
		$macrosManager = new static($register);

		$macrosManager
			->addMacro('!5', function () {
				return '<!DOCTYPE html>';
			})
			->addMacro('!DOCTYPE', function ($line) {
				return '<!DOCTYPE ' . $line . '>';
			})
			->addMacro('charset', function ($line) {
				return '<meta charset="' . $line . '">';
			})
			->addMacro('utf-8', function () {
				return '<meta charset="utf-8">';
			})
			->addMacro('keywords', function ($line) {
				return '<meta name="keywords" content="' . $line . '">';
			})
			->addMacro('description', function ($line) {
				return '<meta name="description" content="' . $line . '">';
			})
			->addMacro('author', function ($line) {
				return '<meta name="author" content="' . $line . '">';
			})
			->addMacro('viewport', function ($line) {
				$viewport = '<meta name="viewport" content="';
				$viewport .= ! empty($line) ? $line : 'width=device-width, initial-scale=1';
				$viewport .= '">';

				return $viewport;
			})
			->addMacro('index-follow', function () {
				return '<meta name="robots" content="index, follow">';
			})
			->addMacro('no-index-follow', function () {
				return '<meta name="robots" content="noindex, nofollow">';
			})
			->addMacro('fb', function ($line) {
				$selected = strtok($line, " ");
				$content = trim(preg_replace("/" . $selected . "/", "", $line, 1));

				return '<meta property="og:' . $selected . '" content="' . $content . '">';
			})
			->addMacro('tw', function ($line) {
				$selected = strtok($line, " ");
				$content = trim(preg_replace("/" . $selected . "/", "", $line, 1));

				return '<meta name="twitter:' . $selected . '" content="' . $content . '">';
			})
			->addMacro('css', function ($line) {
				return '<link rel="stylesheet" type="text/css" href="' . $line . '">';
			})
			->addMacro('favicon', function ($line) {
				return '<link rel="shortcut icon" href="' . $line . '">';
			})
			->addMacro('js', function ($line) {
				return '<script type="text/javascript" src="' . $line . '"></script>';
			})
			->addMacro('js-async', function ($line) {
				return '<script type="text/javascript" src="' . $line . '" async="true"></script>';
			})
			->addMacro('preload-css', function ($line) {
				return '<link rel="preload" href="' . $line . '" as="style">';
			})
			->addMacro('preload-js', function ($line) {
				return '<link rel="preload" href="' . $line . '" as="script">';
			})
			->addMacro('//', function ($line) {
				return '<!-- ' . $line . ' -->';
			})
			->addMacro('/*', function () {
				return '<!-- ';
			})
			->addMacro('*/', function () {
				return ' -->';
			})

			// Regular expression macros
			->addMacro('\.css$', function ($line, $keyword) {
				return 'link $' . $keyword . ' $stylesheet $text/css ' . $line . '';

			}, [Engine::REGULAR_EXPRESSION_MACRO])
			->addMacro('\.js$', function ($line, $keyword) {
				return 'script $' . $keyword . ' $text/javascript ' . $line;
			}, [Engine::REGULAR_EXPRESSION_MACRO])

			// XML macros
			->addMacro('!xml', function () {
				return '<?xml version="1.0" encoding="UTF-8" ?>';
			}, [Engine::CONTENT_XML])

			->addMacro('cdata', function ($line) {
				return '<![CDATA[ ' . $line . ' ]]>';
			});
	}

}
