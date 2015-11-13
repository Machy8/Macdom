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

namespace Machy8\Macdom\Macros;

use Machy\Macdom\Macros;

class Macros {
	
	public $replacer;
	public $registrator;
	
	/**
	 * @param \Machy8\Macdom\Macros\MacrosReplacer $replacer
	 * @param \Machy8\Macdom\Macros\MacrosRegistrator $registrator
	 */
	public function __construct (MacrosReplacer $replacer)
	{
		$this->replacer = $replacer;
		$this->replicator = $replicator;
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
