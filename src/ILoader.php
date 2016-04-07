<?php

namespace Machy8\Macdom;

interface ILoader
{
	/** Loader constructor */
	public function __construct();

	/**
	 * @param string $content
	 * @return string
	 */
	public function compileContent($content);
}