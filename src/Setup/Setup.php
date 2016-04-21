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

namespace Machy8\Macdom\Setup;

class Setup
{
	/** @var string */
	public $addBooleanAttributes = '';
	/** @var string */
	public $addElements = [];
	/*** @var array */
	public $addMacros = [];
	/** @var bool */
	public $booleansWithValue = FALSE;
	/** @var array */
	public $changeQkAttributes = [];
	/** @var bool */
	public $closeSelfClosingTags = FALSE;
	/** @var bool */
	public $compressCode = FALSE;
	/** @var string */
	public $finallCodeIndentation = "tabs";
	/** @var int */
	public $indentMethod = 3;
	/** @var array */
	public $ncaCloseTags = [];
	/** @var array */
	public $ncaOpenTags = [];
	/** @var array */
	public $ncaRegExpInlineTags = [];
	/** @var array */
	public $ncaRegExpOpenTags = [];
	/** @var bool */
	public $preferXhtml = FALSE;
	/** @var string */
	public $removeBooleanAtributes = '';
	/** @var string */
	public $removeElements = '';
	/** @var string */
	public $removeMacros = '';
	/** @var array */
	public $skipTags = [];
	/** @var int */
	public $spacesPerIndent = 4;
	/** @var bool */
	public $structureHtmlSkeleton = TRUE;
}
