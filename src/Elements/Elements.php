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
			$qkAttributes = isset($s['qkAttributes']) && count($s['qkAttributes']) ? $s['qkAttributes'] : NULL;
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
		if ($elements && is_array($elements)) {
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

	/** @param array $elements */
	public function removeElements($elements)
	{
		if ($elements && is_string($elements)) {
			$elements = explode(" ", $elements);
			$this->elements = array_diff($this->elements, $elements);
		}
	}

	/** @param array $elements */
	public function changeQkAttributes($elements)
	{
		if ($elements && is_array($elements) && !empty($elements)) {
			$removeAttributes = [];
			foreach ($elements as $element => $attributes) {
				foreach ($attributes as $actAttribute => $newAttribute) {
					if (array_key_exists($element, $this->elementsSettings) && array_key_exists("qkAttributes", $this->elementsSettings[$element]) && in_array($actAttribute, $this->elementsSettings[$element]["qkAttributes"])) {
						if ($newAttribute) {
							$attrKey = array_search($actAttribute, $this->elementsSettings[$element]["qkAttributes"]);
							$this->elementsSettings["$element"]["qkAttributes"][$attrKey] = $newAttribute;
						} else {
							$removeAttributes[] = $actAttribute;
						}
					}
				}
				if ($removeAttributes) {
					$this->elementsSettings[$element]["qkAttributes"] = array_diff($this->elementsSettings[$element]["qkAttributes"], $removeAttributes);
				}
			}
		}
	}

	/** @param array $attributes */
	public function addBooleanAttributes($attributes)
	{
		if ($attributes && is_string($attributes)) {
			$attributes = explode(" ", $attributes);
			$this->booleanAttributes = array_merge($this->booleanAttributes, $attributes);
		}
	}

	/** @param array $attributes */
	public function removeBooleanAttributes($attributes)
	{
		if ($attributes && is_string($attributes)) {
			$attributes = explode(" ", $attributes);
			$this->booleanAttributes = array_diff($this->booleanAttributes, $attributes);
		}
	}
}
