<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace Macdom\Elements;

use Macdom\Register;


abstract class AbstractElementsManager
{

	/**
	 * @var Register
	 */
	private $register;


	public function __construct(Register $register)
	{
		$this->register = $register;
	}


	/**
	 * @param string $attribute
	 * @param string|array|NULL $contentType
	 * @return AbstractElementsManager
	 */
	public function addBooleanAttribute(string $attribute, $contentType = NULL): self
	{
		$this->register->addBooleanAttribute($attribute, $contentType);

		return $this;
	}


	public function addElement(string $element, array $settings = NULL): self
	{
		$this->register->addElement($element, $settings);

		return $this;
	}

}
