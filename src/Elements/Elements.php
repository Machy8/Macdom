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

namespace Machy8\Macdom\Elements;

class Elements extends ElementsSettings
{

	/**
	 * @param string $attribute
	 * @return bool
	 */
	public function isBoolean($attribute)
	{
		return in_array($attribute, $this->booleanAttributes);
	}

	/**
	 * @param string $el
	 * @param string $returnSettings
	 * @return bool|array
	 */
	public function findElement($el, $returnSettings)
	{
		$return = FALSE;
		if (in_array($el, $this->elements))
			$return = $returnSettings ? $this->getElementSettings($el) : TRUE;
		return $return;
	}

	/**
	 * @param string $el
	 * @return array
	 */
	private function getElementSettings($el)
	{
		$qkAttributes = NULL;
		$paired = TRUE;
		$settings = $this->elementsSettings;
		if (isset($settings[$el])) {
			$s = $settings[$el];
			$paired = in_array('unpaired', $s) ? FALSE : TRUE;
			if (isset($s['qkAttributes'])) {
				if (count($s['qkAttributes']))
					$qkAttributes = $s['qkAttributes'];
			}
		}
		return [
			'element' => $el,
			'paired' => $paired,
			'qkAttributes' => $qkAttributes
		];
	}

	/** @param array $elements */
	public function addElements($elements)
	{
		if ($elements) {
			foreach ($elements as $element => $settings) {
				$settingsExists = TRUE;
				if (is_integer($element)) {
					$settingsExists = FALSE;
					$element = $settings;
				}

				if (!in_array($element, $this->elements))
					$this->elements[] = $element;

				if ($settingsExists) {
					if (!isset($this->elementsSettings[$element]))
						$this->elementsSettings[] = $element;

					if ($settings)
						$this->elementsSettings[$element] = $settings;
				}
			}
		}
	}

	/** @param array $attributes */
	public function addBooleanAttributes($attributes)
	{
		if ($attributes && is_array($attributes)) {
			if (count($attributes)) {
				$merged = array_merge($this->booleanAttributes, $attributes);
				$this->booleanAttributes = $merged;
			}
		}
	}
}
