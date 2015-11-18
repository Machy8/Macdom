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

use Latte\Loaders\FileLoader;
use Machy8\Macdom\Elements\Elements;
use Machy8\Macdom\Macros\Macros;
use Machy8\Macdom\Replicator\Replicator;

class Loader extends FileLoader
{
	/**
	 * @param string $file
	 * @return string $compiled
	 */
	public function getContent ($file)
	{
		$content = parent::getContent($file);
		$compiler = new Compiler(new Elements, new Macros, new Replicator);
		$compiled = $compiler->compile($content);

		return $compiled;
	}

}
