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


final class Parser
{

	const
		TOKEN_TYPES_MATCH_ORDER = [
			Token::UNFORMABLE_TEXT,
			Token::ELEMENT,
			Token::ELEMENT_OPEN_TAG,
			Token::ELEMENT_CLOSE_TAG,
			Token::MACRO,
			Token::REPLICATOR_OPEN_TAG,
			Token::REPLICATOR_CLOSE_TAG,
			Token::REPLICATOR_REPLICANT,
			Token::TEXT
		],

		REPLICATOR_REGISTER_PREFIX = '#',

		SKIPPED_AREA_INDENTED_BLOCK_MODE = 'skippedAreaIndentedBlock',
		SKIPPED_AREA_TAGGED_BLOCK_MODE = 'skippedAreaTaggedBlock',
		SKIP_TAG = 'SKIP',

		REPLICATOR_PREFIX = '@',
		REPLICATOR_OPEN_TAG_RE = '/^' . self::REPLICATOR_PREFIX . '(?<keyword>[\S]*)/',
		REPLICATOR_CLOSE_TAG_RE = '/^\/' . self::REPLICATOR_PREFIX . '(?<keyword>[\S]*)/';

	/**
	 * @var Compiler
	 */
	private $compiler;

	/**
	 * @var string
	 */
	private $indentationMethod = Engine::TABS_INDENTATION;

	/**
	 * @var int
	 */
	private $indentationSize = Engine::DEFAULT_INDENTATION_SIZE;

	/**
	 * @var Token[]
	 */
	private $output;

	/**
	 * @var array
	 */
	private $processedLine;

	/**
	 * @var array
	 */
	private $replicatorRegister = [];

	/**
	 * @var array
	 */
	private $skippedArea;


	public function __construct(Compiler $compiler)
	{
		$this->compiler = $compiler;
	}


	public function analyzeString(string $string): array
	{
		$type = Token::TEXT;
		$keyword = Helpers::getFirstWord($string);
		$settings = NULL;

		if ($this->compiler->findElement($keyword)) {
			$type = Token::ELEMENT;

		} elseif ($this->compiler->findMacro($keyword)) {
			$type = Token::MACRO;

		} elseif (Helpers::matchElementOpenTag($string, $matches, $this->compiler->getElementsCustomOpenTags())
			&& $this->compiler->findElement($matches['element'])
		) {
			$keyword = $matches['element'];
			$type = Token::ELEMENT_OPEN_TAG;

		} elseif (Helpers::matchElementCloseTag($string, $matches, $this->compiler->getElementsCustomCloseTags())) {
			$type = Token::ELEMENT_CLOSE_TAG;

		} elseif (preg_match(self::REPLICATOR_OPEN_TAG_RE, $keyword, $matches)) {
			$keyword = $matches['keyword'];
			$type = Token::REPLICATOR_OPEN_TAG;

		} elseif (preg_match(self::REPLICATOR_CLOSE_TAG_RE, $keyword, $matches)) {
			$keyword = $matches['keyword'];
			$type = Token::REPLICATOR_CLOSE_TAG;
		}

		$typeIsElement = in_array($type, [Token::ELEMENT, Token::ELEMENT_OPEN_TAG], TRUE);

		if ($typeIsElement) {
			$elementSettings = $this->compiler->getElement($keyword);

			$details = [
				'skipElement' => in_array(Engine::SKIPPED_ELEMENT, $elementSettings),
				'skipContent' => in_array(Engine::CONTENT_SKIPPED, $elementSettings)
			];

			if ($this->isElementUnpaired($elementSettings)) {
				$details[] = Engine::UNPAIRED_ELEMENT;
			}
		}

		return [
			'keyword' => $keyword,
			'type' => $type,
			'details' => $details ?? []
		];
	}


	public function isElementUnpaired(array $elementSettings): bool
	{
		return in_array(Engine::UNPAIRED_ELEMENT, $elementSettings, TRUE);
	}


	public function parse(string $input): array
	{
		$input = str_replace("\r\n", "\n", $input);
		$this->output = [];
		$this->skippedArea = [
			'mode' => [],
			'indentationLevel' => [],
			'closeTags' => []
		];

		foreach (preg_split("/\\r|\\n/", $input, -1, PREG_SPLIT_DELIM_CAPTURE) as $lineNumber => $line) {
			$this->setProcessedLine($line, $lineNumber);

			foreach (self::TOKEN_TYPES_MATCH_ORDER as $methodName) {
				if ($this->{'match' . ucfirst($methodName)}()) {
					break;
				}
			}
		}

		return $this->output;
	}


