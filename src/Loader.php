<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom;

use Latte;
use Machy8\Macdom;

class Loader extends Latte\Loaders\FileLoader
{

	/** 
	 * @param string $file
	 * @return string $compiled
	 */
	public function getContent ($file)
	{
		$content = parent::getContent($file);
		$compiler = new Compiler;
		$compiled = $compiler->compile($content);

		return $compiled;
	}

}
