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

	const
		/** @const regular expression */
		REG_EXP = '@([\S]*)',
		/** @const string */
		SUFFIX = '-x';

	/** @var array */
	private $register = [];

	/**
	 * @param int $lvl
	 * @param string $el
	 * @return bool
	 */
	protected function deregisterLvl($lvl, $el)
	{
		$unregistered = FALSE;
		if (preg_match('/^\/' . self::REG_EXP . '/', $el, $matches)) {
			$selected = $lvl . self::SUFFIX;
			if ($matches[1]) {
				$selected = $lvl . '-' . $matches[1];
				if (isset($this->register[$selected])) {
					unset($this->register[$selected]);
					$unregistered = TRUE;
				}
			} elseif (isset($this->register[$selected])) {
				unset($this->register[$selected]);
				$unregistered = TRUE;
			}
		}
		return $unregistered;
	}

	/**
	 * @param string $registerId
	 * @return string $registeredLine
	 */
	protected function getRegisteredLine($registerId)
	{
		return $this->register[$registerId];
	}

	/**
	 * @param int $lvl
	 * @param string $el
	 * @param string $ln
	 * @param string $registrationLn
	 * @return array
	 */
	protected function isRegistered($lvl, $el, $ln, $registrationLn)
	{
		$registered = $key = FALSE;
		$registerId = NULL;
		if (!$registrationLn) {
			if (array_key_exists($lvl . '-' . $el, $this->register)) {
				$registered = $key = TRUE;
				$registerId = $lvl . '-' . $el;
			} elseif (array_key_exists($lvl . self::SUFFIX, $this->register)) {
				$registered = TRUE;
				$registerId = $lvl . self::SUFFIX;
			}
		}
		if (!$registered || $registrationLn) {
			$registerLvl = $this->registerLvl($el, $ln, $lvl);
			$registered = $registerLvl['registered'];
			$registerId = $registerLvl['registerId'];
		}
		return [
			'registered' => $registered,
			'key' => $key,
			'registerId' => $registerId
		];
	}

	/**
	 * @param string $el
	 * @param string $ln
	 * @param int $lvl
	 * @return array
	 */
	private function registerLvl($el, $ln, $lvl)
	{
		$registered = FALSE;
		$registerId = NULL;
		if (preg_match('/^' . self::REG_EXP . '/', $el, $matches)) {
			$registerId = $lvl;
			$registerId .= $matches[1] ? '-' . $matches[1] : self::SUFFIX;
			$this->register[$registerId] = $ln;
			$registered = TRUE;
		}
		return [
			'registered' => $registered,
			'registerId' => $registerId
		];
	}
}