	public function setSpacesIndentationMethod(int $indentationSize = NULL): self
	{
		$this->indentationMethod = Engine::SPACES_INDENTATION;
		$this->indentationSize = $indentationSize;

		return $this;
	}


	private function addToken(string $type, array $parameters = NULL): void
	{
		$this->output[] = $token = new Token;
		$token->text = $parameters['text'] ?? $this->processedLine['text'];
		$token->indentationLevel = $this->processedLine['indentationLevel'];
		$token->keyword = $parameters['keyword'] ?? NULL;
		$token->line = $this->processedLine['line'];
		$token->type = $type;
	}


	private function getIndentationLevel(string $token): int
	{
		Helpers::getIndentation($token, $whitespaceCharacters);
		$tabsIndentation = preg_match_all('/\t/', $whitespaceCharacters);

		if ($this->indentationMethod === Engine::TABS_INDENTATION) {
			$indentationLevel = $tabsIndentation;

			if (preg_match('/ /', $whitespaceCharacters)) {
				throw new CompileException(
					'The tabs indentation method is used but spaces in indentation were found on line '
					. ($this->processedLine['line']) . ' near "' . $this->processedLine['text'] . '"');
			}

		} elseif ($this->indentationMethod === Engine::SPACES_INDENTATION) {
			$indentationLevel = preg_match_all('/ {' . $this->indentationSize . '}/', $whitespaceCharacters);

			if ($tabsIndentation) {
				throw new CompileException(
					'The spaces indentation method is used but tabs in indentation were found on line '
					. ($this->processedLine['line']) . ' near "' . $this->processedLine['text'] . '"');
			}

		} else {
			throw new SetupException('Unknown indentation method ' . $this->indentationMethod);
		}

		return $indentationLevel;
	}


	private function matchElement(): bool
	{
		if ($this->processedLine['type'] !== Token::ELEMENT) {
			return FALSE;
		}

		$this->addToken(Token::ELEMENT, [
			'keyword' => $this->processedLine['keyword']
		]);

		return TRUE;
	}


	private function matchElementCloseTag(): bool
	{
		if ($this->processedLine['type'] !== Token::ELEMENT_CLOSE_TAG) {
			return FALSE;
		}

		$this->addToken(Token::ELEMENT_CLOSE_TAG);

		return TRUE;
	}


	private function matchElementOpenTag(): bool
	{
		if ($this->processedLine['type'] !== Token::ELEMENT_OPEN_TAG) {
			return FALSE;
		}

		$this->addToken(Token::ELEMENT_OPEN_TAG);

		return TRUE;
	}


	private function matchMacro(): bool
	{
		if ($this->processedLine['type'] !== Token::MACRO) {
			return FALSE;
		}

		$this->addToken(Token::MACRO, [
			'keyword' => $this->processedLine['keyword']
		]);

		return TRUE;
	}


	private function matchReplicatorCloseTag(): bool
	{
		if ($this->processedLine['type'] !== Token::REPLICATOR_CLOSE_TAG) {
			return FALSE;
		}

		$processedLineLevel = $this->processedLine['indentationLevel'];
		$replicatedLineKey = ! empty($this->processedLine['keyword'])
			? $this->processedLine['keyword']
			: '#' . $processedLineLevel;

		if (array_key_exists($replicatedLineKey, $this->replicatorRegister)) {
			unset($this->replicatorRegister[$replicatedLineKey]);

		} else {
			throw new CompileException('No replicated line can be deregistered on line ' . $this->processedLine['line']
				. ' near "' . $this->processedLine['text'] . '"');
		}

		return TRUE;
	}


	private function matchReplicatorOpenTag(): bool
	{
		$processedLineKeyword = $this->processedLine['keyword'];

		if ($this->processedLine['type'] !== Token::REPLICATOR_OPEN_TAG) {
			return FALSE;
		}

		if ((bool) $processedLineKeyword) {
			if (substr($processedLineKeyword, 0, 1) === self::REPLICATOR_REGISTER_PREFIX) {
				throw new CompileException(
					'Replicated line keyword can\'t start with "' . self::REPLICATOR_REGISTER_PREFIX
					. '" because it is default internal selector on line "' . $this->processedLine['line']
					. '" near "' . $this->processedLine['keyword'] . '"');
			}

			$this->replicatorRegister[$processedLineKeyword] = [
				'indentationLevel' => $this->processedLine['indentationLevel'],
				'text' => $this->processedLine['text']
			];

		} else {
			$this->replicatorRegister[
				self::REPLICATOR_REGISTER_PREFIX . $this->processedLine['indentationLevel']
			] = $this->processedLine['text'];
		}

		return TRUE;
	}


