<?php

namespace Machy8\Macdom\Loaders;

use Machy8\Macdom\Compiler;
use Machy8\Macdom\Setup\Setup;
use Machy8\Macdom\Setup\SetupChecker;

trait LoadersCore
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