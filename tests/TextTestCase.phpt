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


final class TextTestCase extends AbstractTestCase
{

	public function testConnectedParagraphs(): void
	{
		$this->assertMatchFile('connectedParagraph');
	}


	public function testParagraph(): void
	{
		$this->assertMatchFile('paragraph');
	}


	public function testParagraphWithConnector(): void
	{
		$this->assertMatchFile('paragraphWithConnector');
	}


	public function testParagraphWithElements(): void
	{
		$this->assertMatchFile('paragraphWithElements');
	}


	public function testSavedTrailingSpace(): void
	{
		$expected = "<div>Text </div>";
		$actual = "div Text |";
		$this->assertSame($expected, $actual);
	}


	public function testTrailingSpaceTrimming(): void
	{
		$expected = "<div>Text</div>";
		$actual = "div Text ";
		$this->assertSame($expected, $actual);
	}

}

run(new TextTestCase());
