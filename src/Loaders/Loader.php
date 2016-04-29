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

namespace Machy8\Macdom\Loaders;

use Machy8\Macdom\Compiler;
use Machy8\Macdom\Setup\Setup;
use Machy8\Macdom\Setup\SetupChecker;

class Loader implements ILoader
{
	/** @var Setup */
	public $setup;

	/** @var SetupChecker */
	private $setupChecker;

	/** Loader constructor */
	public function __construct()
	{
		$this->setup = new Setup;
		$this->setupChecker = new SetupChecker($this->setup);
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function compile($content)
	{
		$compiler = new Compiler($this->setup, $this->setupChecker);
		$compiled = $compiler->compile($content);
		return $compiled;
	}
}
