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
		REG_EXP_B = '/\[\@\]/';

	/**
	 * @param int $lvl
	 * @param string $element
	 * @param string $ln
	 * @return array|bool
	 */
	public function detect($lvl, $element, $ln)
	{
		$clearLn = FALSE;
		$replacement = NULL;
		if (preg_match('/^' . parent::REG_EXP . '/', $ln, $regLn)) {
			$clearLn = TRUE;
			$ln = preg_replace('/' . preg_quote($element) . '/', '', $ln, 1);
		}
		$deregister = $this->deregisterLvl($lvl, $element);
		if (!$deregister) {
			$isRegistered = $this->isRegistered($lvl, $element, $ln, $regLn);
			if ($isRegistered['registered'] && !$regLn) {
				$key = $isRegistered['key'];
				$replacement = $key
					? $this->replicate($isRegistered['registerId'], $ln, $element, $key)
					: $this->replicate($isRegistered['registerId'], $ln);
			}
		} else {
			$clearLn = TRUE;
		}
		return [
			'clearLn' => $clearLn,
			'toReplicate' => $replacement
		];
	}

	/**
	 * @param string $regId
	 * @param string $ln
	 * @param string $element
	 * @param bool $key
	 * @return string $replicatedLn
	 */
	private function replicate($regId, $ln, $element = NULL, $key = FALSE)
	{
		$contentArrays = preg_match_all(self::REG_EXP_A, $ln, $matches);
		if ($key)
			$ln = preg_replace('/' . preg_quote($element) . '/', '', $ln, 1);
		$replicatedLn = $contentArrays
			? $this->synchronizeLines($ln, $regId, $matches[1])
			: $this->synchronizeLines($ln, $regId);
		return $replicatedLn;
	}

	/**
	 * @param string $ln
	 * @param string $regId
	 * @param array $matches
	 * @return string $synchronizedLn
	 */
	private function synchronizeLines($ln, $regId, $matches = NULL)
	{
		$regLn = $this->getRegisteredLine($regId);
		if ($matches) {
			foreach ($matches as $match) {
				$exists = preg_match(self::REG_EXP_B, $regLn);
				if ($exists) {
					$regLn = preg_replace(self::REG_EXP_B, $match, $regLn, 1);
					$ln = ltrim(str_replace('[' . $match . ']', '', $ln));
				} else {
					break;
				}
			}
		}
		$clear = preg_replace(self::REG_EXP_B, '', $regLn);
		return trim($clear . $ln);
	}
}
