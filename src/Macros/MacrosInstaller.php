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
	public function addCustomMacros ($macros)
	{
		if ($macros) {
			foreach ($macros as $macro => $function) {
				if (is_callable($function)) $this->addMacro($macro, $function);
			}
		}
	}

	/**
	 * @param string $macro
	 * @param callable $function
	 */
	protected function addMacro ($macro, $function)
	{
		if (!array_key_exists($macro, $this->macros)) $this->macros[$macro] = $function;
	}

	/** @param array $macros */
	public function removeMacros ($macros)
	{
		if ($macros) {
			$macros = explode(' ', $macros);
			$this->macros = array_diff_key($this->macros, array_flip($macros));
		}
	}
}
