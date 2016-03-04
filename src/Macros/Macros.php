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
	public function replace($macro, $ln) {
		$replacement = NULL;
		$exists = FALSE;

		if (isset($this->macros[$macro])) {
			$line = trim(strstr($ln, ' '));
			if (isset($this->macros[$macro]['function'])) {
				$replacement = call_user_func($this->macros[$macro]['function'], $line);
			} else {
				$fn = ucfirst($this->macros[$macro]);
				$replacement = $this->{'macro' . $fn}($line);
			}
			$exists = TRUE;
		}
		return [
			'exists' => $exists,
			'replacement' => $replacement
		];
	}

}
