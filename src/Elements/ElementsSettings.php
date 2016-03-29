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

namespace Machy8\Macdom\Elements;

class ElementsSettings extends ElementsList {

	/** @var array */
	protected $elementsSettings = [
		'a' => [
			'qkAttributes' => ['href', 'target', 'role']
		],
		'abbr' => [
			'qkAttributes' => ['title']
		],
		'area' => [
			'unpaired',
		],
		'audio' => [
			'qkAttributes' => ['src']
		],
		'base' => [
			'unpaired'
		],
		'bdo' => [
			'qkAttributes' => ['dir']
		],
		'br' => [
			'unpaired'
		],
		'button' => [
			'qkAttributes' => ['type', 'value']
		],
		'canvas' => [
			'qkAttributes' => ['width', 'height']
		],
		'col' => [
			'unpaired',
			'qkAttributes' => ['span']
		],
		'data' => [
			'qkAttributes' => ['value']
		],
		'embed' => [
			'unpaired',
			'qkAttributes' => ['src', 'type', 'width', 'height']
		],
		'form' => [
			'qkAttributes' => ['method']
		],
		'hr' => [
			'unpaired'
		],
		'html' => [
			'qkAttributes' => ['lang']
		],
		'iframe' => [
			'qkAttributes' => ['src', 'frameborder', 'width', 'height']
		],
		'img' => [
			'unpaired',
			'qkAttributes' => ['src', 'alt']
		],
		'input' => [
			'unpaired',
			'qkAttributes' => ['type', 'value', 'placeholder']
		],
		'label' => [
			'qkAttributes' => ['for']
		],
		'link' => [
			'unpaired',
			'qkAttributes' => ['href', 'rel', 'title']
		],
		'meta' => [
			'unpaired',
			'qkAttributes' => ['name', 'content']
		],
		'object' => [
			'qkAttributes' => ['data', 'type']
		],
		'ol' => [
			'qkAttributes' => ['type', 'start', 'reversed']
		],
		'optgroup' => [
			'qkAttributes' => ['label']
		],
		'option' => [
			'qkAttributes' => ['value']
		],
		'param' => [
			'unpaired',
			'qkAttributes' => ['name', 'value']
		],
		'progress' => [
			'qkAttributes' => ['value', 'max']
		],
		'q' => [
			'qkAttributes' => ['cite']
		],
		'script' => [
			'qkAttributes' => ['src', 'type']
		],
		'source' => [
			'unpaired',
			'qkAttributes' => ['src', 'type']
		],
		'style' => [
			'qkAttributes' => ['type']
		],
		'td' => [
			'qkAttributes' => ['rowspan', 'colspan']
		],
		'textarea' => [
			'qkAttributes' => ['placeholder']
		],
		'track' => [
			'unpaired',
			'qkAttributes' => ['src', 'srclang', 'kind']
		],
		'wbr' => [
			'unpaired'
		],
		//Experimental and not standardized API elements
		'menu' => [
			'qkAttributes' => ['type', 'label']
		],
		'menuitem' => [
			'qkAttributes' => ['type']
		],
	];
}
