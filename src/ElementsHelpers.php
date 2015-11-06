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

namespace Machy8\Macdom;

class ElementsHelpers extends Elements
{

	public function __construct() {
	    parent::__construct();
	}

	protected function isBoolean ($attribute)
	{
		if (in_array($attribute, $this->booleanAttributes))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// @param string $el
	// @param string $method
	// method settings = @return array $settings
	// method exists = @return boolean
	protected function findElement ($el, $method)
	{
		$exists = FALSE;

		foreach ($this->elements as $element)
		{
			if ($element === $el)
			{
				$exists = TRUE;
				break;
			}
		}

		if ($exists === TRUE)
		{
			switch ($method)
			{
				case 'exists':
					return TRUE;
					break;
				case 'settings':
					$settings = $this->getElementSettings($el);
					return $settings;
					break;
			}
		}
		else
		{
			return FALSE;
		}
	}

	// @param string $el
	// @return array
	private function getElementSettings ($el)
	{
		$qkAttributes = NULL;
		$paired = TRUE;

		if (array_key_exists($el, $this->elementsSettings))
		{
			$settings = $this->elementsSettings[$el];

			if (array_key_exists('paired', $settings))
			{
				$paired = FALSE;
			}

			if (array_key_exists('qkAttributes', $settings))
			{
				$qkAttributes = $settings['qkAttributes'];
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
