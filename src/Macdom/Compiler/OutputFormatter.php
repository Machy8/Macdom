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


final class OutputFormatter
{

	/**
	 * @var string
	 */
	private $output;

	/**
	 * @var bool
	 */
	private $outputFormatterDisabled = FALSE;

	/**
	 * @var string
	 */
	private $outputIndentation;

	/**
	 * @var string
	 */
	private $outputIndentationMethod = Engine::TABS_INDENTATION;

	/**
	 * @var int
	 */
	private $outputIndentationSize = Engine::DEFAULT_INDENTATION_SIZE;

	/**
	 * @var string
	 */
	private $outputIndentationUnit;

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var string
	 */
	private $previousTokenType = NULL;


	public function __construct(Parser $parser)
	{
		$this->parser = $parser;
	}


	public function disableOutputFormatter(): self
	{
		$this->outputFormatterDisabled = TRUE;

		return $this;
	}


	public function format(array $tokens): string
	{
		$this->output = '';
		$this->outputIndentation = '';
		$this->previousTokenType = NULL;
		$this->outputIndentationUnit = $this->outputIndentationMethod === Engine::TABS_INDENTATION
			? "\t"
			: str_repeat(' ', $this->outputIndentationSize);

		foreach ($tokens as $token) {
			$tokenType = $token['type'];
			$tokenCode = $token['code'];

			if ($this->outputFormatterDisabled) {
				$this->output .= $tokenCode;
				continue;
			}

			if ($tokenType !== Token::UNFORMABLE_TEXT) {
				$subtokens = preg_split('/(<[^>]+>)/', $tokenCode, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

				foreach ($subtokens as $subtoken) {
					$subtokenCode = $subtoken;
					$subtoken = $this->parser->analyzeString($subtoken);

					if ($this->parser->isElementUnpaired($subtoken['details'])) {
						$subtoken['type'] = Token::UNPAIRED_ELEMENT;
					}

					$this->formatToken($subtokenCode, $subtoken['type']);
				}
				continue;
			}
			$this->formatToken($tokenCode, $tokenType);
		}

		if ( ! preg_match("/\n$/", $this->output)) {
			$this->output .= "\n";
		}

		return $this->output;
	}


	public function setOutputIndentationSize(int $size): self
	{
		$this->outputIndentationSize = $size;

		return $this;
	}


	public function setSpacesIndentationMethod(int $indentationSize): self
	{
		$this->outputIndentationMethod = Engine::SPACES_INDENTATION;
		$this->outputIndentationSize = $indentationSize;

		return $this;
	}


	private function decreaseIndentation(string $indentation): string
	{
		$indentation = preg_replace("/" . $this->outputIndentationUnit . "/", '', $indentation, 1);

		return $indentation ?? '';
	}


	private function formatToken(string $token, string $tokenType): void
	{
		$previousTokenIsOpenTag = $this->previousTokenType === Token::ELEMENT_OPEN_TAG;
		$previousTokenIsText = $this->previousTokenType === Token::TEXT;
		$previousTokenIsUnformableText = $this->previousTokenType === Token::UNFORMABLE_TEXT;
		$tokenIsOpenTag = $tokenType === Token::ELEMENT_OPEN_TAG;
		$tokenIsUnpairedTag = $tokenType === Token::UNPAIRED_ELEMENT;
		$tokenIsCloseTag = $tokenType === Token::ELEMENT_CLOSE_TAG;
		$tokenIsText = $tokenType === Token::TEXT;
		$connectedText = $tokenIsText && $previousTokenIsText;
		$emptyElement = $tokenIsCloseTag && $previousTokenIsOpenTag;

		if ($previousTokenIsOpenTag && ($tokenIsOpenTag || $tokenIsUnpairedTag || $tokenIsText)) {
			$this->outputIndentation = $this->increaseIndentation($this->outputIndentation);

		} elseif ($tokenIsCloseTag && ! ($previousTokenIsOpenTag || $previousTokenIsUnformableText)) {
			$this->outputIndentation = $this->decreaseIndentation($this->outputIndentation);
		}

		if ( ! ( ! $this->output && ! $this->previousTokenType || $connectedText || $emptyElement)) {
			$this->output .= "\n";
		}

		if ( ! ( ! $token || $emptyElement || $connectedText)) {
			$this->output .= $this->outputIndentation;
		}

		$this->previousTokenType = $tokenType;
		$this->output .= $token;
	}


	private function increaseIndentation(string $indentation): string
	{
		return $indentation . $this->outputIndentationUnit;
	}

}
