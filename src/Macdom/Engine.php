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

namespace Macdom;

use XhtmlFormatter\Formatter;


final class Engine
{

	const
		VERSION = '3.0.0',

		DEFAULT_CONTENT_TYPE = self::CONTENT_HTML,
		CONTENT_HTML = 'contentHtml',
		CONTENT_XHTML = 'contentXhtml',
		CONTENT_XML = 'contentXml',

		DEFAULT_INDENTATION_SIZE = 4,
		SPACES_INDENTATION = 'spacesIndentation',
		TABS_INDENTATION = 'tabsIndentation',

		CONTENT_SKIPPED = Token::CONTENT_SKIPPED,
		UNPAIRED_ELEMENT = Token::UNPAIRED_ELEMENT,
		REGULAR_EXPRESSION_MACRO = Token::REGULAR_EXPRESSION_MACRO;

	/**
	 * @var Compiler
	 */
	private $compiler;

	/**
	 * @var string
	 */
	private $contentType = self::DEFAULT_CONTENT_TYPE;

	/**
	 * @var Formatter
	 */
	private $outputFormatter;

	/**
	 * @var bool
	 */
	private $outputFormatterEnabled = TRUE;

	/**
	 * @var Parser
	 */
	private $parser;


	/**
	 * @param string $attribute
	 * @param string|array|NULL $contentType
	 * @return Engine
	 */
	public function addBooleanAttribute(string $attribute, $contentType = NULL): self
	{
		$this->getCompiler()->addBooleanAttribute($attribute, $contentType);

		return $this;
	}


	public function addElement(string $element, array $settings = NULL): self
	{
		$this->getCompiler()->addElement($element, $settings);

		return $this;
	}


	public function addMacro(string $keyword, Callable $macro, array $flags = NULL): self
	{
		$this->getCompiler()->addMacro($keyword, $macro, $flags);

		return $this;
	}


	public function changeElementQuickAttributes(string $element, array $quickAttributes): self
	{
		$this->getCompiler()->changeElementQuickAttributes($element, $quickAttributes);

		return $this;
	}


	public function compile(string $content): string
	{
		try {
			$compiler = $this->getCompiler()->setContentType($this->contentType);
			$tokens = $this->getParser()->parse($content);
			$code = $compiler->compile($tokens);

			if ($this->outputFormatterEnabled) {
				$code = $this->getOutputFormatter()->format($code);
			}

		} catch (\Exception $exception) {
			throw $exception;
		}

		return $code;
	}


	public function disableOutputFormatter(): self
	{
		$this->outputFormatterEnabled = FALSE;

		return $this;
	}


	public function getContentType(): string
	{
		return $this->contentType;
	}


	public function getElements(): array
	{
		return $this->getCompiler()->getElements();
	}


	public function getElementsBooleanAttributes(): array
	{
		return $this->getCompiler()->getElementsBooleanAttributes();
	}


	public function getMacros(): array
	{
		return $this->getCompiler()->getMacros();
	}


	public function removeBooleanAttribute(string $attribute): self
	{
		$this->getCompiler()->removeBooleanAttribute($attribute);

		return $this;
	}


	public function removeElement(string $element): self
	{
		$this->getCompiler()->removeElement($element);

		return $this;
	}


	public function removeMacro(string $macro): self
	{
		$this->getCompiler()->removeMacro($macro);

		return $this;
	}


	public function setContentType(string $type): self
	{
		$this->contentType = $type;

		return $this;
	}


	public function setSpacesIndentationMethod(int $indentationSize = Engine::DEFAULT_INDENTATION_SIZE): self
	{
		$this->getParser()->setSpacesIndentationMethod($indentationSize);
		$this->getOutputFormatter()->setSpacesIndentationMethod($indentationSize);

		return $this;
	}


	private function getCompiler(): Compiler
	{
		if ( ! $this->compiler) {
			$this->compiler = new Compiler;
			Elements\CoreElements::install($this->compiler);
			Elements\CoreBooleanAttributes::install($this->compiler);
			Macros\CoreMacros::install($this->compiler);
		}

		return $this->compiler;
	}


	private function getOutputFormatter(): Formatter
	{
		if ( ! $this->outputFormatter) {
			$this->outputFormatter = new Formatter;
			$unpairedElements = $this->getCompiler()->getUnpairedElements();

			$this->outputFormatter
				->addUnpairedElements($unpairedElements)
				->setContentType($this->contentType);
		}

		return $this->outputFormatter;
	}


	private function getParser(): Parser
	{
		if ( ! $this->parser) {
			$this->parser = new Parser($this->getCompiler());
		}

		return $this->parser;
	}

}
