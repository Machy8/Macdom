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

class Register {

	const
			/** @const regular expression */
			REG_EXP = '@([\S]*)',
			/** @const string */
			SUFFIX = '-x';

	/** @var REGEXP */
	protected $regExp;

	/** @var array */
	private $register = [];

	/**
	 * @param int $lvl
	 * @param string $element
	 * @return bool
	 */
	protected function deregisterLvl($lvl, $element) {
		$unregistered = FALSE;
		$match = preg_match('/^\/' . self::REG_EXP . '/', $element, $matches);
		if ($match) {
			$selected = $lvl . self::SUFFIX;
			if (!empty($matches[1])) {
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
	 * @param int $lvl
	 * @param string $element
	 * @param string $line
	 * @param int $registrationLine
	 * @return array
	 */
	protected function isRegistered($lvl, $element, $line, $registrationLine) {
		$registered = $key = FALSE;
		$registerId = NULL;
		if (!$registrationLine) {
			if (array_key_exists($lvl . '-' . $element, $this->register)) {
				$registered = $key = TRUE;
				$registerId = $lvl . '-' . $element;
			} elseif (array_key_exists($lvl . self::SUFFIX, $this->register)) {
				$registered = TRUE;
				$registerId = $lvl . self::SUFFIX;
			}
		}
		if (!$registered || $registrationLine) {
			$registerLvl = $this->registerLvl($element, $line, $lvl);
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
	 * @param string $element
	 * @param string $line
	 * @param int $lvl
	 * @return array
	 */
	private function registerLvl($element, $line, $lvl) {
		$registered = FALSE;
		$registerId = NULL;
		$match = preg_match('/^' . self::REG_EXP . '/', $element, $matches);
		if ($match) {
			$registerId = $lvl;
			$registerId .=!empty($matches[1]) ? '-' . $matches[1] : self::SUFFIX;
			$this->register[$registerId] = $line;
			$registered = TRUE;
		}
		return [
			'registered' => $registered,
			'registerId' => $registerId
		];
	}

	/**
	 * @param string $registerId
	 * @return string $registeredLine
	 */
	protected function getRegisteredLine($registerId) {
		return $this->register[$registerId];
	}
}
