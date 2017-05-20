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

use Macdom\Compiler;


abstract class AbstractElementsManager
{

	/**
	 * @var Compiler
	 */
	private $compiler;


	public function __construct(Compiler $compiler)
	{
		$this->compiler = $compiler;
	}


	public function addBooleanAttribute(string $attribute, string $contentType = NULL): self
	{
		$this->compiler->addElementsBooleanAttribute($attribute, $contentType);

		return $this;
	}


	public function addElement(string $element, array $settings = NULL): self
	{
		$this->compiler->addElement($element, $settings);

		return $this;
	}


	public function addInlineSkipArea(string $regularExpression, string $contentType = NULL): self
	{
		$this->compiler->addElementsInlineSkipArea($regularExpression, $contentType);

		return $this;
	}

}
