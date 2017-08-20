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


final class Compiler
{

	const
		BOOLEAN_ATTRIBUTES = 'booleanAttributes',
		CLASS_SELECTORS = 'classSelectors',
		DATA_ATTRIBUTES = 'dataAttributes',
		HTML_ATTRIBUTES = 'htmlAttributes',
		ID_SELECTOR = 'idSelector',
		QUICK_ATTRIBUTES = 'quickAttributes',

		REPLICATOR_PLACEHOLDERS_RE = '/\[\@\]/',
		REPLICATOR_PLACEHOLDERS_REPLACEMENTS_RE = '/\[(?<replacement>.*?)\]/';

	/**
	 * @var array
	 */
	private $closeTags = [];

	/**
	 * @var string
	 */
	private $output;

	/**
	 * @var Token
	 */
	private $processedToken;

	/**
	 * @var Register
	 */
	private $register;

	/**
	 * @var bool
	 */
	private $xmlSyntax = FALSE;


	public function compile(array $parserOutput): string
	{
		$this->output = '';
		$this->closeTags = [];

		foreach ($parserOutput['tokens'] as $token) {
			$this->processedToken = $token;

			if ($token->text) {
				$this->addCloseTags($token->indentationLevel);
			}

			do {
				$processed = $this->{'process' . ucfirst($this->processedToken->type)}($this->processedToken);

			} while ( ! $processed);
		}

		if ($this->closeTags) {
			$this->addCloseTags();
		}

		$this->output = $this->unsetCodePlaceholders($parserOutput['codePlaceholders'], $this->output);

		return $this->output;
	}


	public function refactorToken(Token $token, string $output)
	{
		$tokenKeyword = Helpers::getFirstWord($output);
		$token->type = Token::TEXT;
		$token->keyword = $tokenKeyword;
		$token->text = $output;
		$isElement = $this->register->findElement($token->keyword);
		$isMacro = $this->register->findMacro($token->keyword);

		if ($isElement) {
			$token->type = Token::ELEMENT;

		} elseif ($isMacro) {
			$token->type = Token::MACRO;
		}

		$this->processedToken = $token;
	}


	public function setRegister(Register $register): self
	{
		$this->register = $register;

		return $this;
	}


	public function setXmlSyntax(string $contentType): self
	{
		$this->xmlSyntax = in_array($contentType, [Engine::CONTENT_XHTML, Engine::CONTENT_XML], TRUE);

		return $this;
	}


	private function addCloseTags(int $tokenIndentationLevel = NULL)
	{
		foreach ($this->closeTags as $closeTag) {
			if ($tokenIndentationLevel !== NULL && $tokenIndentationLevel > $closeTag['indentationLevel']) {
				break;
			}

			$this->addOutput($closeTag['tag']);
			array_shift($this->closeTags);
		}
	}


	private function addOutput(string $code)
	{
		$this->output .= $code;
	}


	private function getTokenText(string $string): string
	{
		return preg_replace('/\|$/', '', trim($string), 1);
	}


