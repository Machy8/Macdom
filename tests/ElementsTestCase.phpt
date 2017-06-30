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


final class ElementsTestCase extends AbstractTestCase
{

	public function testBooleanAttributeDeletion()
	{
		$this->macdom->removeBooleanAttribute('disabled readonly');
		$this->assertSame('<input type="text">', 'input $text disabled readonly');
	}


	public function testBooleanAttributes()
	{
		$this->assertMatchFile('booleanAttributes');
	}


	public function testBooleanAttributesAddition()
	{
		$this->macdom->addBooleanAttribute('whisky beer');
		$this->assertSame('<input type="text" whisky beer>', 'input $text whisky beer');
	}


	public function testBooleanAttributesXmlSyntax()
	{
		$this->macdom->setContentType($this->macdom::CONTENT_XHTML);
		$this->assertMatchFile('booleanAttributesXmlSyntax');
	}


	public function testClassSelectors()
	{
		$this->assertMatchFile('classSelectors');
	}


	public function testDataAttributes()
	{
		$this->assertMatchFile('dataAttributes');
	}


	public function testElementsAddition()
	{
		$this->macdom
			->addElement('beer', ['quickAttributes' => ['brand']])
			->addElement('pong', [$this->macdom::UNPAIRED_ELEMENT, 'quickAttributes' => ['score', 'winner']]);
		$this->assertMatchFile('elementsAddition');
	}


	public function testElementsContentType()
	{
		$this->macdom->setContentType($this->macdom::CONTENT_XML);
		$this->assertMatchFile('elementsContentType');
	}


	public function testElementsDeletion()
	{
		$this->macdom->removeElement('div a');
		$this->assertMatchFile('elementsDeletion');
	}


	public function testHtmlAttributes()
	{
		$this->assertMatchFile('htmlAttributes');
	}


	public function testIdSelectors()
	{
		$this->assertMatchFile('idSelectors');
	}


	public function testQuickAttributes()
	{
		$this->assertMatchFile('quickAttributes');
	}


	/**
	 * @throws \Macdom\CompileException The tabs indentation method is used but spaces in indentation were found on line 1 near "  div"
	 */
	public function testWrongIndentation()
	{
		$this->macdom->compile('  div');
	}


	/**
	 * @throws \Macdom\CompileException Element "div" has no quick attributes on line 1 near "div $muhehe"
	 */
	public function testWrongQuickAttribute()
	{
		$this->macdom->compile('div $muhehe');
	}

}

run(new ElementsTestCase());
