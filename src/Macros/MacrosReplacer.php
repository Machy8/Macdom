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

use Machy8\Macdom\Macros\Defined;

class MacrosReplacer {

	/** @var Defined\InlineMacros */
	public $InlineMacros;

	/** @var Defined\AdvancedMacros */
	public $AdvancedMacros;

	/** @var array */
	private $macrosAreasPassed = [];

	/**
	 * @param \Machy8\Macdom\Macros\Defined\InlineMacros $InlineMacros
	 * @param \Machy8\Macdom\Macros\Defined\AdvancedMacros $AdvancedMacros
	 */
	public function __construct(InlineMacros $InlineMacros, AdvancedMacros $AdvancedMacros) {

		$this->InlineMacros = $InlineMacros;
		$this->AdvancedMacros = $AdvancedMacros;

	}

	/**
	 * @param string
	 * @param string
	 * @return array [exists, replacement]
	 */
	public function detect ($element, $line)
	{

		$line = trim(strstr($line, " "));
		
		if($element === 'html' or $element === '<html>')
		{
			array_push($this->macrosAreasPassed, 'doctype');
		}
		elseif($element === '</head>' or $element === 'body')
		{
			array_push($this->macrosAreasPassed, 'head');
		}
		
		$macro = $this->replace('InlineMacros', $element, $line);

		if($macro['exists'] === FALSE)
		{
			$macro = $this->replace('AdvancedMacros', $element, $line);
		}

		return $macro;
	}

	/**
	 * @param string
	 * @param string
	 * @return array [exists, replacement]
	 */
	private function replace ($macros, $element, $line)
	{
		$replacement = NULL;
		$exists = FALSE;

		if(!in_array('doctype', $this->macrosAreasPassed))
		{
			$replacement = $this->${$macros}->doctypeMacros($element, $line);
		}

		if($this->replacement === NULL and !in_array('head', $this->macrosAreasPassed))
		{
			$replacement = $this->${$macros}->headMacros($element, $line);
		}

		if($this->replacement === NULL)
		{
			$replacement = $this->${$macros}->globalMacros($element, $line);
		}

		if($replacement !== NULL)
		{
			$exists = TRUE;
		}

		$macro =
		[
			'exists' => $exists,
			'replacement' => $replacement
		];

		return $macro;
	}
}
