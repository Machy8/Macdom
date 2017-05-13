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

namespace Macdom;


final class Token
{

	const
		ELEMENT = 'element',
		ELEMENT_OPEN_TAG = 'elementOpenTag',
		ELEMENT_CLOSE_TAG = 'elementCloseTag',
		MACRO = 'macro',
		REPLICATOR_OPEN_TAG = 'replicatorOpenTag',
		REPLICATOR_REPLICANT = 'replicatorReplicant',
		REPLICATOR_CLOSE_TAG = 'replicatorCloseTag',
		TEXT = 'text',
		UNFORMABLE_TEXT = 'unformableText',

		REGULAR_EXPRESSION_MACRO = 'regularExpressionMacro',
		CONTENT_SKIPPED = 'contentSkipped',
		SKIPPED_ELEMENT = 'skippedElement',
		UNPAIRED_ELEMENT = 'unpairedElement';

	/**
	 * @var int
	 */
	public $indentationLevel;

	/**
	 * @var string
	 */
	public $keyword;

	/**
	 * @var int
	 */
	public $line;

	/**
	 * @var string|string[]
	 */
	public $text;

	/**
	 * @var string
	 */
	public $type;

}
