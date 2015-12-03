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

use Machy8\Macdom\Elements\ElementsList;

class ElementsSettings extends ElementsList {

	/** @var array */
	protected $elementsSettings = [
		// Document metadata
		'html' => [
			'qkAttributes' => ['lang']
		],

		'meta' => [
			'unpaired',
			'qkAttributes' => ['name', 'content']
		],

		'link' => [
			'unpaired',
			'qkAttributes' => ['href', 'rel', 'title']
		],

		'style' => [
			'qkAttributes' => ['type']
		],

		// Text content
		'hr' => [
			'unpaired'
		],

		'ol' => [
			'qkAttributes' => ['type', 'start', 'reversed']
		],

		// Inline text semantic
		'a' => [
			'qkAttributes' => ['href', 'target', 'role']
		],

		'abbr' => [
			'qkAttributes' => ['title']
		],

		'bdo' => [
			'qkAttributes' => ['dir']
		],

		'br' => [
			'unpaired'
		],

		'data' => [
			'qkAttributes' => ['value']
		],

		'q' => [
			'qkAttributes' => ['cite']
		],

		'wbr' => [
			'unpaired'
		],

		// Image and multimedia
		'img' => [
			'unpaired',
			'qkAttributes' => ['src', 'alt']
		],

		'audio' => [
			'qkAttributes' => ['src']
		],

		'track' => [
			'unpaired',
			'qkAttributes' => ['src', 'srclang', 'kind']
		],

		'track' => [
			'qkAttributes' => ['src']
		],

		// Embedded content
		'embed' => [
			'unpaired',
			'qkAttributes' => ['src', 'type', 'width', 'height']
		],

		'iframe' => [
			'qkAttributes' => ['src', 'frameborder', 'width', 'height']
		],

		'object' => [
			'qkAttributes' => ['data', 'type']
		],

		'param' => [
			'unpaired',
			'qkAttributes' => ['name', 'value']
		],

		'source' => [
			'unpaired',
			'qkAttributes' => ['src', 'type']
		],

		// Scripting
		'canvas' => [
			'qkAttributes' => ['width', 'height']
		],

		'script' => [
			'qkAttributes' => ['src', 'type']
		],

		// Table content
		'col' => [
			'unpaired',
			'qkAttributes' => ['span']
		],

		'td' => [
			'qkAttributes' => ['rowspan', 'colspan']
		],

		// Forms
		'button' => [
			'qkAttributes' => ['type', 'value']
		],

		'form' => [
			'qkAttributes' => ['method']
		],

		'input' => [
			'unpaired',
			'qkAttributes' => ['type', 'value', 'placeholder']
		],

		'textarea' => [
			'qkAttributes' => ['placeholder']
		],

		'label' => [
			'qkAttributes' => ['for']
		],

		'progress' => [
			'qkAttributes' => ['value', 'max']
		],

		'optgroup' => [
			'qkAttributes' => ['label']
		],

		'option' => [
			'qkAttributes' => ['value']
		],

		// Interactive elements
		'menu' => [
			'qkAttributes' => ['type', 'label']
		],

		'menuitem' => [
			'qkAttributes' => ['type']
		]
	];
}
