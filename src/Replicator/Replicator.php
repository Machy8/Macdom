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
use Tracy\Debugger;

class Replicator extends Register {

	private $caRegExp = "/\[(.*?)\]/";

	public function __construct() {
		parent::__construct();
	}

	public function detect ($lvl, $element, $line) {
		$replicate = FALSE;
		$clearLine = FALSE;
		$replacement = NULL;

		$registrationLine = preg_match("/".$this->regExp."/", $line);

		if($registrationLine === 1){
			$clearLine = TRUE;
			$removeElement = str_replace($element, "", $line);
			$line = trim($removeElement);
		}

		$deregister = $this->deregisterLvl($lvl, $element);

		if($deregister === FALSE and strlen($line) !== 0){
			$isRegistered = $this->isRegistered($lvl, $element, $line, $registrationLine);

			if($isRegistered['registered'] === TRUE and $registrationLine !== 1 and $registrationLine !== FALSE){
				$replacement = $this->replicate($isRegistered['registerId'], $line);
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

	private function replicate ($registerId, $line){
		$registeredLine = $this->getRegisteredLine($registerId);

		$contentArrays = preg_match_all($this->caRegExp, $line, $matches);

		if($contentArrays !== 0 and $contentArrays !== FALSE){
			$line = $this->synchronizeLines($line, $registeredLine, $matches[1]);
		}
		else{
			$line = $this->synchronizeLines($line, $registeredLine);
		}

		return $line;
	}

	private function synchronizeLines ($line, $registeredLine, $matches = NULL){

		$replicatedLine = NULL;

		if($matches !== NULL){

			foreach($matches as $key => $match){
				$exists = preg_match($this->caRegExp, $registeredLine);

				if($exists === 1){
					$replaceRegisteredLine = preg_replace($this->caRegExp, $match, $registeredLine, 1);
					$replaceLine = preg_replace("/\[".$match."\]/", "", $line, 1);
					$registeredLine = $replaceRegisteredLine;
					$line = $replaceLine;
				}
				else {
					break;
				}
			}
		}

		$replaceRegisteredLine = preg_replace("/\[\]/", "", $registeredLine);
		$registeredLine = $replaceRegisteredLine;

		$replicatedLine = $registeredLine.$line;

		return $replicatedLine;
	}

}
