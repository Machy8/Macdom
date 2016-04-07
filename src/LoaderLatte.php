<?php

namespace Machy8\Macdom;

use Latte\Loaders\FileLoader;

class LoaderLatte extends FileLoader implements ILoader
{
	/** @var \Machy8\Macdom\Setup */
	public $setup;

	/** Loader constructor */
	public function __construct()
	{
		$this->setup = new Setup;
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
		$compiler = new Compiler($this->setup);
		$compiled = $compiler->compile($content);
		return $compiled;
	}
}
