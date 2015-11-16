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

use Nette;

class MacrosInstaller {

	public $macros = [];

	protected function addMacro($fnName, $macroId)
	{
		if($fnName !== NULL and $macroId !== NULL){

			if(!in_array($macroId, $this->macros)){
				$this->macros[$macroId] = $fnName;
			}
		}
	}
}
