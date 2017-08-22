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
			Token::ELEMENT,
			Token::MACRO,
			Token::REPLICATOR_OPEN_TAG,
			Token::REPLICATOR_CLOSE_TAG,
			Token::REPLICATOR_REPLICA,
			Token::TEXT
		],

		REPLICATOR_CLOSE_TAG_RE = '/^\/' . self::REPLICATOR_PREFIX . '(?<keyword>[\S]*)/',
		REPLICATOR_OPEN_TAG_RE = '/^' . self::REPLICATOR_PREFIX . '(?<keyword>[\S]*)/',
		REPLICATOR_PREFIX = '@',
		REPLICATOR_REGISTER_PREFIX = '#',

		CODE_PLACEHOLDER_NAMESPACE_PREFIX = 'codePlaceholder_',
		CODE_PLACEHOLDER_RE = '/' . self::CODE_PLACEHOLDER_NAMESPACE_PREFIX . '\d+/';

	/**
	 * @var array
	 */
	private $codePlaceholdersRegularExpression = [];

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
	 * @var Register
	 */
	private $register;

	/**
	 * @var array
	 */
	private $replicatorRegister = [];


	public function isElementUnpaired(array $elementSettings): bool
	{
		return in_array(Engine::UNPAIRED_ELEMENT, $elementSettings, TRUE);
	}


	public function parse(string $input): array
	{
		$this->codePlaceholdersRegularExpression = [];
		$this->output = [];
		$this->setCodePlaceholdersRegularExpressions();
		$input = str_replace("\r\n", "\n", $input);
		$input = $this->setCodePlaceholders($input, $codePlaceholders);

		foreach (preg_split("/\\r|\\n/", $input, -1, PREG_SPLIT_DELIM_CAPTURE) as $lineNumber => $line) {
			$this->setProcessedLine($line, $lineNumber);

			foreach (self::TOKEN_TYPES_MATCH_ORDER as $methodName) {
				if ($this->{'match' . ucfirst($methodName)}()) {
					break;
				}
			}
		}

		return [
			'tokens' => $this->output,
			'codePlaceholders' => $codePlaceholders
		];
	}


	public function setCodePlaceholders(string $string, array &$codePlaceholders = NULL): string
	{
		$codePlaceholders = [];

		foreach ($this->codePlaceholdersRegularExpression as $codePlaceholderRe => $surroundByIndentation) {
			preg_match_all($codePlaceholderRe, $string, $matches, PREG_SET_ORDER);

			foreach ($matches as $match) {
				$fullMatch = $match[0];
				$fullMatchCopy = $fullMatch;
				$codeToReplace = end($match);

				if ( ! trim($codeToReplace)) {
					continue;
				}

				$codePlaceholder = uniqid(self::CODE_PLACEHOLDER_NAMESPACE_PREFIX);

				if ($surroundByIndentation) {
					$indentation = $this->indentationMethod === Engine::TABS_INDENTATION ? "\t" : " ";
					$codePlaceholder = $indentation . $codePlaceholder;
				}

				$fullMatch = str_replace($codeToReplace, $codePlaceholder, $fullMatch);
				$string = preg_replace('/' . preg_quote($fullMatchCopy, '/') . '/', $fullMatch, $string, 1);
				$codePlaceholders[trim($codePlaceholder)] = $codeToReplace;
			}
		}

		return preg_replace('/(?:<\/?' . Register::SKIP_TAG . '>|' . Register::SKIP_TAG . ')\s*/', '', $string);
	}


	public function setRegister(Register $register): self
	{
		$this->register = $register;

		return $this;
	}


	public function setSpacesIndentationMethod(int $indentationSize = NULL): self
	{
		$this->indentationMethod = Engine::SPACES_INDENTATION;
		$this->indentationSize = $indentationSize;

		return $this;
	}


	private function addToken(string $type, array $parameters = NULL)
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
		$matched = preg_match('/^\s+/', $token, $matches);
		$whitespaceCharacters = $matched ? $matches[0] : '';
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
		if ( ! $this->register->findElement($this->processedLine['keyword'])) {
			return FALSE;
		}

		$this->addToken(Token::ELEMENT, [
			'keyword' => $this->processedLine['keyword']
		]);

		return TRUE;
	}


	private function matchMacro(): bool
	{
		if ( ! $this->register->findMacro($this->processedLine['keyword'])) {
			return FALSE;
		}

		$this->addToken(Token::MACRO, [
			'keyword' => $this->processedLine['keyword']
		]);

		return TRUE;
	}


	private function matchReplicatorCloseTag(): bool
	{
		if ( ! preg_match(self::REPLICATOR_CLOSE_TAG_RE, $this->processedLine['keyword'], $matches)) {
			return FALSE;
		}

		$keyword = preg_replace('/^\/@/', '', $this->processedLine['keyword'], 1);
		$processedLineLevel = $this->processedLine['indentationLevel'];
		$replicatedLineKey = (bool) $keyword ? $keyword : '#' . $processedLineLevel;

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
		if ( ! preg_match(self::REPLICATOR_OPEN_TAG_RE, $this->processedLine['keyword'], $matches)) {
			return FALSE;
		}

		if ((bool) $matches['keyword']) {
			if (substr($matches['keyword'], 0, 1) === self::REPLICATOR_REGISTER_PREFIX) {
				throw new CompileException(
					'Replicated line keyword can\'t start with "' . self::REPLICATOR_REGISTER_PREFIX
					. '" because it is default internal selector on line "' . $this->processedLine['line']
					. '" near "' . $this->processedLine['keyword'] . '"');
			}

			$this->replicatorRegister[$matches['keyword']] = [
				'indentationLevel' => $this->processedLine['indentationLevel'],
				'text' => $this->processedLine['text']
			];

		} else {
			$newReplicatorRegisterItemKey = self::REPLICATOR_REGISTER_PREFIX . $this->processedLine['indentationLevel'];
			$this->replicatorRegister[$newReplicatorRegisterItemKey] = $this->processedLine['text'];
		}

		return TRUE;
	}


	private function matchReplicatorReplica(): bool
	{
		if ( ! $this->processedLine['text']) {
			return TRUE;
		}

		$processedLineKeyword = $this->processedLine['keyword'];
		$processedLineLevel = $this->processedLine['indentationLevel'];

		if (isset($this->replicatorRegister[$processedLineKeyword])
			&& $processedLineLevel >= $this->replicatorRegister[$processedLineKeyword]['indentationLevel']
		) {
			$replicatedText = $this->replicatorRegister[$processedLineKeyword]['text'];
			$synchronizedText = Helpers::removeFirstWord($this->processedLine['text']);

		} elseif (isset($this->replicatorRegister[self::REPLICATOR_REGISTER_PREFIX . $processedLineLevel])) {
			$replicatedText = $this->replicatorRegister[self::REPLICATOR_REGISTER_PREFIX . $processedLineLevel];
			$synchronizedText = $this->processedLine['text'];

		} else {
			return FALSE;
		}

		$this->addToken(Token::REPLICATOR_REPLICA, [
			'text' => [
				'replicated' => Helpers::removeFirstWord($replicatedText),
				'synchronized' => $synchronizedText
			]
		]);

		return TRUE;
	}


	private function matchText(): bool
	{
		$this->addToken(Token::TEXT);

		return TRUE;
	}


	private function setCodePlaceholdersRegularExpressions()
	{
		$indentation = '\t';
		$firstIndentationMultiplier = '*';

		if ($this->indentationMethod !== Engine::TABS_INDENTATION) {
			$indentation = ' {' . $this->indentationSize . '}';
			$firstIndentationMultiplier = '';
		}

		$skippedElements = $this->register->getSkippedElements();
		$skippedElements = join('|', $skippedElements);

		// Regular expression => indented from left
		$this->codePlaceholdersRegularExpression = [
			'/<\?php(?: |\n)(?:.|\n)*\?>/Um' => FALSE, // PHP code
			'/(' . $indentation . $firstIndentationMultiplier . ')(?<! |\S)(?:' . $skippedElements . ')((?= ) [\S\h]+(?:.*\n\1' . $indentation . '.*)*|((?= *\n)(?:.*\n\1' . $indentation . '.*)+))/' => TRUE, // indented block
			'/<(' . $skippedElements . ')(?:[-\w]+)?(?:[^>]+)?>([\s\S]*?)<\/\1>/' => FALSE // tags
		];
	}


	private function setProcessedLine(string $line, int $lineNumber)
	{
		$this->processedLine = [
			'keyword' => Helpers::getFirstWord($line),
			'text' => $line,
			'line' => $lineNumber + 1
		];

		$this->processedLine['indentationLevel'] = $this->getIndentationLevel($line);
	}

}
