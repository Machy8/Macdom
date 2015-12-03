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

namespace Machy8\Macdom\Replicator;

use Machy8\Macdom\Replicator\Register;

class Replicator extends Register
{
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
	public function detect ($lvl, $element, $line)
	{
		$replicate = FALSE;
		$clearLine = FALSE;
		$replacement = NULL;

		$registrationLine = preg_match("/".parent::REG_EXP."/", $line);

		if ($registrationLine === 1) {
			$clearLine = TRUE;
			$removeElement = preg_replace('/\\'.$element.'/', "", $line, 1);
			$line = $removeElement;
		}

		$deregister = $this->deregisterLvl($lvl, $element);

		if ($deregister === FALSE && strlen($line) !== 0) {
			$isRegistered = $this->isRegistered($lvl, $element, $line, $registrationLine);

			if ($isRegistered['registered'] === TRUE && $registrationLine !== 1 && $registrationLine !== FALSE) {

				// If the first word on line is also the part of the key in the register
				$key = $isRegistered['key'];

				if ($key === TRUE) {
					$replacement = $this->replicate($isRegistered['registerId'], $line, $element, $key);
				}
				else{
					$replacement = $this->replicate($isRegistered['registerId'], $line);
				}
				$replicate = TRUE;
			}

		}
		else {
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
	private function replicate ($registerId, $line, $element = NULL, $key = FALSE)
	{

		$contentArrays = preg_match_all(self::REG_EXP_A, $line, $matches);
		if ($key === TRUE) {
			$removeKey = preg_replace("/".$element."/", "", $line, 1);
			$line = $removeKey;
		}

		if ($contentArrays > 0) {
			$replicatedline = $this->synchronizeLines($line, $registerId, $matches[1]);
		}
		else{
			$replicatedline = $this->synchronizeLines($line, $registerId);
		}

		return $replicatedline;
	}

	/**
	 * @param string $line
	 * @param string $registerId
	 * @param array $matches
	 * @return string $synchronizedLine
	 */
	private function synchronizeLines ($line, $registerId, $matches = NULL)
	{
		$registeredLine = $this->getRegisteredLine($registerId);

		if ($matches !== NULL) {

			foreach ($matches as $key => $match) {
				$exists = preg_match(self::REG_EXP_B, $registeredLine);

				if ($exists === 1) {
					$synchronizeLine = preg_replace(self::REG_EXP_B, $match, $registeredLine, 1);
					$clearLine = str_replace('['.$match.']', "", $line);
					$registeredLine = $synchronizeLine;
					$line = $clearLine;
				}
				else {
					break;
				}
			}
		}

		$clearedRegisteredLine = preg_replace(self::REG_EXP_B, "", $registeredLine);

		$synchronizedLine = trim($clearedRegisteredLine.' '.$line);

		return $synchronizedLine;
	}

}
