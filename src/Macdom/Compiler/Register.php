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


final class Register
{

	const SKIP_TAG = 'macdom-off';

	/**
	 * @var array
	 */
	private $booleanAttributes = [];

	/**
	 * @var string
	 */
	private $contentType = Engine::DEFAULT_CONTENT_TYPE;

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
	 * @param array|string $booleanAttribute
	 */
	public function addBooleanAttribute($booleanAttribute, string $contentType = NULL)
	{
		$contentType = $this->getCorrectContentType($contentType, $isXhtmlContentType);

		if (is_string($booleanAttribute)) {
			$booleanAttribute = Helpers::explodeString($booleanAttribute);
		}

		foreach ($booleanAttribute as $booleanAttributeToAdd) {
			if ($isXhtmlContentType) {
				if ( ! $this->findBooleanAttribute($booleanAttributeToAdd, Engine::CONTENT_HTML)) {
					$this->booleanAttributes[Engine::CONTENT_HTML][] = $booleanAttributeToAdd;
				}

				if ( ! $this->findBooleanAttribute($booleanAttributeToAdd, Engine::CONTENT_XML)) {
					$this->booleanAttributes[Engine::CONTENT_XML][] = $booleanAttributeToAdd;
				}

				continue;
			}

			$this->booleanAttributes[$contentType][] = $booleanAttributeToAdd;
		}
	}


	public function addElement(string $element, array $settings = NULL)
	{
		$settings = $settings ?? [];
		$xhtmlElement = $settings && in_array(Engine::CONTENT_XHTML, $settings);
		$contentType = $settings && in_array(Engine::CONTENT_XML, $settings)
			? Engine::CONTENT_XML
			: Engine::CONTENT_HTML;

		if ($settings) {
			$openTags = $settings['openTags'] ?? [];
			$closeTags = $settings['closeTags'] ?? [];
			$this->addElementCustomTags($openTags, $closeTags);
		}

		if ($xhtmlElement) {
			$this->elements[Engine::CONTENT_HTML][$element] = $settings;
			$this->elements[Engine::CONTENT_XML][$element] = $settings;

			return;
		}

		$this->elements[$contentType][$element] = $settings;
	}


	public function addMacro(string $keyword, Callable $macro, array $flags = NULL)
	{
		$xhtmlMacro = $flags && in_array(Engine::CONTENT_XHTML, $flags);
		$contentType = $flags && in_array(Engine::CONTENT_XML, $flags) ? Engine::CONTENT_XML : Engine::CONTENT_HTML;
		$macroSettings = [
			'flags' => $flags ?? [],
			'callback' => $macro
		];

		if ($xhtmlMacro) {
			$this->macros[Engine::CONTENT_HTML][$keyword] = $macroSettings;
			$this->macros[Engine::CONTENT_XML][$keyword] = $macroSettings;

			return;
		}

		$this->macros[$contentType][$keyword] = $macroSettings;
	}


	/**
	 * @param array|string $quickAttributes
	 * @throws SetupException
	 */
	public function changeElementQuickAttributes(string $element, $quickAttributes, string $contentType = NULL)
	{
		$contentType = $this->getCorrectContentType($contentType);

		if ( ! $this->findElement($element, $contentType)) {
			throw new SetupException('Can\'t change quick attributes for undefined element "' . $element . '"');
		}

		if (is_string($quickAttributes)) {
			$quickAttributes = Helpers::explodeString($quickAttributes);
		}

		$this->elements[$contentType][$element]['quickAttributes'] = $quickAttributes;
	}


