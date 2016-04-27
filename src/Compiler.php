<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2016 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom;

use Machy8\Macdom\Elements\Elements;
use Machy8\Macdom\Macros\Macros;
use Machy8\Macdom\Replicator\Replicator;

class Compiler
{
	/** @const string */
	const AREA_TAG = 'SKIP';
	/** @var Elements */
	private $Elements;
	/** @var Macros */
	private $Macros;
	/** @var Replicator */
	private $Replicator;
	/** @var bool */
	private $booleansWithValue;
	/** @var bool */
	private $closeSelfClosingTags;
	/** @var array */
	private $closeTags = [];
	/** @var string */
	private $codeStorage;
	/** @var int */
	private $finallCodeIndentation;
	/** @var bool */
	private $inNoCompileArea = FALSE;
	/** @var int */
	private $indentMethod;
	/** @var string */
	private $lnBreak;
	/** @var string */
	private $lvlIndentation = "";
	/** @var array */
	private $ncaCloseTags;
	/** @var array */
	private $ncaOpenTags;
	/** @var array */
	private $ncaRegExpInlineTags;
	/** @var array */
	private $ncaRegExpOpenTags;
	/** @var bool */
	private $noCompileAreaClosed;
	/** @var bool */
	private $skipRow = FALSE;
	/** @var array */
	private $skipTags;
	/** @var int */
	private $skippedTagLvl = NULL;
	/** @var int */
	private $spacesPerIndent;
	/** @var bool */
	private $structureHtmlSkeleton;

