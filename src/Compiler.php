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

	/**
	 * @const string
	 */
	const AREA_TAG = 'SKIP';

	/**
	 * @var Elements
	 */
	private $elements;

	/**
	 * @var Macros
	 */
	private $macros;

	/**
	 * @var Replicator
	 */
	private $replicator;

	/**
	 * @var Setup\Setup
	 */
	private $setup;

	/**
	 * @var array
	 */
	private $closeTags = [];

	/**
	 * @var bool
	 */
	private $inNoCompileArea;

	/**
	 * @var array
	 */
	private $ncaCloseTags;

	/**
	 * @var array
	 */
	private $ncaOpenTags;

	/**
	 * @var array
	 */
	private $ncaSkipElements;

	/**
	 * @var array
	 */
	private $ncaRegExpInlineTags;

	/**
	 * @var array
	 */
	private $ncaRegExpOpenTags;

	/**
	 * @var bool
	 */
	private $noCompileAreaClosed;

	/**
	 * @var string
	 */
	private $outputStorage = '';

	/**
	 * @var array
	 */
	private $outputQueue = [];

	/**
	 * @var array
	 */
	private $prevOutput;

	/**
	 * @var string
	 */
	private $prev2OutputType;

	/**
	 * @var bool
	 */
	private $skipRow;

	/**
	 * @var int
	 */
	private $skippedElementLvl;


	/**
	 * Compiler constructor.
	 * @param Setup\Setup $setup
	 */
	public function __construct($setup)
	{
		if ($setup->preferXhtml) $setup->closeSelfClosingTags = $setup->booleansWithValue = TRUE;

		$this->ncaSkipElements = array_filter(array_merge(['script', 'style', 'textarea', 'code'], explode(' ', $setup->skipElements)));
		$this->ncaOpenTags = ['<?php', '<?', self::AREA_TAG];
		$this->ncaCloseTags = ['?>', '/' . self::AREA_TAG];
		$inlineOpenTags = join("|", $this->ncaSkipElements);
		$this->ncaRegExpInlineTags = ['\<\?(?:php)? .*\?\>', '\<(?:' . $inlineOpenTags . ') *[^>]*\>.*\<\/(?:' . $inlineOpenTags . ')\>'];
		$this->ncaRegExpOpenTags = ['\<(?:' . $inlineOpenTags . ') *[^\>]*\>', '\<\?(?:php)?'];

		foreach ($this->ncaSkipElements as $element) {
			$this->ncaCloseTags[] = '</' . $element . '>';
			$this->ncaOpenTags[] = '<' . $element . '>';
		}

		$this->elements = new Elements;
		$this->macros = new Macros;
		$this->repliactor = new Replicator;
		$this->setup = $setup;

		$this->elements->addQkAttributes($setup->addQkAttributes);
		$this->elements->addElements($setup->addElements);
		$this->elements->addBooleanAttributes($setup->addBooleanAttributes);
		$this->elements->removeBooleanAttributes($setup->removeBooleanAtributes);
		$this->elements->removeElements($setup->removeElements);
		$this->elements->changeQkAttributes($setup->changeQkAttributes);

		$this->macros->addCustomMacros($setup->addMacros);
		$this->macros->removeMacros($setup->removeMacros);
	}


	/**
	 * @param string $content
	 * @return string
	 */
	public function compile($content)
	{
		if (!$content) return '';

		foreach (preg_split('/\n/', $content) as $ln) {
			$lvl = $this->getLnLvl($ln);
			$element = $this->getElement($ln);
			$noCompileAreaTag = $this->detectNoCompileArea($ln, $element, $lvl);
			$compilationAllowed = !$this->inNoCompileArea && !$this->skipRow && $this->noCompileAreaClosed;

			if ($noCompileAreaTag || $compilationAllowed && !$ln) continue;

			if ($this->setup->structureHtmlSkeleton) {
				$lvl = in_array($element, ['head', 'body']) ? 1 : $lvl + 1;

				if ($element === 'html') $lvl = 0;
			}

			if ($compilationAllowed && trim($ln) && !$this->elements->findElement($element) && !$this->macros->findMacro($element)) {
				$ln = preg_replace('/\|$/', '', $ln, 1);
				$replicatorResult = $this->repliactor->detect($lvl, $element, $ln);

				if ($replicatorResult['toReplicate']) {
					$ln = $replicatorResult['toReplicate'];
					$element = $this->getElement($ln);
				}

				if ($replicatorResult['clearLn']) $ln = $element = NULL;
			}

			$processElement = $compilationAllowed && $this->elements->findElement($element);
			$txt = $this->getLnTxt($ln, $compilationAllowed, $processElement);

			if ($processElement) {
				$attributes = $this->processLn($txt);
				$this->addOpenTag($element, $lvl, $attributes);

			} elseif ($txt) {
				$this->addCloseTags($lvl);
				$isJsCssLink = $this->getElement($txt);

				if ($compilationAllowed && preg_match('/\.((?:css|js))$/', $isJsCssLink, $isJsCss)) {
					if ($isJsCss[1] === "css") {
						$element = "link";
						$attr = "href";
						$type = 'rel="stylesheet" type="text/css"';

					} else {
						$element = "script";
						$attr = "src";
						$type = 'type="text/javascript"';
					}

					$txt = $this->getLnTxt($txt, TRUE, TRUE);
					$txt = ' ' . $type . ' ' . $attr . '="' . $isJsCssLink . '"' . $txt;
					$attributes = $this->processLn($txt);
					$attributes['txt'] = NULL;
					$this->addOpenTag($element, $lvl, $attributes);

				} else {
					$macro = $compilationAllowed && $this->macros->findMacro($element);
					$content = $macro ? $this->macros->replace($element, $txt) : $txt;
					$type = $macro ? 'macro' : 'text';
					$this->addToQueue($type, $content, $lvl);
				}
			}
		}

		$this->addCloseTags();
		$this->composeContent();

		if($this->setup->blankLine && !preg_match('/\n+$/', $this->outputStorage)) $this->outputStorage .= "\n";

		return $this->outputStorage;
	}


	/**
	 * @param string $ln
	 * @return int
	 */
	private function getLnLvl($ln)
	{
		$method = $this->setup->indentMethod;
		preg_match('/^\s+/', $ln, $matches);
		$whites = $matches ? $matches[0] : 0;

		// Only for spaces and combined method
		$spaces = $method === 'spaces' || $method === 'combined' ? preg_match_all('/ {' . $this->setup->spacesPerIndent . '}/', $whites) : 0;

		// Only for tabulators and combined method
		$tabulators = $method === 'tabs' || $method === 'combined' ? preg_match_all('/\t/', $whites) : 0;

		if ($method === 'combined') $tabulators *= 2;

		return $spaces + $tabulators;
	}


	/**
	 * @param string|null $ln
	 * @param bool $clean
	 * @param bool $elementExists
	 * @return string
	 */
	private function getLnTxt($ln, $clean = FALSE, $elementExists = FALSE)
	{
		$find = ['/ *' . self::AREA_TAG . '(?:-CONTENT)?/'];

		$txt = $this->setup->trim === 'left' ? ltrim($ln) : trim ($ln);

		if ($elementExists) $txt = strstr($txt, " ");
		if ($clean) $find[] = '/^\|/';

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

		if ($this->skipRow) $this->skipRow = $this->inNoCompileArea = FALSE;

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

		if (in_array($element, $this->ncaSkipElements) && ($this->skippedElementLvl === NULL || $this->skippedElementLvl !== NULL && $lvl <= $this->skippedElementLvl) || $skipContent) {
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
		if ($idSelector && !preg_match('/ id="[^"]+"|  id=\'[^\']+\'| id=[\S]+/', $htmlAttributes)) $htmlAttributes .= ' id="' . $matches[1] . '"';
		if ($idSelector) $txt = preg_replace($re, '', $txt);

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

		$txt = $this->getLnTxt($txt, TRUE) . $txtFromTag2End;

		// Split the txt to an array in oder to get the boolean attributes
		$txt2array = explode(' ', $txt);
		$booleanAttributes = '';

		foreach ($txt2array as $attribute) {
			if ($this->elements->isBoolean($attribute)) {
				$txt = str_replace($attribute, '', $txt);
				$booleanAttributes .= ' ' . $attribute;
				$booleanAttributes .= $this->setup->booleansWithValue ? '="' . $attribute . '"' : '';

			} else {
				break;
			}
		}

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
		$elementSettings = $this->elements->findElement($element, TRUE);
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
		$selfClosing = $elementSettings['paired'] || !$this->setup->closeSelfClosingTags ? '' : ' /';
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
						if ($code[0] && preg_match("/" . $phpMark . $key . "/", $txt)) $txt = str_replace($phpMark . $key, $code[0], $txt);
					}
				}
				$this->addToQueue('text', $txt, $lvl);
			}

			$closeTag = '</' . $element . '>';
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	/**
	 * @param int $lvl
	 */
	private function addCloseTags($lvl = 0)
	{
		$lastTag = $length = count($this->closeTags);

		for ($i = $length - 1; $i >= 0; $i--) {
			if ($lvl > $this->closeTags[$i][0]) break;

			$this->addToQueue('closeTag', $this->closeTags[$i][1], $this->closeTags[$i][0]);
			$lastTag = $i;
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
		$formatting = !$this->inNoCompileArea && !$this->skipRow;
		$lastKey = count($this->outputQueue) - 1;
		$contentArr = [
			'type' => $type,
			'content' => $content,
			'lvl' => $lvl,
			'formatting' => $formatting
		];

		if ($type === 'text' && $this->setup->compressText && $formatting && isset($this->outputQueue[$lastKey]) && $this->outputQueue[$lastKey]['type'] === 'text' && $this->outputQueue[$lastKey]['formatting']) {
			$this->outputQueue[$lastKey]['content'] .= $content;

			return;
		}

		$this->outputQueue[] = $contentArr;

		if ($type !== 'text') $this->composeContent();

	}


	private function composeContent()
	{
		foreach ($this->outputQueue as $contentKey => $contentArr) {
			if (!$this->setup->compressCode) {
				$lvl = $contentArr['lvl'];
				$nextOutputKey = $contentKey + 1;
				$nextOutputType = isset($this->outputQueue[$nextOutputKey]) ? $this->outputQueue[$nextOutputKey]['type'] : '';

				$trio = ['openTag', 'inlineTag', 'macro'];

				// WTF condition for output formatting
				if ($this->prevOutput['type'] !== NULL && (!$this->prevOutput['formatting'] || in_array($this->prevOutput['type'], ['closeTag', 'inlineTag', 'macro']) || in_array($contentArr['type'], $trio)
						|| $contentArr['type'] === 'closeTag' && ($this->prevOutput['type'] === 'text' && (!$this->setup->compressText || $this->prev2OutputType !== 'openTag'))
						|| $contentArr['type'] === 'text' && (!$this->setup->compressText || $this->setup->compressText && (!$contentArr['formatting'] || $this->prevOutput['type'] === 'openTag' && in_array($nextOutputType, $trio))))
				) {

					if ($contentArr['formatting'] && $contentArr['type'] === 'text') {
						if (!$this->setup->compressText && $lvl === $this->prevOutput['lvl'] && $this->prevOutput['type'] === 'openTag') {
							$lvl++;

						} elseif ($this->setup->compressText && ($this->prevOutput['type'] === 'text' && $this->prevOutput['formatting'] || $this->prevOutput['type'] === 'openTag')) {
							$lvl = $this->prevOutput['lvl'] + 1;
						}
					}

					$lvl += $this->setup->structureHtmlSkeleton && $lvl > 0 ? -1 : 0;
					$method = $this->setup->outputIndentation === 'spaces' ? '    ' : "\t";
					$indentation = str_repeat($method, $lvl);
					$lnBreak = $this->setup->compressCode ? '' : "\n";
					$this->outputStorage .= $lnBreak . $indentation;
				}
				$this->prev2OutputType = $this->prevOutput['type'];
				$this->prevOutput = $contentArr;
			}
			$this->outputStorage .= $contentArr['content'];
		}
		$this->outputQueue = [];
	}

}
