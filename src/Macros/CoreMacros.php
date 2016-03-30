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

	/**
	 * CoreMacros constructor
	 */
	public function __construct()
	{
		// Doctype
		$this->addMacro('doctype5', '!5');
		$this->addMacro('doctype', '!DOCTYPE');

		// Meta tags
		$this->addMacro('charset', 'charset');
		$this->addMacro('utf8', 'utf-8');
		$this->addMacro('keywords', 'keywords');
		$this->addMacro('description', 'description');
		$this->addMacro('author', 'author');
		$this->addMacro('viewport', 'viewport');

		// Twitter + Facebook
		$this->addMacro('facebook', 'fb');
		$this->addMacro('twitter', 'tw');

		// Stylesheet
		$this->addMacro('css', 'css');

		// Favicon
		$this->addMacro('favicon', 'favicon');

		// Javascript
		$this->addMacro('js', 'js');
		$this->addMacro('jsAsync', 'js-async');

		// Html comments
		$this->addMacro('inlineHtmlComment', '//');
		$this->addMacro('openHtmlComment', '/');
		$this->addMacro('closeHtmlComment', '\\');
	}

	/**
	 * @return string
	 */
	public function macroDoctype5()
	{
		return '<!DOCTYPE html>';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroDoctype($line)
	{
		return '<!DOCTYPE ' . $line . '>';
	}

	/**
	 * @return string
	 */
	public function macroUtf8()
	{
		return '<meta charset="utf-8" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroCharset($line)
	{
		return '<meta charset="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroKeywords($line)
	{
		return '<meta name="Keywords" content="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroDescription($line)
	{
		return '<meta name="Description" content="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroAuthor($line)
	{
		return '<meta name="Author" content="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroViewport($line)
	{
		return '<meta name="viewport" content="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroFacebook($line)
	{
		$selected = strtok($line, " ");
		$content = preg_replace("/" . $selected . " /", "", $line);
		return '<meta property="og:' . $selected . '" content="' . $content . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroTwitter($line)
	{
		$selected = strtok($line, " ");
		$content = trim(preg_replace("/" . $selected . "/", "", $line));
		return '<meta name="twitter:' . $selected . '" content="' . $content . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroCss($line)
	{
		return '<link rel="stylesheet" type="text/css" href="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroFavicon($line)
	{
		return '<link rel="shortcut icon" href="' . $line . '" />';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroJs($line)
	{
		return '<script type="text/javascript" src="' . $line . '"></script>';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroJsAsync($line)
	{
		return '<script type="text/javascript" src="' . $line . '" async></script>';
	}

	/**
	 * @param string $line
	 * @return string
	 */
	public function macroInlineHtmlComment($line)
	{
		return '<!--' . $line . '-->';
	}

	/**
	 * @return string
	 */
	public function macroOpenHtmlComment()
	{
		return '<!--';
	}

	/**
	 * @return string
	 */
	public function macroCloseHtmlComment()
	{
		return '-->';
	}
}
