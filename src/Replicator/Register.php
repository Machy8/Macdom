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

class Register {

	/** @var string */
	private $character = "@";

	/** @var string */
	private $prefix = "ln-";

	/** @var regular expression */
	protected $regExp;

	/** @var array */
	private $register = [];

	protected function __construct()
	{
		$this->regExp = $this->character.'([\S]*)'; 
	}

	/**
	 * @param integer $lvl
	 * @param string $element
	 * @return boolean $unregistered
	 */
	protected function deregisterLvl ($lvl, $element)
	{
		$unregistered = FALSE;

		$match = preg_match("/\/".$this->regExp."/", $element, $matches);
		if($match === 1){
			$selected = $this->prefix.$lvl;

			if(!empty($matches[1])){
				$selected .= '-'.$matches[1];

				if(array_key_exists($selected, $this->register)){
					unset($this->register[$selected]);
					$unregistered = TRUE;
				}
			}
			elseif(array_key_exists($selected, $this->register)){
				unset($this->register[$selected]);
				$unregistered = TRUE;
			}
		}

		return $unregistered;
	}

	/**
	 * @param integer $lvl
	 * @param string $element
	 * @param string $line
	 * @param integer $registrationLine
	 * @return array [registered, registerId]
	 */
	protected function isRegistered ($lvl, $element, $line, $registrationLine)
	{
		$registered = FALSE;
		$registereId = NULL;

		$selected = $this->prefix.$lvl;

		if($registrationLine === 0){

			if(array_key_exists($selected.'-'.$element, $this->register)){
				$registered = TRUE;
				$registerId = $selected.'-'.$element;
			}
			elseif(array_key_exists($selected, $this->register)){
				$registered = TRUE;
				$registerId = $selected;
			}
		}

		if($registered === FALSE or $registrationLine === 1){
			$registerLvl = $this->registerLvl($element, $line, $lvl);
			$registered = $registerLvl['registered'];
			$registerId = $registerLvl['registerId'];
		}

		return 
		[
			'registered' => $registered,
			'registerId' => $registerId
		];

	}

	/**
	 * @param string $element
	 * @param string $line
	 * @param integer $lvl
	 * @return array [registered, registerId]
	 */
	private function registerLvl ($element, $line, $lvl)
	{
		$registered = FALSE;
		$registerId = NULL;

		$match = preg_match("/".$this->regExp."/", $element, $matches);

		if($match === 1){

			$newKey = $this->prefix.$lvl;

			if(!empty($matches[1])){
				$newKey .= '-'.$matches[1];
			}

			$this->register[$newKey] = $line;

			$registered = TRUE;
			$registerId = $newKey;

		}

		return 
		[
			'registered' => $registered,
			'registerId' => $registerId
		];
	}

	/**
	 * @param string $registerId
	 * @return string $registeredLine
	 */
	protected function getRegisteredLine ($registerId)
	{
		return $this->register[$registerId];
	}
}
