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

use Machy8\Macdom\Replicator\Register;

class Replicator extends Register {

	const
	/** @const regular expression */
			REG_EXP_A = '/\[(.*?)\]/',
			/** @const regular expression */
			REG_EXP_B = '/\[\@\]/';

	/**
	 * @param integer $lvl
	 * @param string $element
	 * @param string $line
	 * @return array [replicate, clearLine, line]
	 */
	public function detect($lvl, $element, $line) {
		$replicate = FALSE;
		$clearLine = FALSE;
		$replacement = NULL;
		$registrationLine = preg_match('/' . parent::REG_EXP . '/', $line);
		if ($registrationLine) {
			$clearLine = TRUE;
			$removeElement = preg_replace('/\\' . $element . '/', '', $line, 1);
			$line = $removeElement;
		}
		$deregister = $this->deregisterLvl($lvl, $element);
		if (!$deregister && strlen($line)) {
			$isRegistered = $this->isRegistered($lvl, $element, $line, $registrationLine);
			if ($isRegistered['registered'] && !$registrationLine) {
				$replicate = TRUE;
				// If the first word on line is also the part of the key in the register
				$key = $isRegistered['key'];
				$replacement = $key === TRUE ? $this->replicate($isRegistered['registerId'], $line, $element, $key) : $this->replicate($isRegistered['registerId'], $line);
			}
		} else {
			$clearLine = TRUE;
		}
		return
				[
					'replicate' => $replicate,
					'clearLine' => $clearLine,
					'line' => $replacement
		];
	}

	/**
	 * @param string $registerId
	 * @param string $line
	 * @param string $element
	 * @param boolean $key
	 * @return string $replicatedLine
	 */
	private function replicate($registerId, $line, $element = NULL, $key = FALSE) {
		$contentArrays = preg_match_all(self::REG_EXP_A, $line, $matches);
		if ($key) {
			$removeKey = preg_replace('/' . $element . '/', '', $line, 1);
			$line = $removeKey;
		}
		$replicatedline = $contentArrays ? $this->synchronizeLines($line, $registerId, $matches[1]) : $this->synchronizeLines($line, $registerId);

		return $replicatedline;
	}

	/**
	 * @param string $line
	 * @param string $registerId
	 * @param array $matches
	 * @return string $synchronizedLine
	 */
	private function synchronizeLines($line, $registerId, $matches = NULL) {
		$registeredLine = $this->getRegisteredLine($registerId);
		if ($matches !== NULL) {
			foreach ($matches as $key => $match) {
				$exists = preg_match(self::REG_EXP_B, $registeredLine);
				if ($exists) {
					$synchronizeLine = preg_replace(self::REG_EXP_B, $match, $registeredLine, 1);
					$clearLine = str_replace('[' . $match . ']', '', $line);
					$registeredLine = $synchronizeLine;
					$line = $clearLine;
				} else {
					break;
				}
			}
		}
		$clearedRegisteredLine = preg_replace(self::REG_EXP_B, '', $registeredLine);
		$synchronizedLine = trim($clearedRegisteredLine . ' ' . $line);
		return $synchronizedLine;
	}

}
