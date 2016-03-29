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

use Latte\Loaders\FileLoader;
use Machy8\Macdom\Elements\Elements;
use Machy8\Macdom\Macros\Macros;

class SetupLatte extends FileLoader {

	/** @var Elements */
	protected $elements;

	/** @var Macros */
	protected $macros;

	/** @var bool */
	protected $compressCode;

	/** @var int */
	protected $indentMethod;

	/** @var int */
	protected $spacesCount;

	public function __construct() {
		$this->elements = new Elements;
		$this->macros = new Macros;
	}

	/**
	 * @param bool|null $compress
	 * @return SetupLatte
	 */
	public function compressCode($compress = NULL) {
		$this->compressCode = $compress ?: TRUE;
		return $this;
	}

	/**
	 * @param int $count
	 * @return SetupLatte
	 */
	public function spacesPerIndent($count) {
		$this->spacesCount = $count;
		return $this;
	}

	/**
	 * @param int $id
	 * @return SetupLatte
	 */
	public function indentMethod($id) {
		$this->indentMethod = $id;
		return $this;
	}

	/**
	 * @param array $elements
	 * @return SetupLatte
	 */
	public function addElements($elements) {
		$this->elements->addElements($elements);
		return $this;
	}

	/**
	 * @param array $attributes
	 * @return SetupLatte
	 */
	public function addBooleanAttributes($attributes) {
		$this->elements->addBooleanAttributes($attributes);
		return $this;
	}

	/**
	 * @param string $macroId
	 * @param function $function
	 * @return SetupLatte
	 */
	public function addMacro($macroId, $function) {
		$this->macros->addCustomMacro($macroId, $function);
		return $this;
	}
}
