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

namespace Machy8\Macdom\Setup;

use Exception;


class SetupChecker
{
	/** @var array */
	private $options = [
		'outputIndentation' => ['spaces', 'tabs'],
		'indentMethod' => ['spaces', 'tabs', 'combined']
	];

	/** @var array */
	private $register = [];

	/**
	 * SetupChecker constructor.
	 * @param object $setup
	 */
	public function __construct ($setup)
	{
		$setupVars = get_object_vars($setup);

		foreach ($setupVars as $key => $var) {
			$this->register[$key] = gettype($var);
		}
	}

	/**
	 * @param $setup
	 * @throws Exception
	 */
	public function check ($setup)
	{
		$setupVars = get_object_vars($setup);

		foreach ($setupVars as $key => $var) {
			$type = gettype($var);
			$throw = FALSE;
			$exceptionType = NULL;
			$options = array_key_exists($key, $this->options) ? $this->options[$key] : NULL;

			if ($type === $this->register[$key]) {
				if ($options && !in_array($var, $options)) {
					$throw = TRUE;
					$exceptionType = 'options';
				}

			} else {
				$throw = TRUE;
				$exceptionType = 'type';
			}

			if ($throw) $this->throwException($key, $options, $this->register[$key], $exceptionType);
		}
	}

	/**
	 * @param string $var
	 * @param array $options
	 * @param string $requiredType
	 * @param string $exceptionType
	 * @throws Exception
	 */
	private function throwException ($var, $options, $requiredType, $exceptionType)
	{
		if ($exceptionType === 'type') {
			$article = $requiredType === 'array' || $requiredType === 'integer' ? 'an' : 'a';
			$msg = 'must have ' . $article . ' ' . strtoupper($requiredType) . ' value.';

		} else {
			$allowedOptions = join(', ', $options);
			$msg = 'has illegal value. Options are: ' . $allowedOptions . '.';
		}

		throw new Exception('Variable $' . $var . ' ' . $msg);
	}
}
