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

class Setup
{
	/** @var bool */
	public $compressCode = FALSE;

	/** @var bool */
	public $structureHtmlSkeleton = TRUE;

	/** @var bool */
	public $closeSelfClosingTags = FALSE;

	/** @var bool */
	public $preferXhtml = FALSE;

	/** @var bool */
	public $booleansWithValue = FALSE;

	/** @var int */
	public $indentMethod = 3;

	/** @var int */
	public $spacesPerIndent = 4;

	/** @var string */
	public $addElements = '';

	/** @var string */
	public $removeElements = '';

	/** @var string */
	public $addBooleanAttributes = '';

	/** @var string */
	public $removeBooleanAtributes = '';

	/** @var string */
	public $removeMacros = '';

	/*** @var array */
	public $addMacros = [];

	/** @var array */
	public $changeQkAttributes = [];

	/** @var array */
	public $ncaOpenTags = [];

	/** @var array */
	public $ncaCloseTags = [];

	/** @var array */
	public $ncaRegExpInlineTags = [];

	/** @var array */
	public $ncaRegExpOpenTags = [];
}
