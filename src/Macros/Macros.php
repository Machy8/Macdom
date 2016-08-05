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

class Macros extends CoreMacros
{

	/**
	 * @param string $macro
	 * @return bool
	 */
	public function findMacro($macro)
	{
		return array_key_exists($macro, $this->macros);
	}

	
	/**
	 * @param string $macro
	 * @param string $ln
	 * @return string
	 */
	public function replace($macro, $ln)
	{
		$ln = trim(strstr($ln, ' '));

		return call_user_func($this->macros[$macro], $ln);
	}

}