	private function processElement(Token $token): bool
	{
		$element = $this->register->getElement($token->keyword);
		$tokenText = Helpers::removeFirstWord($token->text);
		$elementCloseTags = $element['closeTags'] ?? NULL;
		$skipAreasPlaceholders = [];
		$elementAttributes = [];

		$attributesTypes = [
			self::DATA_ATTRIBUTES => '/\s-(?<attributeName>[\w\:-]+)=(?<attributeValue>"[^"]*"|\'[^\']*\'|\S+)/',
			self::HTML_ATTRIBUTES => '/\s(?<attributeName>[\w\:-]+)=(?<attributeValue>"[^"]*"|\'[^\']*\'|\S+)/',
			self::ID_SELECTOR => '/\s#(?<attributeValue>\S+)/',
			self::CLASS_SELECTORS => '/\s\.(?<attributeValue>\S+)/',
			self::QUICK_ATTRIBUTES =>
				'/\s(?<attributeKey>[\d]+)?\$(?:(?<wrappedAttributeValue>[^$;"]+);|(?<attributeValue>\S+)+)/',
			self::BOOLEAN_ATTRIBUTES => '/\s(?<attributeValue>\S+)/'
		];

		$openTag = isset($element['openTags']) ? $element['openTags'][0] : '<';
		$elementToken = $openTag . $token->keyword;

		foreach ($attributesTypes as $typeName => $regularExpression) {
			if ( ! preg_match_all($regularExpression, $tokenText, $matches, PREG_SET_ORDER)) {
				continue;
			}

			$removeMatch = TRUE;

			foreach ($matches as $match) {
				if ($typeName === self::DATA_ATTRIBUTES) {
					$elementAttributes[self::DATA_ATTRIBUTES][$match['attributeName']] = $match['attributeValue'];

				} elseif ($typeName === self::HTML_ATTRIBUTES) {
					$elementAttributes[self::HTML_ATTRIBUTES][$match['attributeName']] = $match['attributeValue'];

				} elseif ($typeName === self::ID_SELECTOR) {
					$elementAttributes[self::ID_SELECTOR] = $match['attributeValue'];

				} elseif ($typeName === self::CLASS_SELECTORS) {
					$elementAttributes[self::CLASS_SELECTORS][] = $match['attributeValue'];

				} elseif ($typeName === self::QUICK_ATTRIBUTES) {
					$elementAttributes[self::QUICK_ATTRIBUTES][] = [
						'key' => $match['attributeKey'] ? (int) $match['attributeKey'] : NULL,
						'value' => $match['wrappedAttributeValue']
							? $match['wrappedAttributeValue']
							: $match['attributeValue']
					];

				} elseif ($typeName === self::BOOLEAN_ATTRIBUTES) {
					if ($this->register->findBooleanAttribute($match['attributeValue'])) {
						$elementAttributes[self::BOOLEAN_ATTRIBUTES][] = $match['attributeValue'];

					} else {
						$removeMatch = FALSE;
					}
				}

				if ($removeMatch) {
					$tokenText = str_replace($match[0], '', $tokenText);
				}
			}
		}

		foreach ($elementAttributes as $attributesType => $attributes) {
			$hasClassSelector = isset($elementAttributes[self::CLASS_SELECTORS]);
			$hasHtmlClassAttribute = isset($elementAttributes[self::HTML_ATTRIBUTES]['class']);

			if ($hasClassSelector || $hasHtmlClassAttribute) {
				$classSelectors = '';

				if ($hasClassSelector) {
					$classSelectors .= implode(' ', $elementAttributes[self::CLASS_SELECTORS]);
					unset($elementAttributes[self::CLASS_SELECTORS]);
				}

				if ($hasHtmlClassAttribute) {
					$classesFromAttribute = str_replace(
						["'", '"'], '', $elementAttributes[self::HTML_ATTRIBUTES]['class']
					);

					$classSelectors .= (bool) $classSelectors ? ' ' . $classesFromAttribute : $classesFromAttribute;
					unset($elementAttributes[self::HTML_ATTRIBUTES]['class']);

					if ($attributesType === self::HTML_ATTRIBUTES) {
						unset($attributes['class']);
					}
				}

				$elementToken .= ' class="' . $classSelectors . '"';
			}

			if ($attributesType === self::DATA_ATTRIBUTES) {
				foreach ($attributes as $attributeName => $attributeValue) {
					$elementToken .= ' data-' . $attributeName . '=' . $attributeValue;
				}

			} elseif ($attributesType === self::HTML_ATTRIBUTES) {
				foreach ($attributes as $attributeName => $attributeValue) {
					$elementToken .= ' ' . $attributeName . '=' . $attributeValue;
				}

			} elseif ($attributesType === self::ID_SELECTOR
				&& ! isset($elementAttributes[self::HTML_ATTRIBUTES]['id'])
			) {
				$elementToken .= ' id="' . $attributes . '"';

			} elseif ($attributesType === self::QUICK_ATTRIBUTES) {
				if ( ! isset($element[self::QUICK_ATTRIBUTES])) {
					throw new CompileException(
						'Element "' . $token->keyword . '" has no quick attributes on line '
						. $token->line . ' near "' . $token->text . '"');
				}

				$usedKeys = [];
				$keysCounter = 0;

				foreach ($attributes as $attributeOrder => $attribute) {
					if ($attribute['key']) {
						$attributeKey = $attribute['key'];
						$attributeArrayKey = $attributeKey - 1;

					} else {
						$attributeKey = $attributeArrayKey = $keysCounter++;
					}

					if ( ! in_array($attributeKey, $usedKeys)) {
						$usedKeys[] = $attributeKey;

					} else {
						throw new CompileException(
							'Element quick attribute "' . $element[self::QUICK_ATTRIBUTES][$attributeArrayKey]
							. '" with key "' . $attributeKey . '" already used on line ' . $token->line
							. ' near "' . $tokenText . '"');
					}

					if (isset($element[self::QUICK_ATTRIBUTES][$attributeArrayKey])) {
						$elementToken .= ' ' . $element[self::QUICK_ATTRIBUTES][$attributeArrayKey]
							. '="' . $attribute['value'] . '"';

					} else {
						throw new CompileException(
							'Element quick attribute with key "' . $attributeKey . '" doesn\'t exist on line '
							. $token->line . ' near "' . $tokenText . '". Quick attributes for element "'
							. $token->keyword . '" are "' . implode(', ', $element[self::QUICK_ATTRIBUTES]) . '"');
					}
				}

			} elseif ($attributesType === self::BOOLEAN_ATTRIBUTES) {
				foreach ($attributes as $attribute) {
					$elementToken .= $this->xmlSyntax ? ' ' . $attribute . '="' . $attribute . '"' : ' ' . $attribute;
				}
			}
		}

		$elementIsUnpaired = in_array(Token::UNPAIRED_ELEMENT, $element);

		if (is_array($element) && $elementIsUnpaired && ! $elementCloseTags) {
			if ($this->xmlSyntax) {
				$elementToken .= ' /';
			}

		} else {
			$closeTag = $elementCloseTags ? $element['closeTags'][0] : '</' . $token->keyword . '>';
			$closeTag = [
				'tag' => $closeTag,
				'indentationLevel' => $token->indentationLevel
			];

			array_unshift($this->closeTags, $closeTag);
		}

		if ( ! $elementCloseTags) {
			$elementToken .= '>';
		}

		$this->addOutput($elementToken);
		$elementText = $this->getTokenText($tokenText);

		if ($skipAreasPlaceholders) {
			foreach ($skipAreasPlaceholders as $placeholderKey => $code) {
				$elementText = str_replace($placeholderKey, $code, $elementText);
			}
		}

		if ($elementText && ! $elementIsUnpaired) {
			$this->addOutput($elementText);
		}

		return TRUE;
	}


