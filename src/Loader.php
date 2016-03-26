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

namespace Machy8\Macdom;

use Machy8\Macdom\Replicator\Replicator;

class Loader extends Setup {

	/**
	 * @param string $file
	 * @return string $compiled
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @param sting $content
	 * @return string
	 */
	public function compileContent($content) {
		$compiler = new Compiler($this->elements, $this->macros, new Replicator, $this->indentMethod, $this->spacesCount, $this->compressCode);
		$compiled = $compiler->compile($content);
		return $compiled;
	}
}
