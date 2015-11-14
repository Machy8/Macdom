<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015 Vladimír Macháček
 *
 * For the full copyright and license  information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom\CompilerMacros\Defined;

class AdvancedMacros implements IMacros {

	/**
	 * @param string $element
	 * @param string $line
	 * @return string $replacement
	 */
	public function doctypeMacros ($element, $line)
	{
		$replacement = NULL;

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

		return $replacement;
	}
}
