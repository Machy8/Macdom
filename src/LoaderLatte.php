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

class LoaderLatte extends SetupLatte {

	/**
	 * @param string $file
	 * @return string $compiled
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function getContent($file) {
		$content = parent::getContent($file);
		$compiled = $this->compileContent($content);
		return $compiled;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function compileContent($content) {
		$compiler = new Compiler($this->elements, $this->macros, new Replicator, $this->indentMethod, $this->spacesCount, $this->compressCode);
		$compiled = $compiler->compile($content);
		return $compiled;
	}
}
