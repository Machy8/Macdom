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

namespace Macdom\Tests;

require_once 'bootstrap.php';


final class SkipAreasTestCase extends AbstractTestCase
{

	public function testIndentedBlockMode(): void
	{
		$this->assertMatchFile('indentedBlockMode');
	}


	public function testInlineSkipAreas(): void
	{
		$this->assertMatchFile('inlineMode');
	}


	public function testTaggedBlockMode(): void
	{
		$this->macdom->addElement('skipthisarea', [$this->macdom::CONTENT_SKIPPED]);
		$this->assertMatchFile('taggedBlockMode');
	}

}

run(new SkipAreasTestCase());