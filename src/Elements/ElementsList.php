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

namespace Machy8\Macdom\Elements;

use Machy8\Macdom\Elements\BooleanAttributes;

class ElementsList extends BooleanAttributes
{

	/** @var array */
	protected $elements =
	[
		// Document metadata
		'html',
		'base',
		'head',
		'link',
		'meta',
		'style',
		'title',

		// Content sectioning
		'address',
		'article',
		'body',
		'footer',
		'header',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'hgroup',
		'nav',
		'section',

		// Text content
		'dd',
		'div',
		'dl',
		'dt',
		'figcaption',
		'figure',
		'hr',
		'main',
		'ol',
		'ul',
		'li',
		'p',
		'pre',

		// Inline text semantic
		'a',
		'abbr',
		'b',
		'i',
		'bdi',
		'bdo',
		'br',
		'cite',
		'code',
		'data',
		'dfn',
		'em',
		'kbd',
		'mark',
		'q',
		'rp',
		'rt',
		'rtc',
		'ruby',
		's',
		'samp',
		'small',
		'span',
		'strong',
		'sub',
		'sup',
		'time',
		'u',
		'var',
		'wbr',

		// Multimedia
		'area',
		'audio',
		'img',
		'map',
		'track',
		'video',

		// Embedded content
		'embed',
		'iframe',
		'object',
		'param',
		'source',

		// Scripting
		'canvas',
		'noscript',
		'script',

		// Demarcating edits
		'del',
		'ins',

		// Table content
		'caption',
		'col',
		'colgroup',
		'table',
		'tbody',
		'td',
		'tfoot',
		'th',
		'thead',
		'tr',

		// Forms
		'button',
		'datalist',
		'fieldset',
		'form',
		'input',
		'label',
		'legend',
		'meter',
		'optgroup',
		'option',
		'output',
		'progress',
		'select',
		'textarea',

		// Interactive elements
		'details',
		'dialog',
		'menu',
		'menuitem',
		'summary'

		// User defined elements

	];
}
