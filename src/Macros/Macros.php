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

use Machy8\Macdom\Macros\CoreMacros;

class Macros extends CoreMacros {

	/**
	 * @param string $macro
	 * @param string $line
	 * @return array [exists, replacement]
	 */
	public function replace ($macro, $line)
	{
		$replacement = NULL;
		$exists = FALSE;

		$line = trim(strstr($line, " "));

		foreach($this->macros as $macroId => $fnName){

			if($exists === TRUE){
				break;
			}

			$selectedMacro = $this->macros[$macroId];

			if($macro === $macroId){
				$fn = ucfirst($fnName);
				$replacement = $this->{'macro'.$fn}($line);
				$exists = TRUE;
				break;
			}
		}

		$macro =
		[
			'exists' => $exists,
			'replacement' => $replacement
		];

		return $macro;
	}
}
