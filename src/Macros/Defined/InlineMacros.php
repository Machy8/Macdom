<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom\Macros\Defined;

class InlineMacros implements IMacros {

	/**
	 * @param string $element
	 * @param string $line
	 * @return string $replacement
	 */
	public function doctypeMacros ($element, $line)
	{
		$replacement = NULL;

		switch($element)
		{
		case '!5':
			$replacement = '<!DOCTYPE html>';
			break;
		case '!':
			$replacement = '<!DOCTYPE '.$line.'>';
			break;
		}

		return $replacement;
	}

	/**
	 * @param string $element
	 * @param string $line
	 * @return string $replacement
	 */
	public function headMacros ($element, $line)
	{
		$replacement = NULL;

		switch($element)
		{
		// Meta tags
		case 'charset':
			$replacement = '<meta charset="'.$line.'" />';
			break;
		case 'keywords':
			$replacement = '<meta name="Keywords" content="'.$line.'" />';
			break;
		case 'description':
			$replacement = '<meta name="Description" content="'.$line.'" />';
			break;
		case 'author':
			$replacement = '<meta name="Author" content="'.$line.'" />';
			break;
		case 'viewport':
			$replacement = '<meta name="viewport" content="'.$line.'" />';
			break;

		// Basic Facebook meta tags
		case 'og-title':
			$replacement = '<meta property="og:title" content="'.$line.'" />';
			break;
		case 'og-description':
			$replacement = '<meta property="og:description" content="'.$line.'" />';
			break;
		case 'og-type':
			$replacement = '<meta property="og:type" content="'.$line.'" />';
			break;
		case 'og-url':
			$replacement = '<meta property="og:url" content="'.$line.'" />';
			break;
		case 'og-image':
			$replacement = '<meta property="og:image" content="'.$line.'" />';
			break;

		// Basic Twitter meta tags
		case 'tw-card':
			$replacement = '<meta name="twitter:card" content="'.$line.'" />';
			break;
		case 'tw-site':
			$replacement = '<meta name="twitter:site" content="'.$line.'" />';
			break;
		case 'tw-title':
			$replacement = '<meta name="twitter:title" content="'.$line.'" />';
			break;
		case 'tw-description':
			$replacement = '<meta name="twitter:description" content="'.$line.'" />';
			break;
		case 'tw-image':
			$replacement = '<meta name="twitter:image" content="'.$line.'" />';
			break;

		// Link in the head tag
		case 'stylesheet':
			$replacement = '<link rel="stylesheet" type="text/css" href="'.$line.'">';
			break;
		case 'favicon':
			$replacement = '<link rel="shortcut icon" href="'.$line.'">';
			break;
		}

		return $replacement;
	}

	/**
	 * @param string $element
	 * @param string $line
	 * @return string $replacement
	 */
	public function globalMacros ($element, $line) 
	{
		$replacement = NULL;

		switch($element)
		{
		// Html condition comments
		case '!if':
			$replacement = '<!--[if '.$line.']-->';
			break;
		case '!endif':
			$replacement = '<!--[endif]-->';
			break;
		case '!if-!ie':
			$replacement = '<!--[if !IE]-->';
			break;
		case '!if-ie6':
			$replacement = '<!--[if IE 6]-->';
			break;
		case '!if-ie7':
			$replacement = '<!--[if IE 7]-->';
			break;
		case '!if-ie8':
			$replacement = '<!--[if IE 8]-->';
			break;

		// Script tag
		case 'js':
			$replacement = '<script language="javascript" type="text/javascript" src="'.$line.'"></script>';
			break;
		case 'js-async':
			$replacement = '<script language="javascript" async type="text/javascript" src="'.$line.'"></script>';
			break;
		}

		return $replacement;
	}
}