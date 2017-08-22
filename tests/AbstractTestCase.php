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

use Macdom;
use Macdom\Engine;
use Tester\Assert;
use Tester\TestCase;


abstract class AbstractTestCase extends TestCase
{

	/**
	 * @var Engine
	 */
	public $macdom;

	/**
	 * @var string
	 */
	private $actualTestsDirectoryNamePrefix;


	public function setUp()
	{
		parent::setUp();
		$this->setActualTestsDirectoryNamePrefix();
		$this->macdom = new Engine;
	}


	protected function assertMatchFile(string $fileName, bool $rewriteExpectedFile = NULL)
	{
		if ($rewriteExpectedFile) {
			$this->rewriteExpectedFile($fileName);
		}

		Assert::matchFile(
			$this->getExpectedFilePath($fileName),
			$this->macdom->compile($this->getCompiledFileContent($this->getActualFilePath($fileName))));
	}


	protected function assertSame(string $expected, string $actual)
	{
		Assert::same($expected, $this->macdom->disableOutputFormatter()->compile($actual));
	}


	protected function getActualFilePath(string $fileName): string
	{
		return $this->getActualDir() . '/' . $fileName . '.html';
	}


	protected function rewriteExpectedFile(string $testName)
	{
		file_put_contents(
			$this->getExpectedFilePath($testName),
			$this->macdom->compile($this->getCompiledFileContent($this->getActualFilePath($testName)))
		);
	}


	protected function setActualTestsDirectoryNamePrefix()
	{
		if ($this->actualTestsDirectoryNamePrefix) {
			return;
		}

		$childClassNamespace = explode('\\', get_class($this));
		$childClassName = str_replace('TestCase', '', end($childClassNamespace));
		$this->actualTestsDirectoryNamePrefix = $childClassName;
	}


	private function getActualDir(): string
	{
		return __DIR__ . '/' . $this->actualTestsDirectoryNamePrefix . 'Tests/Actual';
	}


	private function getCompiledFileContent(string $filePath): string
	{
		return file_get_contents($filePath);
	}


	private function getExpectedDir(): string
	{
		return __DIR__ . '/' . $this->actualTestsDirectoryNamePrefix . 'Tests/Expected';
	}


	private function getExpectedFilePath(string $fileName): string
	{
		return $this->getExpectedDir() . '/' . $fileName . '.html';
	}

}
