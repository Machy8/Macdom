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

namespace Machy8\Macdom\CompilerMacros;

use Machy8\Macdom\CompilerMacros\MacrosReplacer;

class Macros {
	
	public $replacer;
	
	/**
	 * @param \Machy8\Macdom\Macros\MacrosReplacer $replacer
	 * @param \Machy8\Macdom\Macros\MacrosRegistrator $registrator
	 */
	public function __construct ()
	{
		$this->replacer = new MacrosReplacer;
	}
	
	/**
	 * @param string
	 * @param string
	 * @return array [exists, replacement.]
	 */
	public function replace ($element, $line)
	{
		$macro = $this->replacer->detect($element, $line);
	
		return $macro;
	}
}
