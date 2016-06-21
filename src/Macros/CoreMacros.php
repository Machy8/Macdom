<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2016 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom\Macros;

class CoreMacros extends MacrosInstaller
{

	/** CoreMacros constructor */
	public function __construct ()
	{
		$this->addMacro('!5', function () {
			return '<!DOCTYPE html>';
		});

		$this->addMacro('!DOCTYPE', function ($line) {
			return '<!DOCTYPE ' . $line . '>';
		});

		$this->addMacro('charset', function ($line) {
			return '<meta charset="' . $line . '">';
		});

		$this->addMacro('utf-8', function () {
			return '<meta charset="utf-8">';
		});

		$this->addMacro('keywords', function ($line) {
			return '<meta name="keywords" content="' . $line . '">';
		});

		$this->addMacro('description', function ($line) {
			return '<meta name="description" content="' . $line . '">';
		});

		$this->addMacro('author', function ($line) {
			return '<meta name="author" content="' . $line . '">';
		});

		$this->addMacro('viewport', function ($line) {
			$viewport = '<meta name="viewport" content="';
			$viewport .= !empty($line) ? $line : 'width=device-width, initial-scale=1';
			$viewport .= '">';

			return $viewport;
		});

		$this->addMacro('index-follow', function () {
			return '<meta name="robots" content="index, follow">';
		});

		$this->addMacro('no-index-follow', function () {
			return '<meta name="robots" content="noindex, nofollow">';
		});

		$this->addMacro('fb', function ($line) {
			$selected = strtok($line, " ");
			$content = preg_replace("/" . $selected . " /", "", $line);

			return '<meta property="og:' . $selected . '" content="' . $content . '">';
		});

		$this->addMacro('tw', function ($line) {
			$selected = strtok($line, " ");
			$content = trim(preg_replace("/" . $selected . "/", "", $line));

			return '<meta name="twitter:' . $selected . '" content="' . $content . '">';
		});

		$this->addMacro('css', function ($line) {
			return '<link rel="stylesheet" type="text/css" href="' . $line . '">';
		});

		$this->addMacro('favicon', function ($line) {
			return '<link rel="shortcut icon" href="' . $line . '">';
		});

		$this->addMacro('js', function ($line) {
			return '<script type="text/javascript" src="' . $line . '"></script>';
		});

		$this->addMacro('js-async', function ($line) {
			return '<script type="text/javascript" src="' . $line . '" async></script>';
		});

		$this->addMacro('//', function ($line) {
			return '<!--' . $line . '-->';
		});

		$this->addMacro('/*', function () {
			return '<!--';
		});

		$this->addMacro('*/', function () {
			return '-->';
		});
	}
}
