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


/**
 * @testCase
 */
final class FormattingTestCase extends AbstractTestCase
{

	public function testIndentedBlockMode()
	{
		$this->macdom->addElement('skipthisarea', [$this->macdom::CONTENT_SKIPPED]);
		$this->assertMatchFile('indentedBlockMode');
	}


	public function testInlineSkipAreas()
	{
		$this->macdom->addElement('skipthisarea', [$this->macdom::CONTENT_SKIPPED]);
		$this->assertMatchFile('inlineMode');
	}


	public function testPhtmlSkipAreas()
	{
		$this->assertMatchFile('phtml');
	}


	public function testTaggedBlockMode()
	{
		$this->macdom->addElement('skipthisarea', [$this->macdom::CONTENT_SKIPPED]);
		$this->assertMatchFile('taggedBlockMode');
	}


    public function testSpacesIndendation()
    {
        $this->macdom->setSpacesIndentation(8);
        $this->assertMatchFile('spacesIndentation');
    }

}

(new FormattingTestCase())->run();
