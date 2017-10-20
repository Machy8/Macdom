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

namespace Macdom\Bridges\Latte;

use Latte\Loaders\FileLoader as LatteFileLoader;
use Macdom\Engine;


final class FileLoader extends LatteFileLoader
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

		return $this->getMacdom()->compile($content);
	}


	public function setMacdom(Engine $macdom): self
	{
		$this->macdom = $macdom;

		return $this;
	}


	private function getMacdom(): Engine
	{
		return $this->macdom ?? new Engine();
	}

}
