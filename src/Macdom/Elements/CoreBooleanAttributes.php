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

namespace Macdom\Elements;

use Macdom\Register;


final class CoreBooleanAttributes extends AbstractElementsManager
{

	public static function install(Register $register)
	{
		$elementsManager = new static($register);

		$elementsManager
			->addBooleanAttribute('allowfullscreen')
			->addBooleanAttribute('autofocus')
			->addBooleanAttribute('autoplay')
			->addBooleanAttribute('async')
			->addBooleanAttribute('contenteditable')
			->addBooleanAttribute('controls')
			->addBooleanAttribute('default')
			->addBooleanAttribute('defer')
			->addBooleanAttribute('disabled')
			->addBooleanAttribute('draggable')
			->addBooleanAttribute('formnovalidate')
			->addBooleanAttribute('hidden')
			->addBooleanAttribute('checked')
			->addBooleanAttribute('ismap')
			->addBooleanAttribute('itemscope')
			->addBooleanAttribute('loop')
			->addBooleanAttribute('multiple')
			->addBooleanAttribute('muted')
			->addBooleanAttribute('open')
			->addBooleanAttribute('readonly')
			->addBooleanAttribute('required')
			->addBooleanAttribute('selected')
			->addBooleanAttribute('spellcheck');
	}

}
