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

namespace Machy8\Macdom\Replicator;

class Register
{

	/** @var array */
	private $register = [];

	/**
	 * @param int $lvl
	 * @param string $ln
	 * @return bool
	 */
	protected function deregisterLvl ($lvl, $ln)
	{
		$selected = $ln ? $lvl . '-' . $ln : $lvl . '-x';

		if (isset($this->register[$selected])) unset($this->register[$selected]);
	}

	/**
	 * @param $lvl
	 * @param $el
	 * @return mixed|null
	 */
	protected function isRegistered ($lvl, $el)
	{
		$ln = NULL;
		$key = FALSE;

		if (array_key_exists($lvl . '-' . $el, $this->register)) {
			$ln = $this->register[$lvl . '-' . $el];
			$key = TRUE;

		} elseif (array_key_exists($lvl . '-x', $this->register)) {
			$ln = $this->register[$lvl . '-x'];
		}

		return [
			'ln' => $ln,
			'key' => $key
		];
	}

	/**
	 * @param string $key
	 * @param int $lvl
	 * @param string $ln
	 */
	protected function registerLvl ($key, $lvl, $ln)
	{
		$registerId = $key ? $lvl . '-' . $key : $lvl . '-x';
		$this->register[$registerId] = $ln;
	}
}
