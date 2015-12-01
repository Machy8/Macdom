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

namespace Machy8\Macdom\Elements;

use Machy8\Macdom\Elements\ElementsSettings;
use Tracy\Debugger;

class Elements extends ElementsSettings
{

	public function isBoolean ($attribute)
	{
		$is = in_array($attribute, parent::BOOLEAN_ATTRIBUTES);
		
		return $is;
	}

	/**
	 * @param string $el
	 * @param string $method
	 * @return boolean
	 * @return array $settings
	 */
	public function findElement ($el, $method)
	{
		$exists = in_array($el, parent::ELEMENTS);
		$return = FALSE;

		if ($exists === TRUE){

			switch ($method){
				case 'exists':
					$return = TRUE;
					break;
				case 'settings':
					$return = $this->getElementSettings($el);
					break;
			}
		}
		
		return $return;
	}

	/**
	 * @param string $el
	 * @return array
	 */
	private function getElementSettings ($el)
	{
		$qkAttributes = NULL;
		$paired = TRUE;
		$settings = parent::ELEMENT_SETTINGS;
		
		if (isset($settings[$el])){
			$s = $settings[$el];
			
			if (isset($s['paired'])){
				$paired = FALSE;
			}

			if (isset($s['qkAttributes'])){
				$qkAttributes = $s['qkAttributes'];
			}
		}

		return
		[
			'element' => $el,
			'paired' => $paired,
			'qkAttributes' => $qkAttributes
		];
	}
}
