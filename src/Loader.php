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

use Machy8\Macdom\Replicator\Replicator;

class Loader extends Setup
{
	/**
	 * @param string $file
	 * @return string $compiled
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function getContent ($file)
	{
		$content = parent::getContent($file);
		$compiler = new Compiler($this->elements, $this->macros, new Replicator, $this->indentMethod, $this->spacesCount);
		$compiled = $compiler->compile($content);

		return $compiled;
	}
}
