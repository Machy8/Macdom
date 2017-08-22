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

use Macdom\Register;


abstract class AbstractMacrosManager
{

	/**
	 * @var Register
	 */
	private $register;


	public function __construct(Register $register)
	{
		$this->register = $register;
	}


	public function addMacro(string $keyword, \closure $macro, array $flags = NULL): self
	{
		$this->register->addMacro($keyword, $macro, $flags);

		return $this;
	}

}