	private function matchReplicatorReplicant(): bool
	{
		if ( ! $this->processedLine['text']) {
			return TRUE;
		}

		$processedLineKeyword = $this->processedLine['keyword'];
		$processedLineLevel = $this->processedLine['indentationLevel'];

		if (isset($this->replicatorRegister[$processedLineKeyword])
			&& $processedLineLevel >= $this->replicatorRegister[$processedLineKeyword]['indentationLevel']
		) {
			$this->addToken(Token::REPLICATOR_REPLICANT, [
				'text' => [
					'replicated' => $this->replicatorRegister[$processedLineKeyword]['text'],
					'synchronized' => Helpers::removeFirstWord($this->processedLine['text'])
				]
			]);

			return TRUE;

		} elseif (isset($this->replicatorRegister[self::REPLICATOR_REGISTER_PREFIX . $processedLineLevel])) {
			$this->addToken(Token::REPLICATOR_REPLICANT, [
				'text' => [
					'replicated' => $this->replicatorRegister[self::REPLICATOR_REGISTER_PREFIX . $processedLineLevel],
					'synchronized' => $this->processedLine['text']
				]
			]);

			return TRUE;
		}

		return FALSE;
	}


	private function matchText(): bool
	{
		$this->addToken(Token::TEXT);

		return TRUE;
	}


	private function matchUnformableText(): bool
	{
		$processedLineKeyword = $this->processedLine['keyword'];
		$processedLineType = $this->processedLine['type'];
		$processedLineDetails = $this->processedLine['details'];

		if ( ! $this->skippedArea['mode']) {
			if ($processedLineKeyword === self::SKIP_TAG) {
				$this->setSkippedArea(self::SKIPPED_AREA_INDENTED_BLOCK_MODE);

				return TRUE;

			} elseif ($processedLineType === Token::ELEMENT_OPEN_TAG) {
				$elementSkipped = $processedLineDetails['skipElement'];

				if ($elementSkipped && isset($processedLineDetails['closeTags'])) {
					$this->setSkippedArea(self::SKIPPED_AREA_TAGGED_BLOCK_MODE, $processedLineDetails['closeTags']);

				} elseif ($elementSkipped || $processedLineDetails['skipContent']) {
					$this->setSkippedArea(self::SKIPPED_AREA_TAGGED_BLOCK_MODE, '/\<\/{1}[^\/ ?]+\>|[^?< ]*\?\>/');
				}

			} elseif ($processedLineType === Token::ELEMENT && $processedLineDetails['skipContent']) {
				$this->setSkippedArea(self::SKIPPED_AREA_INDENTED_BLOCK_MODE);
			}

		} elseif ($this->skippedArea['mode']) {
			$closeTags = $this->skippedArea['closeTags'];

			if ($this->skippedArea['mode'] === self::SKIPPED_AREA_TAGGED_BLOCK_MODE && $closeTags
				&& (is_array($closeTags) && in_array($processedLineKeyword, $closeTags)
				|| is_string($closeTags) && preg_match($closeTags, $processedLineKeyword))
				|| $this->skippedArea['mode'] === self::SKIPPED_AREA_INDENTED_BLOCK_MODE
				&& $this->processedLine['indentationLevel'] < $this->skippedArea['indentationLevel']
			) {
				$this->setSkippedArea(NULL);

				return FALSE;
			}

			$this->addToken(Token::UNFORMABLE_TEXT);

			return TRUE;
		}

		return FALSE;
	}


	private function setProcessedLine(string $line, int $lineNumber): void
	{
		$tokenInfo = $this->analyzeString($line);

		$this->processedLine = [
			'keyword' => $tokenInfo['keyword'],
			'type' => $tokenInfo['type'],
			'details' => $tokenInfo['details'],
			'text' => $line,
			'line' => $lineNumber + 1,
		];

		$this->processedLine['indentationLevel'] = $this->getIndentationLevel($line);

	}


	/**
	 * @param string|NULL $mode
	 * @param string|array|NULL $closeTags
	 */
	private function setSkippedArea($mode, $closeTags = NULL): void
	{
		if ($mode === self::SKIPPED_AREA_INDENTED_BLOCK_MODE) {
			$indentationLevel = $this->processedLine['indentationLevel'] + 1;
		}

		$this->skippedArea = [
			'mode' => $mode,
			'indentationLevel' => $indentationLevel ?? 0,
			'closeTags' => $closeTags ?? []
		];
	}

}
