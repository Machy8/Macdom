<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2016 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

namespace Machy8\Macdom\Setup;


class Setup
{

	/**
	 * @var string
	 */
	public $addBooleanAttributes = '';

	/**
	 * @var array
	 */
	public $addElements = [];

	/**
	 * @var array
	 */
	public $addMacros = [];

	/**
	 * @var array
	 */
	public $addQkAttributes = [];

	/**
	 * @var bool
	 */
	public $booleansWithValue = FALSE;

	/**
	 * @var array
	 */
	public $changeQkAttributes = [];

	/**
	 * @var bool
	 */
	public $closeSelfClosingTags = FALSE;

	/**
	 * @var bool
	 */
	public $compressCode = FALSE;

	/**
	 * @var bool
	 */
	public $compressText = FALSE;

	/**
	 * @var string
	 */
	public $indentMethod = 'combined';

	/**
	 * @var bool
	 */
	public $blankLine = FALSE;

	/**
	 * @var string
	 */
	public $outputIndentation = "tabs";

	/**
	 * @var bool
	 */
	public $preferXhtml = FALSE;

	/**
	 * @var string
	 */
	public $removeBooleanAtributes = '';

	/**
	 * @var string
	 */
	public $removeElements = '';

	/**
	 * @var string
	 */
	public $removeMacros = '';

	/**
	 * @var string
	 */
	public $skipElements = '';

	/**
	 * @var int
	 */
	public $spacesPerIndent = 4;

	/**
	 * @var bool
	 */
	public $structureHtmlSkeleton = TRUE;

	/**
	 * @var string
	 */
	public $trim = 'left';

}
