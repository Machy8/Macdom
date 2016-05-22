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
	/** @var array */
	private $contentQueue = [];
	/** @var bool */
	private $compressText;
	/** @var int */
	private $outputIndentation;
	/** @var bool */
	private $inNoCompileArea = FALSE;
	/** @var int */
	private $indentMethod;
	/** @var bool */
	private $compressCode;
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
	private $skipElements;
	/** @var int */
	private $skippedElementLvl = NULL;
	/** @var int */
	private $spacesPerIndent;
	/** @var bool */
	private $structureHtmlSkeleton;

	/**
	 * Compiler constructor.
	 * @param Setup\Setup $setup
	 */
	public function __construct($setup)
	{
		$this->Elements = new Elements;
		$this->Macros = new Macros;
		$this->Replicator = new Replicator;

		$closeSelfClosingTags = $setup->closeSelfClosingTags;
		$booleansWithValue = $setup->booleansWithValue;

		if ($setup->preferXhtml)
			$closeSelfClosingTags = $booleansWithValue = TRUE;

		$this->indentMethod = $setup->indentMethod;
		$this->spacesPerIndent = $setup->spacesPerIndent;
		$this->compressCode = $setup->compressCode;
		$this->compressText = $setup->compressText;
		$this->structureHtmlSkeleton = $setup->structureHtmlSkeleton;
		$this->closeSelfClosingTags = $closeSelfClosingTags;
		$this->booleansWithValue = $booleansWithValue;
		$this->skipElements = array_merge(['script', 'style', 'textarea', 'code'], explode(' ', $setup->skipElements));
		$this->ncaOpenTags = ['<?php', '<?', self::AREA_TAG];
		$this->ncaCloseTags = ['?>', '/' . self::AREA_TAG];
		$inlineOpenTags = join("|", array_filter($this->skipElements));
		$this->ncaRegExpInlineTags = ['\<\?(?:php)? .*\?\>', '\<(?:' . $inlineOpenTags . ') *[^>]*\>.*\<\/(?:' . $inlineOpenTags . ')\>'];
		$this->ncaRegExpOpenTags = ['\<(?:' . $inlineOpenTags . ') *[^\>]*\>', '\<\?(?:php)?'];
		$this->outputIndentation = $setup->outputIndentation;

		foreach ($this->skipElements as $element) {
			$this->ncaCloseTags[] = '</' . $element . '>';
			$this->ncaOpenTags[] = '<' . $element . '>';
		}

		$this->Elements->addQkAttributes($setup->addQkAttributes);
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

		foreach ($lns as $ln) {
			$lvl = $this->getLnLvl($ln);
			$element = $this->getElement($ln);
			$noCompileAreaTag = $this->detectNoCompileArea($ln, $element, $lvl);
			$compilationAllowed = !$this->inNoCompileArea && !$this->skipRow && $this->noCompileAreaClosed;
			if ($noCompileAreaTag || $compilationAllowed && !$ln) continue;

			if ($this->structureHtmlSkeleton) {
				$lvl = in_array($element, ['head', 'body']) ? 1 : $lvl + 1;
				if ($element === 'html') $lvl = 0;
			}

			if ($compilationAllowed && $ln && !$this->Elements->findElement($element)) {
				$ln = preg_replace('/\|{1}$/', '', $ln, 1);
				$replicatorResult = $this->Replicator->detect($lvl, $element, $ln);
				if ($replicatorResult['toReplicate']) {
					$ln = $replicatorResult['toReplicate'];
					$element = $this->getElement($ln);
				}
				if ($replicatorResult['clearLn']) $ln = $element = NULL;
			}

			$processElement = $compilationAllowed && $this->Elements->findElement($element);
			// if compilation allowed => remove "|" if exists on the beginning of the line
			$txt = $this->getLnTxt($ln, $compilationAllowed, $processElement);

			if ($processElement) {
				$attributes = $this->processLn($txt);
				$this->addOpenTag($element, $lvl, $attributes);
			} elseif ($txt) {
				$this->addCloseTags($lvl);

				if (preg_match('/\.((?:css|js))$/', $this->getElement(trim($txt)), $isJsCss)) $element = $isJsCss[1];
				$macro = $compilationAllowed && $this->Macros->findMacro($element);
				$content = $macro ? $this->Macros->replace($element, $txt, !$isJsCss) : $txt;
				$type = $macro ? 'macro' : 'text';

				$this->addToQueue($type, $content, $lvl);
			}
		}

		$this->addCloseTags(0);

		$composedContent = $this->composeContent();
		return $composedContent;
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
	 * @param bool $clean
	 * @param bool $elementExists
	 * @return string
	 */
	private function getLnTxt($ln, $clean = FALSE, $elementExists = FALSE)
	{
		$find = ['/ *' . self::AREA_TAG . '(?:-CONTENT)?/'];
		$txt = ltrim($ln);

		if ($elementExists) $txt = strstr($txt, " ");
		if ($clean) $find[] = '/^\|{1}/';

		$txt = preg_replace($find, '', $txt, 1);

		return $txt;
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
		$skipContent = $skipRow = FALSE;

		if ($this->skipRow)
			$this->skipRow = $this->inNoCompileArea = FALSE;

		$areaClosed = !$this->inNoCompileArea;

		if ($areaClosed) {
			$txt2array = explode(' ', $txt);
			$skipSelector = end($txt2array);
			if ($skipSelector === self::AREA_TAG && count($txt2array) > 1) {
				$skipRow = TRUE;
			} elseif ($skipSelector === self::AREA_TAG . '-CONTENT') {
				$skipContent = TRUE;
			}
		}

		if (in_array($element, $this->skipElements) && ($this->skippedElementLvl === NULL || $this->skippedElementLvl !== NULL && $lvl <= $this->skippedElementLvl) || $skipContent) {
			$this->skippedElementLvl = $lvl;
		} elseif ($this->skippedElementLvl !== NULL && $lvl > $this->skippedElementLvl || $skipRow) {
			$this->skipRow = TRUE;
		} else {
			$this->skippedElementLvl = NULL;
		}

		if (!$this->skippedElementLvl) {
			if (in_array(trim($txt), $this->ncaOpenTags)) {
				$this->inNoCompileArea = TRUE;
			} elseif (in_array(trim($txt), $this->ncaCloseTags)) {
				$this->inNoCompileArea = FALSE;
			} else {
				$matchedTag = FALSE;

				if (!$this->inNoCompileArea) {
					foreach ($this->ncaRegExpInlineTags as $tag) {
						if (preg_match('/^\s*' . $tag . '/', $txt)) {
							$matchedTag = $this->skipRow = TRUE;
							break;
						}
					}
				}

				if (!$matchedTag && !$this->inNoCompileArea) {
					foreach ($this->ncaRegExpOpenTags as $tag) {
						if (preg_match('/^\s*' . $tag . '.*/', $txt)) {
							$matchedTag = $this->inNoCompileArea = TRUE;
							break;
						}
					}
				}

				if (!$matchedTag && $this->inNoCompileArea) {
					foreach ($this->ncaCloseTags as $tag) {
						if (preg_match('/.*' . preg_quote($tag, '/') . '$/', $txt)) {
							$this->skipRow = TRUE;
							$this->inNoCompileArea = FALSE;
							break;
						}
					}
				}
			}
		}


		$tagDetected = $txt === self::AREA_TAG || $txt === '/' . self::AREA_TAG;
		// Set and return
		$this->noCompileAreaClosed = $areaClosed;
		return $tagDetected;
	}

	/**
	 * @param string $txt
	 * @return array
	 */
	private function processLn($txt)
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
			$txt = preg_replace('/\<(?:\?(?:php)?)+ +.*?\?\>/', 'PHP_RESERVED_' . count($preservedPhp), $txt, 1);
			$preservedPhp[] = $matches;
		}

		// Replace -*= for data-*=
		$re = '/ -([\w-]+)+=/';
		if (preg_match_all($re, $txt, $matches)) {
			foreach ($matches[1] as $match) {
				$txt = preg_replace($re, ' data-' . $match . '=', $txt, 1);
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
		if ($clsSelectors && preg_match($re, $htmlAttributes, $matches)) {
			$htmlAttributes = preg_replace($re, ' class="' . end($matches) . ' ' . $clsSelectors . '"', $htmlAttributes);
		} elseif ($clsSelectors) {
			$htmlAttributes .= ' class="' . $clsSelectors . '"';
		}

		// Get all quick attributes
		$re = '/ ([\d]+)?\$(?:([^$;"]+);|(\S+)+)/';
		$qkAttributes = [];
		if (preg_match_all($re, $txt, $matches, PREG_SET_ORDER)) {
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
		$txt = $this->getLnTxt($txt, TRUE) . $txtFromTag2End;

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
		$elementSettings = $this->Elements->findElement($element, TRUE);
		$openTag = '<' . $element;
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
				$openTag .= $newAttr ? ' ' . $newAttr : '';
			}
		}
		// Add html and boolean attributes
		$openTag .= $attributes['htmlAttributes'] . $attributes['booleanAttributes'];

		// Close the open tag, add close tags if needed
		$selfClosing = $elementSettings['paired'] || !$this->closeSelfClosingTags ? '' : ' /';
		$openTag .= $selfClosing . '>';
		$preservedPhp = $attributes['preservedPhp'];
		$phpMark = 'PHP_RESERVED_';
		if ($preservedPhp) {
			foreach ($preservedPhp as $key => $code) {
				if (preg_match("/" . $phpMark . $key . "/", $openTag)) {
					$openTag = str_replace($phpMark . $key, $code[0], $openTag);
					$preservedPhp[$key] = FALSE;
				}
			}
		}
		$this->addCloseTags($lvl);
		$type = $elementSettings['paired'] ? 'openTag' : 'inlineTag';
		$this->addToQueue($type, $openTag, $lvl);
		// If the tag is paired add its close tag to the storage
		if ($elementSettings['paired']) {
			$txt = $attributes['txt'];
			if ($txt) {
				if ($preservedPhp) {
					foreach ($preservedPhp as $key => $code) {
						if ($code[0] && preg_match("/" . $phpMark . $key . "/", $txt))
							$txt = str_replace($phpMark . $key, $code[0], $txt);
					}
				}
				$this->addToQueue('text', $txt, $lvl);
			}
			$closeTag = '</' . $element . '>';
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	/** @param int $lvl */
	private function addCloseTags($lvl)
	{
		$lastTag = $length = count($this->closeTags);
		for ($i = $length - 1; $i >= 0; $i--) {
			if ($lvl <= $this->closeTags[$i][0]) {
				$this->addToQueue('closeTag', $this->closeTags[$i][1], $this->closeTags[$i][0]);
				$lastTag = $i;
			} else {
				break;
			}
		}
		array_splice($this->closeTags, $lastTag);
	}

	/**
	 * @param string $type
	 * @param string $content
	 * @param int $lvl
	 */
	private function addToQueue($type, $content, $lvl)
	{
		$content = [
			'type' => $type,
			'content' => $content,
			'lvl' => $lvl,
			'processed' => FALSE,
			'formatting' => !$this->inNoCompileArea && !$this->skipRow
		];

		$this->contentQueue[] = $content;
	}

	/**
	 * @return string
	 */
	private function composeContent()
	{
		$composedContent = '';
		$prevOutputType = $prevOutputLvl = $lastProcessed = NULL;
		$processedArraysKeys = [];
		$lnBreak = $this->compressCode ? '' : "\n";

		foreach ($this->contentQueue as $contentKey => $contentArr) {
			if (in_array($contentKey, $processedArraysKeys)) continue;

			$content = $contentArr['content'];

			if (!$this->compressCode) {
				$type = $contentArr['type'];
				$lvl = $contentArr['lvl'];
				$formatting = $contentArr['formatting'];
				$prevAllowedFormatting = isset($this->contentQueue[$contentKey - 1]) ? $this->contentQueue[$contentKey - 1]['formatting'] : TRUE;

				$lvl += $this->structureHtmlSkeleton && $lvl > 0 ? -1 : 0;

				if ($formatting) {
					if ($type === 'text' && $this->compressText) {
						$nextKey = $contentKey;
						while (TRUE) {
							$nextKey++;

							if (!isset($this->contentQueue[$nextKey]) || $this->contentQueue[$nextKey]['type'] !== 'text')
								break;

							$content .= $this->contentQueue[$nextKey]['content'];
							$processedArraysKeys[] = $nextKey;

						}
					}

					if ($type === 'text') {
						if (!$this->compressText && $lvl === $prevOutputLvl && $prevOutputType === 'openTag') {
							$lvl++;
						} elseif ($this->compressText && ($prevOutputType === 'text' && $prevAllowedFormatting || ($prevOutputType === 'openTag' || $prevOutputType === 'inlineTag'))) {
							$lvl = $prevOutputLvl + 1;
						}
					}
				}

				$method = $this->outputIndentation === 'spaces' ? '    ' : "\t";
				$indentation = str_repeat($method, $lvl);
				$nextOutputKey = isset($nextKey) ? $nextKey : $contentKey + 1;
				$nextOutputType = isset($this->contentQueue[$nextOutputKey]) ? $this->contentQueue[$nextOutputKey]['type'] : '';

				// WTF condition for output formatting
				if ($prevOutputType !== NULL && (
						$type === 'closeTag' && ($prevOutputType === 'closeTag' || !$prevAllowedFormatting || !$this->compressText && $prevOutputType === 'text' || $prevOutputType === 'text' && $lastProcessed['type'] !== 'openTag')
						|| ($type === 'openTag' || $type === 'inlineTag') && ($prevOutputType === 'closeTag' || $prevOutputType === 'text' || ($prevOutputType === 'openTag' || $prevOutputType === 'inlineTag'))
						|| $type === 'text' && (!$this->compressText || $this->compressText && (!$formatting || !$prevAllowedFormatting || $prevOutputType === 'closeTag' || $prevOutputType === 'inlineTag' || $prevOutputType === 'openTag' && ($nextOutputType === 'openTag' || $nextOutputType === 'macro')))
						|| $type === 'macro' || $prevOutputType === 'macro')
				)
					$composedContent .= $lnBreak . $indentation;

				$lastProcessedKey = $contentKey > 0 ? $contentKey - 1 : 0;
				$lastProcessed = $this->contentQueue[$lastProcessedKey];
				$prevOutputLvl = $lvl;
				$prevOutputType = $type;
				$processedArraysKeys[] = $contentKey;

			}
			$composedContent .= $content;
		}
		return $composedContent;
	}
}
