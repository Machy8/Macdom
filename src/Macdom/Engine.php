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
	 * @var Register
	 */
	private $register;

	/**
	 * @var INT|NULL
	 */
	private $spacesIndentationSize = NULL;


	/**
	 * @param string|array $booleanAttribute
	 */
	public function addBooleanAttribute($booleanAttribute, string $contentType = NULL): self
	{
		$this->getRegister()->addBooleanAttribute($booleanAttribute, $contentType);

		return $this;
	}


	public function addElement(string $element, array $settings = NULL): self
	{
		$this->getRegister()->addElement($element, $settings);

		return $this;
	}


	public function addMacro(string $keyword, Callable $macro, array $flags = NULL): self
	{
		$this->getRegister()->addMacro($keyword, $macro, $flags);

		return $this;
	}


	/**
	 * @param string|array $quickAttributes
	 */
	public function changeElementQuickAttributes(string $element, $quickAttributes, string $contentType = NULL): self
	{
		$this->getRegister()->changeElementQuickAttributes($element, $quickAttributes, $contentType);

		return $this;
	}


	public function compile(string $content = NULL): string
	{

		if ( ! $content) {
			return '';
		}

		try {
			$register = $this->getRegister()->setContentType($this->contentType);
			$compiler = $this->getCompiler()
				->setRegister($register)
				->setXmlSyntax($this->contentType);

			$tokens = $this->getParser()
				->setRegister($register)
				->parse($content);

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


	/**
	 * @internal
	 */
	public function getContentType(): string
	{
		return $this->contentType;
	}


	/**
	 * @internal
	 */
	public function getElements(): array
	{
		return $this->getRegister()->getElements();
	}


	/**
	 * @internal
	 */
	public function getElementsBooleanAttributes(): array
	{
		return $this->getRegister()->getElementsBooleanAttributes();
	}


	/**
	 * @internal
	 */
	public function getMacros(): array
	{
		return $this->getRegister()->getMacros();
	}


	/**
	 * @param array|string $booleanAttribute
	 */
	public function removeBooleanAttribute(string $booleanAttribute): self
	{
		$this->getRegister()->removeBooleanAttribute($booleanAttribute);

		return $this;
	}


	/**
	 * @param array|string $element
	 */
	public function removeElement($element, string $contentType = NULL): self
	{
		$this->getRegister()->removeElement($element, $contentType);

		return $this;
	}


	/**
	 * @param array|string $macro
	 */
	public function removeMacro(string $macro, string $contentType = NULL): self
	{
		$this->getRegister()->removeMacro($macro, $contentType);

		return $this;
	}


	public function setContentType(string $type): self
	{
		$this->contentType = $type;

		return $this;
	}


	public function setSpacesIndentation(int $indentationSize = NULL): self
	{
		$this->spacesIndentationSize = $indentationSize ?? Engine::DEFAULT_INDENTATION_SIZE;
		$this->getParser()->setSpacesIndentationMethod($this->spacesIndentationSize);
		$this->getOutputFormatter()->setSpacesIndentationMethod($this->spacesIndentationSize);

		return $this;
	}


	private function getCompiler(): Compiler
	{
		if ( ! $this->compiler) {
			$this->compiler = new Compiler;
		}

		return $this->compiler;
	}


	private function getOutputFormatter(): Formatter
	{
		if ( ! $this->outputFormatter) {
			$this->outputFormatter = new Formatter;

			$this->outputFormatter
				->addSkippedElement($this->getRegister()->getSkippedElements())
				->addUnpairedElements($this->getRegister()->getUnpairedElements())
				->setContentType($this->contentType);

			if ($this->spacesIndentationSize) {
				$this->outputFormatter->setSpacesIndentationMethod($this->spacesIndentationSize);
			}
		}

		return $this->outputFormatter;
	}


	private function getParser(): Parser
	{
		if ( ! $this->parser) {
			$this->parser = new Parser;
		}

		return $this->parser;
	}


	private function getRegister(): Register
	{
		if ( ! $this->register) {
			$this->register = new Register;
			Elements\CoreElements::install($this->register);
			Elements\CoreBooleanAttributes::install($this->register);
			Macros\CoreMacros::install($this->register);
		}

		return $this->register;
	}

}
