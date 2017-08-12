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

namespace Macdom\Bridges\MacdomLatte;

use Latte\Loaders\FileLoader;
use Macdom\Engine;


final class MacdomLoader extends FileLoader
{

	/**
	 * @var Engine
	 */
	private $macdom;


	/**
	 * @param string $file
	 * @return string
	 */
	public function getContent($file): string
	{
		$content = parent::getContent($file);

		return $this->macdom->compile($content);
	}


	public function setMacdom(Engine $macdom): self
	{
		$this->macdom = $macdom;

		return $this;
	}

}
