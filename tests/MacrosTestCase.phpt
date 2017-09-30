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
final class MacrosTestCase extends AbstractTestCase
{

	public function testMacrosAddition()
	{
		$quickList = function ($content) {
			$listItems = explode(' ', $content);
			$list = '<ul>';

			foreach ($listItems as $listItem) {
				if ( ! $listItem) {
					continue;
				}

				$list .= '<li>' . $listItem . '</li>';
			}

			$list .= '</ul>';

			return $list;
		};

		$this->macdom
			->addMacro('sectionTitle', function ($content) {
				return '<h1 class="sectionTitle">' . $content . '</h1>';
			})
			->addMacro('quickList', $quickList);

		$this->assertMatchFile('macrosAddition');
	}


	public function testMacrosContentType()
	{
		$this->macdom->setContentType($this->macdom::CONTENT_XML);
		$this->assertMatchFile('macrosContentType');
	}


	public function testMacrosDeletion()
	{
		$this->macdom->removeMacro('utf-8 charset');
		$this->assertMatchFile('macrosDeletion');
	}


	public function testMacrosOverwriting()
	{
		$this->macdom
			->addMacro('charset', function ($charset) {
				return 'Selected charset is ' . $charset . '.';
			})
			->addMacro('script', function ($content) {
				return '<script type="text/javascript">' . $content . '</script>';
			});

		$this->assertMatchFile('macrosOverwriting');
	}


	public function testMacrosWithContent()
	{
		$this->assertMatchFile('macrosWithContent');
	}


	public function testMacrosWithoutContent()
	{
		$this->assertMatchFile('macrosWithoutContent');
	}


	public function testRegularExpressionMacros()
	{
		$this->assertMatchFile('regularExpressionMacros');
	}

}

(new MacrosTestCase())->run();