	private function processMacro(Token $token): bool
	{
		$macro = $this->register->getMacro($token->keyword);
		$token->text = Helpers::removeFirstWord($token->text);
		$output = $macro($this->getTokenText($token->text), $token->keyword);
		$this->refactorToken($token, $output);

		return FALSE;
	}


	private function processReplicatorReplica(Token $token): bool
	{
		$replicatedText = $token->text['replicated'];
		$synchronizedText = $token->text['synchronized'];

		if (preg_match_all(self::REPLICATOR_PLACEHOLDERS_REPLACEMENTS_RE, $synchronizedText, $matches)) {
			$matches = $matches['replacement'];
			$replicatedText = preg_replace_callback(self::REPLICATOR_PLACEHOLDERS_RE,
				function () use (&$matches) {
					return array_shift($matches);
				}, $replicatedText);

			$synchronizedText = preg_replace(self::REPLICATOR_PLACEHOLDERS_REPLACEMENTS_RE, '', $synchronizedText);
			$replicatedText = preg_replace(self::REPLICATOR_PLACEHOLDERS_RE, '', $replicatedText);
		}

		$replicatorOutput = $this->getTokenText($replicatedText) . $this->getTokenText($synchronizedText);
		$this->refactorToken($token, $replicatorOutput);

		return FALSE;
	}


	private function processText(Token $token): bool
	{
		$text = $this->getTokenText($token->text);
		$this->addOutput($text);

		return TRUE;
	}


	private function unsetCodePlaceholders(array $codePlaceholders, string $string): string
	{
		$codePlaceholders = array_reverse($codePlaceholders);

		foreach ($codePlaceholders as $codePlaceholder => $code) {
			$string = str_replace($codePlaceholder, $code, $string);
		}

		return $string;
	}

}
