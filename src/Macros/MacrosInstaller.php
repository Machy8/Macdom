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

class MacrosInstaller
{

	/** @var array */
	protected $macros = [];

	/** @param array $macros */
	public function addCustomMacros($macros)
	{
		if ($macros && is_array($macros)) {
			foreach ($macros as $macroId => $function) {
				if (is_callable($function)) {
					if (!in_array($macroId, $this->macros))
						$this->macros[$macroId]['function'] = $function;
				}
			}
		}
	}

	/** @param array $macros */
	public function removeMacros($macros)
	{
		if ($macros && is_string($macros)) {
			$macros = explode(" ", $macros);
			$this->macros = array_diff_key($this->macros, array_flip($macros));
		}
	}

	/**
	 * @param string $fnName
	 * @param string $macroId
	 */
	protected function addMacro($fnName, $macroId)
	{
		if (!in_array($macroId, $this->macros))
			$this->macros[$macroId] = $fnName;
	}
}
