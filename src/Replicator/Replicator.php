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
	 * @param string $txt
	 * @return array|bool
	 */
	public function detect($lvl, $element, $txt)
	{
		$clearLn = FALSE;
		$replacement = NULL;

		if (preg_match('/^' . self::REG_EXP_C . '/', $element, $key)) {
			$clearLn = TRUE;
			$txt = preg_replace('/' . $key[0] . '/', '', ltrim($txt), 1);
			$key = isset($key[1]) ? $key[1] : NULL;
			$this->registerLvl($key, $lvl, $txt);
		}

		if (!preg_match('/^\/' . self::REG_EXP_C . '/', $element, $deregLn) && !$key) {
			$regLn = $this->isRegistered($lvl, $element);

			if ($regLn['ln']) {
				if ($regLn['key']) $txt = preg_replace('/' . preg_quote($element) . '/', '', $txt, 1);

				$replacement = $this->synchronizeLines($txt, $regLn['ln']);
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
