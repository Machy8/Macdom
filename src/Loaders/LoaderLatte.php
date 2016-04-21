<?php

namespace Machy8\Macdom\Loaders;

use Latte\Loaders\FileLoader;
use Machy8\Macdom\Compiler;
use Machy8\Macdom\Setup\Setup;
use Machy8\Macdom\Setup\SetupChecker;

class LoaderLatte extends FileLoader implements ILoader
{
	/** @var \Machy8\Macdom\Setup\Setup */
	public $setup;

	private $setupChecker;

	/** Loader constructor */
	public function __construct()
	{
		$this->setup = new Setup;
		$this->setupChecker = new SetupChecker($this->setup);
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function getContent($file)
	{
		$content = parent::getContent($file);
		$compiled = $this->compileContent($content);
		return $compiled;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function compileContent($content)
	{
		$compiler = new Compiler($this->setup, $this->setupChecker);
		$compiled = $compiler->compile($content);
		return $compiled;
	}
}
