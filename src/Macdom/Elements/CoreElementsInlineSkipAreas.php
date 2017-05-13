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

use Macdom\Compiler;


class CoreElementsInlineSkipAreas extends AbstractElementsManager
{

	public static function install(Compiler $compiler): void
	{
		$elementsManager = new static($compiler);

		$elementsManager
			->addInlineSkipArea('\<\?php .*?\?\>')
			->addInlineSkipArea('<(?:\w+)(?:>|.*?[^?])?>.*<\/(?:[^\>]+)>|<\w+[^>]+\/>');
	}

}