	public function findBooleanAttribute(string $attribute, string $contentType = NULL): bool
	{
		$contentType = $contentType ?? $this->contentType;

		return isset($this->booleanAttributes[$contentType])
			&& in_array($attribute, $this->booleanAttributes[$contentType]);
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


	/**
	 * @return Callable|NULL
	 */
	public function getMacro(string $macro, string $contentType = NULL)
	{
		$contentType = $contentType ?? $this->contentType;

		if (array_key_exists($macro, $this->macros[$contentType])) {
			return $this->macros[$contentType][$macro]['callback'];
		}

		$text = $macro;

		foreach ($this->macros[$contentType] as $regularExpression => $macroObject) {
			if ( ! in_array(Token::REGULAR_EXPRESSION_MACRO, $macroObject['flags'])) {
				continue;
			}

			if (preg_match('/' . $regularExpression . '/', $text)) {
				return $macroObject['callback'];
			}
		}

		return NULL;
	}


	public function getMacros(): array
	{
		return $this->macros;
	}


	public function getSkippedElements(): array
	{
		$elements = $this->getElements(TRUE);
		$skippedElements = [self::SKIP_TAG];

		foreach ($elements as $element => $settings) {
			if (in_array(Engine::CONTENT_SKIPPED, $settings)) {
				$skippedElements[] = $element;
			}
		}

		return $skippedElements;
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


	/**
	 * @param array|string $booleanAttribute
	 */
	public function removeBooleanAttribute($booleanAttribute, string $contentType = NULL): self
	{
		$contentType = $this->getCorrectContentType($contentType, $isXhtmlContentType);

		if (is_string($booleanAttribute)) {
			$booleanAttribute = Helpers::explodeString($booleanAttribute);
		}

		foreach ($booleanAttribute as $booleanAttributeToRemove) {

			if ($isXhtmlContentType) {
				if ($this->findBooleanAttribute($booleanAttributeToRemove, Engine::CONTENT_HTML)) {
					$booleanAttributeKey = array_search(
						$booleanAttributeToRemove, $this->booleanAttributes[Engine::CONTENT_HTML]
					);
					unset($this->booleanAttributes[Engine::CONTENT_HTML][$booleanAttributeKey]);
				}

				if ($this->findBooleanAttribute($booleanAttributeToRemove, Engine::CONTENT_XML)) {
					$booleanAttributeKey = array_search(
						$booleanAttributeToRemove, $this->booleanAttributes[Engine::CONTENT_XML]
					);
					unset($this->booleanAttributes[Engine::CONTENT_HTML][$booleanAttributeKey]);
				}

			} elseif ($this->findBooleanAttribute($booleanAttributeToRemove, $contentType)) {
				$booleanAttributeKey = array_search($booleanAttributeToRemove, $this->booleanAttributes[$contentType]);
				unset($this->booleanAttributes[$contentType][$booleanAttributeKey]);
			}
		}

		return $this;
	}


	/**
	 * @param array|string $element
	 */
	public function removeElement($element, string $contentType = NULL): self
	{
		$contentType = $this->getCorrectContentType($contentType, $isXhtmlContentType);

		if (is_string($element)) {
			$element = Helpers::explodeString($element);
		}

		foreach ($element as $elementToRemove) {
			if ($isXhtmlContentType) {
				if ($this->findElement($elementToRemove, Engine::CONTENT_HTML)) {
					unset($this->elements[Engine::CONTENT_HTML][$elementToRemove]);
				}

				if ($this->findElement($elementToRemove, Engine::CONTENT_XML)) {
					unset($this->elements[Engine::CONTENT_XML][$elementToRemove]);
				}

			} elseif ($this->findElement($elementToRemove, $contentType)) {
				unset($this->elements[$contentType][$elementToRemove]);
			}
		}

		return $this;
	}


	/**
	 * @param array|string $macro
	 */
	public function removeMacro($macro, string $contentType = NULL): self
	{
		$contentType = $this->getCorrectContentType($contentType, $isXhtmlContentType);

		if (is_string($macro)) {
			$macro = Helpers::explodeString($macro);
		}

		foreach ($macro as $macroToRemove) {
			if ($isXhtmlContentType) {
				$removed = FALSE;
				if ($this->findMacro($macroToRemove, Engine::CONTENT_HTML)) {
					unset($this->macros[Engine::CONTENT_HTML][$macroToRemove]);
					$removed = TRUE;
				}

				if ($this->findMacro($macroToRemove, Engine::CONTENT_XML)) {
					unset($this->macros[Engine::CONTENT_XML][$macroToRemove]);
					$removed = TRUE;
				}

				if ($removed) {
					continue;
				}
			}

			if ($this->findMacro($macroToRemove)) {
				unset($this->macros[$contentType][$macroToRemove]);
			}
		}

		return $this;
	}


	public function setContentType(string $contentType): self
	{
		$this->contentType = $this->getCorrectContentType($contentType);

		return $this;
	}


	private function addElementCustomTags(array $openTags, array $closeTags)
	{
		foreach ($openTags as $openTag) {
			if ($this->elementsCustomOpenTags && preg_match('/' . $this->elementsCustomOpenTags . '/', $openTag)) {
				continue;
			}

			if ($this->elementsCustomOpenTags) {
				$this->elementsCustomOpenTags .= '|';
			}

			$this->elementsCustomOpenTags .= preg_quote($openTag);
		}

		foreach ($closeTags as $closeTag) {
			if ($this->elementsCustomCloseTags && preg_match('/' . $this->elementsCustomCloseTags . '/', $closeTag)) {
				continue;
			}

			if ($this->elementsCustomCloseTags) {
				$this->elementsCustomCloseTags .= '|';
			}

			$this->elementsCustomCloseTags .= preg_quote($closeTag);
		}
	}


	private function getCorrectContentType(string $contentType = NULL, bool &$isXhtmlContentType = NULL): string
	{
		if ( ! $contentType) {
			return Engine::DEFAULT_CONTENT_TYPE;
		}

		if ($contentType === Engine::CONTENT_XHTML) {
			$isXhtmlContentType = TRUE;
			return Engine::CONTENT_HTML;
		}

		return $contentType;
	}

}
