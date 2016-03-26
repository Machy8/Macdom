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

class Compiler {

	const
			/**
			 * The skip are tag
			 * @const string
			 */
			AREA_TAG = 'SKIP';

	/**
	 * 1 = only spaces
	 * 2 = only tabulators
	 * 3 = combined - default
	 * @var int
	 */
	private $indentMethod;

	/** @var string */
	private $lnBreak;

	/** @var int */
	private $spacesPerIndent;

	/** @var Elements\Elements */
	private $Elements;

	/** @var Macros\Macros */
	private $Macros;

	/** @var Replicator\Replicator */
	private $Replicator;

	/** @var string */
	private $codeStorage;

	/** @var array */
	private $closeTags = [];

	/** @var bool */
	private $inNoCompileArea = FALSE;

	/** @var bool */
	private $noCompileAreaClosed = NULL;

	/**
	 * @param Elements\Elements $Elements
	 * @param Macros\Macros $Macros
	 * @param Replicator\Replicator $Replicator
	 * @param int $indentMethod
	 * @param int $spacesPerIndent
	 * @param bool $compressCode
	 */
	public function __construct($Elements, $Macros, $Replicator, $indentMethod, $spacesPerIndent, $compressCode) {
		$this->indentMethod = $indentMethod ? : 3;
		$this->spacesPerIndent = $spacesPerIndent ? : 4;
		$this->lnBreak = $compressCode ? '' : "\n";
		$this->Elements = $Elements;
		$this->Macros = $Macros;
		$this->Replicator = $Replicator;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function compile($content) {
		$lns = preg_split('/\n/', $content);

		foreach ($lns as $key => $ln) {
			$lvl = $this->getLnLvl($ln);
			$txt = $this->getLnTxt($ln);
			$element = $this->getElement($txt);
			$noCompileAreaTag = $this->detectNoCompileArea($element);
			if (!$this->inNoCompileArea && !$noCompileAreaTag && $this->noCompileAreaClosed === NULL && !$this->Elements->findElement($element, FALSE) && strlen(ltrim($txt)) && !preg_match('/^[<*]+/', trim($txt)) && $txt) {
				$replicatorResult = $this->Replicator->detect($lvl, $element, $txt);
				if ($replicatorResult['replicate']) {
					$txt = $this->getLnTxt($replicatorResult['line']);
					$element = $this->getElement($txt);
				}
				if ($replicatorResult['clearLine']) {
					$txt = NULL;
					$element = FALSE;
				}
			}
			if ($this->Elements->findElement($element, FALSE) && !$this->inNoCompileArea) {
				$clearedText = preg_replace('/' . $element . '/', '', $txt, 1);
				$attributes = $this->getLnAttributes($clearedText);
				$this->addOpenTag($element, $lvl, $attributes);
			} else {
				if ($txt) {
					$this->addCloseTags($lvl);
					if (!$this->inNoCompileArea) {
						if (!$noCompileAreaTag) {
							$macro = $this->Macros->replace($element, $txt);
							$macroExists = $macro['exists'];
							$this->codeStorage .= $macroExists ? $macro['replacement'] . $this->lnBreak : $txt . $this->lnBreak;
						}
					} elseif ($this->inNoCompileArea) {
						if (!$noCompileAreaTag)
							$this->codeStorage .= $txt . "\n";
					}
				}
			}
		}
		$this->addCloseTags(0);
		return $this->codeStorage;
	}

	/**
	 * @param string $txt
	 * @return string
	 */
	private function getElement($txt) {
		$element = explode(' ', trim($txt));
		return $element[0];
	}

	/**
	 *  HOW LEVELS WORKS
	 *
	 * method 1 = spaces
	 * 	- is better to set the number of the constant SPACES_PER_INDENT on the number
	 * 	  you have in your editor setted as "spaces per indent"
	 * method 2 = tabulators
	 * method 3 = combined
	 * 	- tabulators are always twice bigger
	 * 	- Example:
	 * 	  - spaces per indent = 4 => tab size = 8
	 * 	  - spaces per indent = 8 => tab size = 16
	 * 	  - etc...
	 * @param string $ln
	 * @return int
	 */
	private function getLnLvl($ln) {
		$method = $this->indentMethod;
		preg_match('/^\s+/', $ln, $matches);
		$whites = implode('', $matches);

		// Only for spaces and combined method	
		$spaces = $method === 1 || $method === 3 ? preg_match_all('/ {' . $this->spacesPerIndent . '}/', $whites) : 0;

		// Only for tabulators and combined method
		$tabulators = $method === 2 || $method === 3 ? preg_match_all('/\t/', $whites) : 0;

		if ($method === 3)
			$tabulators *= 2;

		return ($spaces + $tabulators);
	}

	/**
	 * @param string $ln
	 * @return string
	 */
	private function getLnTxt($ln) {
		return ltrim($ln);
	}

	/**
	 *  @param string $txt
	 *  @return array
	 */
	private function getLnAttributes($txt) {

		// Store the text from the first tag to the end of the line
		$re = '/\<.*$/';
		$txtFromTag2End = '';
		$matchedText = preg_match($re, $txt, $match);
		if ($matchedText) {
			$txt = preg_replace($re, '', $txt);
			$txtFromTag2End .= $match[0];
		}

		// Replace n$*; for n:href=""
		$re = '/ n\$(.+);/';
		$nHref = preg_match($re, $txt, $matches);
		if ($nHref) {
			$value = $matches[1] ? : $matches[2];
			$newHref = ' n:href="' . $value . '"';
			$txt = preg_replace($re, $newHref, $txt);
		}

		// Get all html attributes
		$re = '/ [\w:-]+="[^"]*"| [\w:-]+=\S+/';
		$htmlAttributes = preg_match_all($re, $txt, $matches);
		if ($htmlAttributes) {
			$txt = preg_replace($re, '', $txt);
			$htmlAttributes = implode('', $matches[0]);
		}

		// Get the id selector
		$re = '/ #(\S+)/';
		$idSelector = preg_match($re, $txt, $matches);
		if ($idSelector) {
			$txt = preg_replace($re, '', $txt);
			$idSelector = $matches[1];
		}

		// Get all class selectors
		$re = '/ \.(\S+)/';
		$clsSelectors = preg_match_all($re, $txt, $matches);
		if ($clsSelectors) {
			$txt = preg_replace($re, '', $txt);
			$clsSelectors = implode(' ', $matches[1]);
		}

		// Get all quick attributes
		$re = '/ ([\d]+)?\$(?:([^$;"]+);|(\S+)+)/';
		$matched = preg_match_all($re, $txt, $matches, PREG_SET_ORDER);
		$qkAttributes = [];
		if ($matched) {
			$txt = preg_replace($re, '', $txt);
			foreach ($matches as $value) {
				$paramVal = end($value);
				if (!empty($paramVal) && strtolower($paramVal) !== 'null') {

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
		$getTxt = $this->getLnTxt($txt);
		$txt = $getTxt . $txtFromTag2End;

		// Split the txt to an array in oder to get the boolean attributes
		$txt2array = explode(' ', $txt);
		$booleanAttributes = '';
		foreach ($txt2array as $key => $attribute) {
			if ($this->Elements->isBoolean($attribute)) {
				$txt = str_replace($attribute, '', $txt);
				$booleanAttributes .= ' ' . $attribute;
			} else {
				break;
			}
		}

		// Synchronize class selectors
		$re = '/ class="([^"]+)+"| class=([\S]+)+/';
		$htmlClsSelector = preg_match($re, $htmlAttributes, $matches);
		if ($clsSelectors && $htmlClsSelector) {
			$htmlAttributes = preg_replace($re, ' class="' . $matches[1] . ' ' . $clsSelectors . '"', $htmlAttributes);
			$clsSelectors = NULL;
		}

		// Synchronize id selectors
		if ($idSelector && preg_match('/ id="[^"]+"| id=[\S]+/', $htmlAttributes))
			$idSelector = NULL;

		// Return all attributes
		return [
			'id' => $idSelector,
			'classes' => $clsSelectors,
			'qkAttributes' => $qkAttributes,
			'htmlAttributes' => $htmlAttributes,
			'booleanAttributes' => $booleanAttributes,
			'txt' => $txt
		];
	}

	/**
	 * @param string $element
	 * @param int $lvl
	 * @param array $attributes
	 */
	private function addOpenTag($element, $lvl, $attributes) {
		$elementSettings = $this->Elements->findElement($element, TRUE);
		$openTag = '<' . $element;
		if ($elementSettings['qkAttributes'] && $attributes['qkAttributes']) {
			$usedKeys = [];
			$withoutKey = 0;
			foreach ($attributes['qkAttributes'] as $key => $attribute) {
				$unsetKey = NULL;
				$newAttr = NULL;
				if ($attribute['key']) {
					$paramKey = $attribute['key'] - 1;
					if (isset($elementSettings['qkAttributes'][$paramKey])) {
						$newAttr = $elementSettings['qkAttributes'][$paramKey] . '="' . $attribute['value'] . '"';
						$usedKeys[] = $paramKey;
					}
				} elseif (!in_array($withoutKey, $usedKeys)) {
					$newAttr = $elementSettings['qkAttributes'][$withoutKey] . '="' . $attribute['value'] . '"';
					$withoutKey ++;
				}
				if ($newAttr)
					$openTag .= ' ' . $newAttr;
			}
		}

		// Add the id attribute
		if ($attributes['id'] && strtolower($attributes['id']) !== 'null')
			$openTag .= ' id="' . $attributes['id'] . '"';

		// Add classes
		if ($attributes['classes'])
			$openTag .= ' class="' . $attributes['classes'] . '"';

		// Add html attributes
		if ($attributes['htmlAttributes'])
			$openTag .= $attributes['htmlAttributes'];

		// Add boolean attributes
		if ($attributes['booleanAttributes'])
			$openTag .= $attributes['booleanAttributes'];

		// Close the open tag, add close tags if needed
		$selfClosing = $elementSettings['paired'] ? '' : ' /';
		$openTag .= $selfClosing . '>' . $this->lnBreak;
		$this->addCloseTags($lvl);
		$this->codeStorage .= $openTag;

		// If the tag is paired add its close tag to the storage
		if ($elementSettings['paired']) {
			if ($attributes['txt'])
				$this->codeStorage .= $attributes['txt'];
			$closeTag = '</' . $element . '>';
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	/** @param int $lvl */
	private function addCloseTags($lvl) {
		$length = count($this->closeTags);
		$lastTag = $length;
		if ($length > 0) {
			for ($i = $length - 1; $i >= 0; $i--) {
				if ($lvl <= $this->closeTags[$i][0]) {
					$this->codeStorage .= $this->lnBreak . $this->closeTags[$i][1] . $this->lnBreak;
					$lastTag = $i;
				} else {
					break;
				}
			}
			array_splice($this->closeTags, $lastTag);
		}
	}

	/**
	 * @param string $element
	 * @return bool
	 */
	private function detectNoCompileArea($element) {
		$tagDetected = FALSE;
		$areaClosed = $this->inNoCompileArea ? FALSE : NULL;

		// For skip tag
		$closeTag = '/' . self::AREA_TAG;
		if ($element === self::AREA_TAG) {
			$tagDetected = TRUE;
			$this->inNoCompileArea = TRUE;
		} elseif ($element === $closeTag) {
			$tagDetected = TRUE;
			$this->inNoCompileArea = FALSE;
		}

		// For style tag
		$tag = 'style';
		$open = '<' . $tag;
		$close = '</' . $tag . '>';
		if ($element === $open . '>' || $element === $open) {
			$this->inNoCompileArea = TRUE;
		} elseif ($element === $close) {
			$this->inNoCompileArea = FALSE;
		}

		// For script tag
		$tag = 'script';
		$open = '<' . $tag;
		$close = '</' . $tag . '>';
		if ($element === $open . '>' || $element === $open) {
			$this->inNoCompileArea = TRUE;
		} elseif ($element === $close) {
			$this->inNoCompileArea = FALSE;
		}

		// For php
		$open = '<?';
		$close = '?>';
		if ($element === $open . 'php' || $element === $open) {
			$this->inNoCompileArea = TRUE;
		} elseif ($element === $close) {
			$this->inNoCompileArea = FALSE;
		}

		// Set and return
		$this->noCompileAreaClosed = $areaClosed;
		return $tagDetected;
	}
}
