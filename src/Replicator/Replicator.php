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

class Replicator extends Register
{

	const
		/** @const regular expression */
		REG_EXP_A = '/\[(.*?)\]/',

		/** @const regular expression */
		REG_EXP_B = '/\[\@\]/',

		/** @const regular expression */
		REG_EXP_C = '@([\S]*)';


	/**
	 * @param int $lvl
	 * @param string $element
	 * @param string $ln
	 * @return array|bool
	 */
	public function detect($lvl, $element, $ln)
	{
		$clearLn = FALSE;
		$replacement = FALSE;
		preg_match('/^' . self::REG_EXP_C . '/', $element, $key);
		if (preg_match('/^' . self::REG_EXP_C . '/', $element, $regLn)) {
			$clearLn = TRUE;
			$ln = preg_replace('/' . self::REG_EXP_C . '/', '', $ln, 1);
			$key = $key && isset($key[1]) ? $key[1] : NULL;
			$this->registerLvl($key, $lvl, $ln);
		}

		if (!preg_match('/^\/' . self::REG_EXP_C . '/', $element, $deregLn) && !$regLn) {
			$regLn = $this->isRegistered($lvl, $element);
			if ($regLn['ln']) {
				if ($regLn['key']) $ln = preg_replace('/' . preg_quote($element) . '/', '', $ln, 1);
				$replacement = $this->synchronizeLines($ln, $regLn['ln']);
			}
		} elseif ($deregLn) {
			$clearLn = TRUE;
			$this->deregisterLvl($lvl, $deregLn[1]);
		}

		return [
			'clearLn' => $clearLn,
			'toReplicate' => $replacement
		];
	}

	/**
	 * @param string $ln
	 * @param string $regLn
	 * @return string
	 */
	private function synchronizeLines($ln, $regLn)
	{
		if (preg_match_all(self::REG_EXP_A, $ln, $matches)) {
			$matches = $matches[1];
			$regLn = preg_replace_callback(self::REG_EXP_B, function () use (&$matches) {
				return array_shift($matches);
			}, $regLn);
		}

		$ln = ltrim(preg_replace(self::REG_EXP_A, '', $ln));
		$clear = preg_replace(self::REG_EXP_B, '', $regLn);
		return trim($clear . $ln);
	}
}
