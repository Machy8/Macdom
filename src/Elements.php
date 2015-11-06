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

class Elements
{

	protected $elements = [];
	protected $elementsSettings = [];
	protected $booleanAttributes = [];

	public function __construct ()
	{
		$this->setBooleanAttributes();
		$this->setElements();
		$this->setElementsSettings();
	}

	private function setBooleanAttributes ()
	{
		$this->booleanAttributes =
		[
			'autofocus',
			'async',
			'disabled',
			'default',
			'formnovalidate',
			'checked',
			'multiple',
			'open',
			'readonly',
			'required',
			'selected',
			'contentEditable',
			'n:ifcontent'
		];
	}

	private function setElements ()
	{
		$this->elements =
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

	private function setElementsSettings ()
	{
		$this->elementsSettings = array
		(
			// Document metadata
			'html' =>
			[
				'qkAttributes' => ['lang']
			],

			'meta' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['name', 'content']
			],

			'link' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['href', 'rel', 'title']
			],

			'style' =>
			[
				'qkAttributes' => ['type']
			],

			// Text content
			'hr' =>
			[
				'paired' => FALSE
			],

			'ol' =>
			[
				'qkAttributes' => ['type', 'start', 'reversed']
			],

			// Inline text semantic
			'a' =>
			[
				'qkAttributes' => ['href', 'target', 'role']
			],

			'abbr' =>
			[
				'qkAttributes' => ['title']
			],

			'bdo' =>
			[
				'qkAttributes' => ['dir']
			],

			'br' =>
			[
				'paired' => FALSE
			],

			'data' =>
			[
				'qkAttributes' => ['value']
			],

			'q' =>
			[
				'qkAttributes' => ['cite']
			],

			'wbr' =>
			[
				'paired' => FALSE
			],

			// Image and multimedia
			'img' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['src', 'alt']
			],

			'audio' =>
			[
				'qkAttributes' => ['src']
			],

			'track' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['src', 'srclang', 'kind']
			],

			'track' =>
			[
				'qkAttributes' => ['src']
			],

			// Embedded content
			'embed' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['src', 'type', 'width', 'height']
			],

			'iframe' =>
			[
				'qkAttributes' => ['src', 'frameborder', 'width', 'height']
			],

			'object' =>
			[
				'qkAttributes' => ['data', 'type']
			],

			'param' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['name', 'value']
			],

			'source' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['src', 'type']
			],

			// Scripting
			'canvas' =>
			[
				'qkAttributes' => ['width', 'height']
			],

			'script' =>
			[
				'qkAttributes' => ['src', 'type']
			],

			// Table content
			'col' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['span']
			],

			'td' =>
			[
				'qkAttributes' => ['rowspan', 'colspan']
			],

			// Forms
			'button' =>
			[
				'qkAttributes' => ['type', 'value']
			],

			'form' =>
			[
				'qkAttributes' => ['method']
			],

			'input' =>
			[
				'paired' => FALSE,
				'qkAttributes' => ['type', 'value', 'placeholder']
			],

			'textarea' =>
			[
				'qkAttributes' => ['placeholder']
			],

			'label' =>
			[
				'qkAttributes' => ['for']
			],

			'progress' =>
			[
				'qkAttributes' => ['value', 'max']
			],

			'optgroup' =>
			[
				'qkAttributes' => ['label']
			],

			'option' =>
			[
				'qkAttributes' => ['value']
			],

			// Interactive elements
			'menu' =>
			[
				'qkAttributes' => ['type', 'label']
			],

			'menuitem' =>
			[
				'qkAttributes' => ['type']
			]

		);
	}
}
