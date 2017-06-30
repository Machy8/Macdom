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
	 * @var string
	 */
	public $contentType = Engine::DEFAULT_CONTENT_TYPE;

	/**
	 * @var array
	 */
	private $booleanAttributes = [];

	/**
	 * @var array
	 */
	private $closeTags = [];

	/**
	 * @var array
	 */
	private $elements = [];

	/**
	 * @var string
	 */
	private $elementsCustomCloseTags;

	/**
	 * @var string
	 */
	private $elementsCustomOpenTags;

	/**
	 * @var array
	 */
	private $macros = [];

	/**
	 * @var string
	 */
	private $output;

	/**
	 * @var Token
	 */
	private $processedToken;

	/**
	 * @var bool
	 */
	private $xmlSyntax = FALSE;


	/**
	 * @param string $attribute
	 * @param string|array|NULL $contentType
	 */
	public function addBooleanAttribute(string $attribute, $contentType = NULL)
	{
		$booleanAttributes = Helpers::explodeString($attribute);
		$contentType = $contentType ?? Engine::CONTENT_HTML;
		$contentTypes = is_array($contentType) ? $contentType : [$contentType];

		foreach ($booleanAttributes as $booleanAttribute) {
			foreach ($contentTypes as $contentType) {
				if ($this->findBooleanAttribute($booleanAttribute, $contentType)) {
					continue;
				}

				$this->booleanAttributes[$contentType][] = $booleanAttribute;
			}
		}
	}


	public function addElement(string $element, array $settings = NULL)
	{
		$contentType = $settings && in_array(Engine::CONTENT_XML, $settings)
			? Engine::CONTENT_XML
			: Engine::CONTENT_HTML;

		$openTags = $settings['openTags'] ?? [];
		$closeTags = $settings['closeTags'] ?? [];

		$this->addElementCustomTags($openTags, $closeTags);
		$this->elements[$contentType][$element] = $settings ?? [];
	}


	public function addMacro(string $keyword, Callable $macro, array $flags = NULL)
	{
		$contentType = $flags && in_array(Engine::CONTENT_XML, $flags)
			? Engine::CONTENT_XML
			: Engine::CONTENT_HTML;

		$this->macros[$contentType][$keyword] = [
			'flags' => $flags ?? [],
			'callback' => $macro
		];
	}


	public function changeElementQuickAttributes(string $element, array $quickAttributes)
	{
		if ( ! $this->findElement($element)) {
			throw new SetupException('Can\'t change quick attributes for undefined element "' . $element . '"');
		}

		$this->elements[$element]['quickAttributes'] = $quickAttributes;
	}


	public function compile(array $parserOutput): string
	{
		$this->output = '';
		$this->closeTags = [];

		foreach ($parserOutput['tokens'] as $token) {
			$this->processedToken = $token;

			if ( ! ! $token->text) {
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


	public function findElement(string $element, string $contentType = NULL): bool
	{
		return $this->getElement($element, $contentType) !== NULL;
	}


	public function findMacro(string $macro, string $contentType = NULL): bool
	{
		return (bool) $this->getMacro($macro, $contentType);
	}


	/**
	 * @param string $element
	 * @param string|NULL $contentType
	 * @return array|NULL
	 */
	public function getElement(string $element, string $contentType = NULL)
	{
		$contentType = $contentType ?? $this->contentType;

		return array_key_exists($element, $this->elements[$contentType])
			? $this->elements[$contentType][$element]
			: NULL;
	}


	public function getElements(bool $byContentType = NULL): array
	{
		return $byContentType ? $this->elements[$this->contentType] : $this->elements;
	}


	public function getElementsBooleanAttributes(): array
	{
		return $this->booleanAttributes;
	}


	public function getElementsCustomCloseTags(): string
	{
		return $this->elementsCustomCloseTags ?? '';
	}


	public function getElementsCustomOpenTags(): string
	{
		return $this->elementsCustomOpenTags ?? '';
	}


	public function getMacros(): array
	{
		return $this->macros;
	}


	public function getTokenText(string $string): string
	{
		return preg_replace('/\|$/', '', trim($string), 1);
	}


	public function getUnpairedElements(): array
	{
		$unpairedElements = [];

		foreach ($this->getElements(TRUE) as $element => $settings) {
			if (in_array(Engine::UNPAIRED_ELEMENT, $settings)) {
				$unpairedElements[] = $element;
			}
		}

		return $unpairedElements;
	}


	public function refactorToken(Token $token, string $output)
	{
		$tokenKeyword = Helpers::getFirstWord($output);
		$token->type = Token::TEXT;
		$token->keyword = $tokenKeyword;
		$token->text = $output;
		$isElement = $this->findElement($token->keyword);
		$isMacro = $this->findMacro($token->keyword);

		if ($isElement) {
			$token->type = Token::ELEMENT;

		} elseif ($isMacro) {
			$token->type = Token::MACRO;
		}

		$this->processedToken = $token;
	}


	public function removeBooleanAttribute(string $booleanAttribute, string $contentType = NULL): self
	{
		$booleanAttributes = Helpers::explodeString($booleanAttribute);
		$contentType = $contentType ?? Engine::CONTENT_HTML;

		foreach ($booleanAttributes as $booleanAttribute) {
			$booleanAttributeKey = array_search($booleanAttribute, $this->booleanAttributes[$contentType]);

			if ($booleanAttributeKey === FALSE) {
				throw new SetupException('Can\'t remove undefined boolean attribute "' . $booleanAttribute . '"');
			}

			unset($this->booleanAttributes[$contentType][$booleanAttributeKey]);
		}

		return $this;
	}


	public function removeElement(string $element, string $contentType = NULL): self
	{
		$elements = Helpers::explodeString($element);
		$contentType = $contentType ?? Engine::CONTENT_HTML;

		foreach ($elements as $element) {
			if ( ! $this->findElement($element, $contentType)) {
				throw new SetupException('Can\'t remove undefined element "' . $element . '"');
			}

			unset($this->elements[$contentType][$element]);
		}

		return $this;
	}


	public function removeMacro(string $macro, string $contentType = NULL): self
	{
		$macros = Helpers::explodeString($macro);
		$contentType = $contentType ?? Engine::CONTENT_HTML;

		foreach ($macros as $macro) {
			if ( ! $this->findMacro($macro)) {
				throw new SetupException('Can\'t remove undefined macro "' . $macro . '"');
			}

			unset($this->macros[$contentType][$macro]);
		}

		return $this;
	}


	public function setContentType(string $contentType): self
	{
		$this->xmlSyntax = in_array($contentType, [Engine::CONTENT_XHTML, Engine::CONTENT_XML], TRUE);

		if ($contentType === Engine::CONTENT_XHTML) {
			$contentType = Engine::CONTENT_HTML;
		}

		$this->contentType = $contentType;

		return $this;
	}


	public function unsetCodePlaceholders(array $codePlaceholders, string $string): string
	{
		$codePlaceholders = array_reverse($codePlaceholders);

		foreach ($codePlaceholders as $codePlaceholder => $code) {
			$string = str_replace($codePlaceholder, $code, $string);
		}

		return $string;
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


	private function addElementCustomTags(array $openTags, array $closeTags)
	{
		foreach ($openTags as $openTag) {
			if ($this->elementsCustomOpenTags
				&& preg_match('/' . $this->elementsCustomOpenTags . '/', $openTag)
			) {
				continue;
			}

			if ($this->elementsCustomOpenTags) {
				$this->elementsCustomOpenTags .= '|';
			}

			$this->elementsCustomOpenTags .= preg_quote($openTag);
		}

		foreach ($closeTags as $closeTag) {
			if ($this->elementsCustomCloseTags
				&& preg_match('/' . $this->elementsCustomCloseTags . '/', $closeTag)
			) {
				continue;
			}

			if ($this->elementsCustomCloseTags) {
				$this->elementsCustomCloseTags .= '|';
			}

			$this->elementsCustomCloseTags .= preg_quote($closeTag);
		}

	}


	private function addOutput(string $code)
	{
		$this->output .= $code;
	}


	private function findBooleanAttribute(string $attribute, string $contentType = NULL): bool
	{
		$contentType = $contentType ?? $this->contentType;

		return isset($this->booleanAttributes[$contentType])
			&& in_array($attribute, $this->booleanAttributes[$contentType]);
	}


	/**
	 * @param string $macro
	 * @param string|NULL $contentType
	 * @return Callable|NULL
	 */
	private function getMacro(string $macro, string $contentType = NULL)
	{
		$contentType = $contentType ?? $this->contentType;

		if (array_key_exists($macro, $this->macros[$contentType])) {
			return $this->macros[$contentType][$macro]['callback'];

		} else {
			$text = $macro;

			foreach ($this->macros[$contentType] as $regularExpression => $macroObject) {
				if ( ! in_array(Token::REGULAR_EXPRESSION_MACRO, $macroObject['flags'])) {
					continue;
				}

				if (preg_match('/' . $regularExpression . '/', $text)) {
					return $macroObject['callback'];
				}
			}
		}

		return NULL;
	}


	private function processElement(Token $token): bool
	{
		$element = $this->getElement($token->keyword);
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

		$openTag = isset($element['openTags'])
			? $element['openTags'][0]
			: '<';

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
					if ($this->findBooleanAttribute($match['attributeValue'])) {
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
				if ($hasClassSelector) {
					$classSelectors = implode(' ', $elementAttributes[self::CLASS_SELECTORS]);
					unset($elementAttributes[self::CLASS_SELECTORS]);
				}

				if ($hasHtmlClassAttribute) {
					$classesFromAttribute = str_replace(["'", '"'], '',
						$elementAttributes[self::HTML_ATTRIBUTES]['class']);

					$classSelectors = isset($classSelectors)
						? $classSelectors . ' ' . $classesFromAttribute
						: $classesFromAttribute;

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
							'Element quick attribute with key "' . $attributeKey
							. '" doesn\'t exist on line ' . $token->line . ' near "'
							. $tokenText . '". Quick attributes for element "'
							. $token->keyword . '" are "' . implode(', ', $element[self::QUICK_ATTRIBUTES]) . '"');
					}
				}

			} elseif ($attributesType === self::BOOLEAN_ATTRIBUTES) {
				foreach ($attributes as $attribute) {
					$elementToken .= $this->xmlSyntax
						? ' ' . $attribute . '="' . $attribute . '"'
						: ' ' . $attribute;
				}
			}
		}

		$elementIsUnpaired = in_array(Token::UNPAIRED_ELEMENT, $element);

		if (is_array($element) && $elementIsUnpaired && ! $elementCloseTags) {
			if ($this->xmlSyntax) {
				$elementToken .= ' /';
			}

		} else {
			$closeTag = $elementCloseTags
				? $element['closeTags'][0]
				: '</' . $token->keyword . '>';

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
		$macro = $this->getMacro($token->keyword);
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

			$synchronizedText =
				preg_replace(self::REPLICATOR_PLACEHOLDERS_REPLACEMENTS_RE, '', $synchronizedText);

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

}
