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

namespace Macdom\Macros;

use Macdom\Compiler;


abstract class AbstractMacrosManager
{

	/**
	 * @var Compiler
	 */
	private $compiler;


	public function __construct(Compiler $compiler)
	{
		$this->compiler = $compiler;
	}


	public function addMacro(string $keyword, \closure $macro, array $flags = NULL): self
	{
		$this->compiler->addMacro($keyword, $macro, $flags);

		return $this;
	}

}