	/**
	 * Compiler constructor.
	 * @param Setup\Setup $setup
	 * @param Setup\SetupChecker $setupChecker
	 */
	public function __construct($setup, $setupChecker)
	{
		$setupChecker->check($setup);

		$this->Elements = new Elements;
		$this->Macros = new Macros;
		$this->Replicator = new Replicator;

		$closeSelfClosingTags = $setup->closeSelfClosingTags;
		$booleansWithValue = $setup->booleansWithValue;

		if ($setup->preferXhtml)
			$closeSelfClosingTags = $booleansWithValue = TRUE;

		$this->indentMethod = $setup->indentMethod;
		$this->spacesPerIndent = $setup->spacesPerIndent;
		$this->lnBreak = $setup->compressCode ? '' : "\n";
		$this->structureHtmlSkeleton = $setup->structureHtmlSkeleton;
		$this->closeSelfClosingTags = $closeSelfClosingTags;
		$this->booleansWithValue = $booleansWithValue;
		$this->skipTags = $setup->skipTags;
		$this->finallCodeIndentation = $setup->finallCodeIndentation;

		$this->ncaOpenTags = $setup->ncaOpenTags;
		$this->ncaCloseTags = $setup->ncaCloseTags;
		$this->ncaRegExpInlineTags = $setup->ncaRegExpInlineTags;
		$this->ncaRegExpOpenTags = $setup->ncaRegExpOpenTags;

		$this->Elements->addElements($setup->addElements);
		$this->Elements->addBooleanAttributes($setup->addBooleanAttributes);
		$this->Elements->removeBooleanAttributes($setup->removeBooleanAtributes);
		$this->Elements->removeElements($setup->removeElements);
		$this->Elements->changeQkAttributes($setup->changeQkAttributes);

		$this->Macros->addCustomMacros($setup->addMacros);
		$this->Macros->removeMacros($setup->removeMacros);
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function compile($content)
	{
		if (!$content) return '';

		$lns = preg_split('/\n/', $content);

		foreach ($lns as $key => $ln) {
			$lvl = $this->getLnLvl($ln);
			$txt = $this->getLnTxt($ln);
			$element = $this->getElement($txt);
			$elementExists = $this->Elements->findElement($element);
			$noCompileAreaTag = $this->detectNoCompileArea($txt, $element, $lvl);

			if ($this->lnBreak) {
				$indentation = "\t";
				if ($this->finallCodeIndentation === "spaces") {
					$indentation = '    ';
				}

				$this->lvlIndentation = '';
				for ($i = 0; $i < $lvl; $i++) {
					$this->lvlIndentation .= $indentation;
				}
			}

			if ($this->structureHtmlSkeleton && $element === "html") {
				$lvl = 0;
			} elseif ($this->structureHtmlSkeleton) {
				$lvl = in_array($element, ['head', 'body']) ? 1 : $lvl + 1;
			}

			if ($txt && strlen(ltrim($txt)) && !$noCompileAreaTag && !$this->inNoCompileArea && !$this->skipRow && $this->noCompileAreaClosed && !$elementExists) {
				$replicatorResult = $this->Replicator->detect($lvl, $element, $txt);
				if ($replicatorResult['replicate']) {
					$txt = $this->getLnTxt($replicatorResult['ln']);
					$element = $this->getElement($txt);
				}
				if ($replicatorResult['clearLn']) {
					$txt = NULL;
					$element = FALSE;
				}
			}

			if (!$this->inNoCompileArea && !$this->skipRow && $this->Elements->findElement($element)) {
				$clearedText = preg_replace('/' . $element . '/', '', $txt, 1);
				$attributes = $this->processLnAttributes($clearedText);
				$this->addOpenTag($element, $lvl, $attributes);
			} elseif ($txt) {
				$this->addCloseTags($lvl);
				if (!$this->inNoCompileArea && !$noCompileAreaTag && !$this->skipRow) {
					$macro = $this->Macros->replace($element, $txt);
					$macroExists = $macro['exists'];
					$this->codeStorage .= $macroExists ? $this->lvlIndentation . $macro['replacement'] . $this->lnBreak : $this->lvlIndentation . $txt . $this->lnBreak;
				} elseif ($this->inNoCompileArea || $this->skipRow) {
					$this->codeStorage .= !$noCompileAreaTag ? $this->lvlIndentation . $txt . $this->lnBreak : "";
				}
			}
		}
		$this->addCloseTags(0);
		return $this->codeStorage;
	}

	/**
	 * @param string $ln
	 * @return int
	 */
	private function getLnLvl($ln)
	{
		$method = $this->indentMethod;
		preg_match('/^\s+/', $ln, $matches);
		$whites = $matches ? $matches[0] : 0;
		// Only for spaces and combined method
		$spaces = $method === 1 || $method === 3 ? preg_match_all('/ {' . $this->spacesPerIndent . '}/', $whites) : 0;

		// Only for tabulators and combined method
		$tabulators = $method === 2 || $method === 3 ? preg_match_all('/\t/', $whites) : 0;

		if ($method === 3)
			$tabulators *= 2;

		return $spaces + $tabulators;
	}

	/**
	 * @param string $ln
	 * @return string
	 */
	private function getLnTxt($ln)
	{
		return ltrim($ln);
	}

	/**
	 * @param string $txt
	 * @return string
	 */
	private function getElement($txt)
	{
		$element = explode(' ', trim($txt));
		return $element[0];
	}

	/**
	 * @param string $txt
	 * @param string $element
	 * @param int $lvl
	 * @return bool
	 */
	private function detectNoCompileArea($txt, $element, $lvl)
	{
		$txt = trim($txt);
		if ($this->skipRow)
			$this->skipRow = $this->inNoCompileArea = FALSE;

		$areaClosed = $this->inNoCompileArea ? FALSE : TRUE;

		$skipTagClose = '/' . self::AREA_TAG;
		$skippedTags = array_merge(["style", "script", "code"], $this->skipTags);
		$openTags = array_merge(['<style>', '<script>', '<?php', '<?', self::AREA_TAG], $this->ncaOpenTags);
		$closeTags = array_merge(['</style>', '</script>', '?>', $skipTagClose], $this->ncaCloseTags);
		$regExpInlineTags = array_merge(['\<(?:\?|php) .*\?\>', '\<(?:script|style) *[^>]*\>.*\<\/(?:style|script)\>'], $this->ncaRegExpInlineTags);
		$regExpOpenTags = array_merge(['\<(?:script|style) *[^>]*\>'], $this->ncaRegExpOpenTags);

		if (in_array($element, $skippedTags) && $this->skippedTagLvl === NULL) {
			$this->skippedTagLvl = $lvl;
		} elseif ($this->skippedTagLvl !== NULL && $lvl > $this->skippedTagLvl) {
			$this->skipRow = TRUE;
		} elseif ($this->skippedTagLvl !== NULL && $lvl <= $this->skippedTagLvl && in_array($element, $skippedTags)) {
			$this->skippedTagLvl = $lvl;
		} else {
			$this->skippedTagLvl = NULL;
		}

		if (!$this->skippedTagLvl) {
			if (in_array(trim($txt), $openTags)) {
				$this->inNoCompileArea = TRUE;
			} elseif (in_array(trim($txt), $closeTags)) {
				$this->inNoCompileArea = FALSE;
			} else {
				$matchedTag = FALSE;

				if (!$this->inNoCompileArea) {
					foreach ($regExpInlineTags as $tag) {
						if (preg_match('/^\s*' . $tag . '/', $txt)) {
							$matchedTag = $this->skipRow = TRUE;
							break;
						}
					}
				}

				if (!$matchedTag && !$this->inNoCompileArea) {
					foreach ($regExpOpenTags as $tag) {
						if (preg_match('/^\s*' . $tag . '/', $txt)) {
							$matchedTag = $this->inNoCompileArea = TRUE;
							break;
						}
					}
				}

				if (!$matchedTag && $this->inNoCompileArea) {
					foreach ($closeTags as $tag) {
						if (preg_match('/.*' . preg_quote($tag, '/') . '$/', $txt)) {
							$this->skipRow = TRUE;
							$this->inNoCompileArea = FALSE;
							break;
						}
					}
				}
			}
		}

		$tagDetected = $txt === self::AREA_TAG || $txt === $skipTagClose;
		// Set and return
		$this->noCompileAreaClosed = $areaClosed;
		return $tagDetected;
	}

	/**
	 * @param string $txt
	 * @return array
	 */
	private function processLnAttributes($txt)
	{
		// Store the text from the first tag to the end of the line
		$re = '/ \<[\w-]+ .*$/';
		$txtFromTag2End = '';
		if (preg_match($re, $txt, $match)) {
			$txt = preg_replace($re, '', $txt);
			$txtFromTag2End .= $match[0];
		}

		// Preserve php
		$preservedPhp = [];
		while (preg_match('/\<(?:\?(?:php)?) .*?\?\>/', $txt, $matches)) {
			$txt = preg_replace('/\<(?:\?(?:php)?)+ +.*?\?\>/', "PHP_RESERVED_" . count($preservedPhp), $txt, 1);
			$preservedPhp[] = $matches;
		}

		// Replace n$*; for n:href=""
		$re = '/ n\$(.+);/';
		if (preg_match($re, $txt, $matches)) {
			$value = $matches[1] ?: $matches[2];
			$newHref = ' n:href="' . $value . '"';
			$txt = preg_replace($re, $newHref, $txt);
		}

		// Replace -*= for data-*=
		$re = '/ -([\w-]+)+=/';
		if (preg_match_all($re, $txt, $matches)) {
			foreach ($matches[1] as $match) {
				$txt = preg_replace($re, " data-" . $match . "=", $txt, 1);
			}
		}

		// Get all html attributes
		$re = '/ [\w:-]+="[^"]*"| [\w:-]+=\'[^\']*\'| [\w:-]+=\S+/';
		$htmlAttributes = '';
		if (preg_match_all($re, $txt, $matches)) {
			$txt = preg_replace($re, '', $txt);
			$htmlAttributes = implode('', $matches[0]);
		}

		// Get the id selector
		$re = '/ #(\S+)/';
		$idSelector = preg_match($re, $txt, $matches);
		if ($idSelector && !preg_match('/ id="[^"]+"|  id=\'[^\']+\'| id=[\S]+/', $htmlAttributes))
			$htmlAttributes .= ' id="' . $matches[1] . '"';

		if ($idSelector)
			$txt = preg_replace($re, '', $txt);

		// Get all class selectors
		$re = '/ \.(\S+)/';
		$clsSelectors = preg_match_all($re, $txt, $matches);
		if ($clsSelectors) {
			$txt = preg_replace($re, '', $txt);
			$clsSelectors = implode(' ', $matches[1]);
		}

		// Synchronize class selectors
		$re = '/ class="([^"]+)+"| class=\'([^\']+)+\'| class=([\S]+)+/';
		$htmlClsSelector = preg_match($re, $htmlAttributes, $matches);
		if ($clsSelectors && $htmlClsSelector) {
			$htmlAttributes = preg_replace($re, ' class="' . end($matches) . ' ' . $clsSelectors . '"', $htmlAttributes);
		} elseif ($clsSelectors) {
			$htmlAttributes .= ' class="' . $clsSelectors . '"';
		}

		// Get all quick attributes
		$re = '/ ([\d]+)?\$(?:([^$;"]+);|(\S+)+)/';
		$matched = preg_match_all($re, $txt, $matches, PREG_SET_ORDER);
		$qkAttributes = [];
		if ($matched) {
			$txt = preg_replace($re, '', $txt);
			foreach ($matches as $value) {
				$paramVal = end($value);
				if ($paramVal && strtolower($paramVal) !== 'null') {

					// If quick attribute is without index
					$paramKey = is_numeric($value[1]) ? $value[1] : NULL;
					$qkAttributes[] = [
						'key' => $paramKey,
						'value' => $paramVal
					];
				}
			}
		}

		// Get the text
		$txt = $this->getLnTxt($txt) . $txtFromTag2End;

		// Split the txt to an array in oder to get the boolean attributes
		$txt2array = explode(' ', $txt);
		$booleanAttributes = '';
		foreach ($txt2array as $attribute) {
			if ($this->Elements->isBoolean($attribute)) {
				$txt = str_replace($attribute, '', $txt);
				$booleanAttributes .= ' ' . $attribute;
				$booleanAttributes .= $this->booleansWithValue ? '="' . $attribute . '"' : '';
			} else {
				break;
			}
		}

		// Return all attributes
		return [
			'qkAttributes' => $qkAttributes,
			'htmlAttributes' => $htmlAttributes,
			'booleanAttributes' => $booleanAttributes,
			'txt' => $txt,
			'preservedPhp' => $preservedPhp
		];
	}

	/**
	 * @param string $element
	 * @param int $lvl
	 * @param array $attributes
	 */
	private function addOpenTag($element, $lvl, $attributes)
	{
		$indentation = $this->lvlIndentation;
		$elementSettings = $this->Elements->findElement($element, TRUE);
		$openTag = $indentation . '<' . $element;
		$sQkAttributes = $elementSettings['qkAttributes'];
		$qkAttributes = $attributes['qkAttributes'];

		if ($sQkAttributes && $qkAttributes) {
			$usedKeys = [];
			$withoutKey = 0;
			foreach ($qkAttributes as $attribute) {
				$newAttr = NULL;
				if ($attribute['key']) {
					$paramKey = $attribute['key'] - 1;
					if (isset($sQkAttributes[$paramKey])) {
						$newAttr = $sQkAttributes[$paramKey] . '="' . $attribute['value'] . '"';
						$usedKeys[] = $paramKey;
					}
				} elseif (!in_array($withoutKey, $usedKeys) && array_key_exists($withoutKey, $sQkAttributes)) {
					$newAttr = $sQkAttributes[$withoutKey] . '="' . $attribute['value'] . '"';
					$withoutKey++;
				}
				$openTag .= $newAttr ? ' ' . $newAttr : "";
			}
		}
		// Add html and boolean attributes
		$openTag .= $attributes['htmlAttributes'] . $attributes['booleanAttributes'];

		// Close the open tag, add close tags if needed
		$selfClosing = $elementSettings['paired'] || !$this->closeSelfClosingTags ? '' : ' /';
		$openTag .= $selfClosing . '>' . $this->lnBreak;
		$preservedPhp = $attributes['preservedPhp'];
		$phpMark = "PHP_RESERVED_";
		if ($preservedPhp) {
			foreach ($preservedPhp as $key => $code) {
				if (preg_match("/" . $phpMark . $key . "/", $openTag)) {
					$openTag = str_replace($phpMark . $key, $code[0], $openTag);
					$preservedPhp[$key] = FALSE;
				}
			}
		}
		$this->addCloseTags($lvl);
		$this->codeStorage .= $openTag;

		// If the tag is paired add its close tag to the storage
		if ($elementSettings['paired']) {
			$singleLvl = '';
			if ($this->lnBreak)
				$singleLvl = $this->finallCodeIndentation === "spaces" ? '    ' : "\t";
			$textIndentation = $indentation . $singleLvl;
			$txt = $attributes['txt'];
			if ($txt) {
				if ($preservedPhp) {
					foreach ($preservedPhp as $key => $code) {
						if ($code[0] && preg_match("/" . $phpMark . $key . "/", $txt)) {
							$txt = str_replace($phpMark . $key, $code[0], $txt);
						}
					}
				}
			}
			$this->codeStorage .= $txt ? $textIndentation . $txt . $this->lnBreak : "";
			$closeTag = $indentation . '</' . $element . '>' . $this->lnBreak;
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	/** @param int $lvl */
	private function addCloseTags($lvl)
	{
		$lastTag = $length = count($this->closeTags);
		if ($length > 0) {
			for ($i = $length - 1; $i >= 0; $i--) {
				if ($lvl <= $this->closeTags[$i][0]) {
					$this->codeStorage .= $this->closeTags[$i][1];
					$lastTag = $i;
				} else {
					break;
				}
			}
			array_splice($this->closeTags, $lastTag);
		}
	}
}
