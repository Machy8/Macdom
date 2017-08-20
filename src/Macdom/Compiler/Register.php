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


	/**
	 * @param string|array $quickAttributes
	 * @throws SetupException
	 */
	public function changeElementQuickAttributes(string $element, $quickAttributes)
	{
		$quickAttributesType = gettype($quickAttributes);

		if ( ! $this->findElement($element)) {
			throw new SetupException('Can\'t change quick attributes for undefined element "' . $element . '"');
		}

		if ( ! in_array($quickAttributesType, ['string', 'array'])) {
			throw new SetupException(
				'Unsupported type for parameter $quickAttributes. 
				Allowed types are array or string. "' . $quickAttributesType . '" given.'
			);
		}

		if ($quickAttributesType === 'string') {
			$quickAttributes = Helpers::explodeString($quickAttributes);
		}

		$this->elements[$this->contentType][$element]['quickAttributes'] = $quickAttributes;
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
		if ($contentType === Engine::CONTENT_XHTML) {
			$contentType = Engine::CONTENT_HTML;
		}

		$this->contentType = $contentType;

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

}
