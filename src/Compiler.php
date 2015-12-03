<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom;

class Compiler
{
	const
		/**
		 * The skip are tag
		 * @const string
		 */
		AREA_TAG = "SKIP",

		/** @const string connectors reg exp*/
		STR_CONNECTORS = '\+=<>\|\-_\/\\&';

	/**
	* 1 = only spaces
	* 2 = only tabulators
	* 3 = combined - Default
	* @var integer
	*/
	private $indentMethod = 3;

	/**
	*  For 1. and 3. method
	*  @var integer
	*/
	private $spacesPerIndent = 4;

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
	 * @param Macros $Macros
	 * @param Elements $Elements
	 */
	public function __construct ($Elements, $Macros, $Replicator, $indentMethod = NULL, $spacesPerIndent = NULL)
	{
		if($indentMethod !== NULL) {
			$this->indentMethod = $indentMethod;
		}

		if($spacesPerIndent !== NULL) {
			$this->spacesPerIndent = $spacesPerIndent;
		}

		$this->Elements = $Elements;
		$this->Macros = $Macros;
		$this->Replicator = $Replicator;
	}

	/**
	 * @param string $content
	 * @return string $this->codeStorage
	 */
	public function compile ($content)
	{
		$lns = preg_split("/\n/", $content);

		foreach ($lns as $key => $value) {
			$ln = $value;
			$lvl = $this->getLnLvl($ln);
			$txt = $this->getLnTxt($ln);

			$element = $this->getElement($txt);

			$noCompileAreaTag = $this->detectNoCompileArea($element);

			if ($this->inNoCompileArea === FALSE && $noCompileAreaTag === FALSE && $this->noCompileAreaClosed === NULL && $this->Elements->findElement($element, "exists") === FALSE && strlen(ltrim($txt)) >= 1 && preg_match("/^[<*]+/", trim($txt)) === 0 && $txt !== NULL) {
				$replicatorResult = $this->Replicator->detect($lvl, $element, $txt);

				if ($replicatorResult['replicate'] === TRUE) {
					$txt = $this->getLnTxt($replicatorResult['line']);
					$element = $this->getElement($txt);
				}

				if ($replicatorResult['clearLine'] === TRUE) {
					$txt = NULL;
					$element = FALSE;
				}
			}

			if ($this->Elements->findElement($element, "exists") === TRUE && $this->inNoCompileArea === FALSE) {
				$clearedText = preg_replace('/'.$element.'/', '', $txt, 1);
				$attributes = $this->getLnAttributes($clearedText);
				$this->addOpenTag($element, $lvl, $attributes);
			}
			else{
				if ($txt !== NULL)
				{
					$this->addCloseTags($lvl);

					if ($this->inNoCompileArea === FALSE) {

						if ($noCompileAreaTag === FALSE) {
							$macro = $this->Macros->replace($element, $txt);
							$macroExists = $macro['exists'];

							if ($macroExists === FALSE) {
								$match1 = preg_match('/[^'.self::STR_CONNECTORS.']+$/',$this->codeStorage);
								$match2 = preg_match('/^[^'.self::STR_CONNECTORS.']+/', $txt);

								$spacePrefix =  $match1 === 1 && $match2 === 1 ? " " : "";

								$this->codeStorage .= $spacePrefix.$txt;
							}
							elseif ($macroExists === TRUE) {
								$this->codeStorage .= $macro['replacement'];
							}
						}
					}
					elseif ($this->inNoCompileArea === TRUE) {

						if ($noCompileAreaTag === FALSE) {
							$this->codeStorage .= $txt."\n";
						}
					}
				}
			}
		}

		$this->addCloseTags(0);

		return $this->codeStorage;
	}

	/**
	 * @param string $txt
	 * @return string $element
	 */
	private function getElement ($txt)
	{
		$element = explode(" ", trim($txt));

		return $element[0];
	}

	/**
	 *  HOW LEVELS WORKS
	 *
	 * method 1 = spaces
	 *	- is better to set the number of the constant SPACES_PER_INDENT on the number
	 *	  you have in your editor setted as "spaces per indent"
	 * method 2 = tabulators
	 * method 3 = combined
	 *	- is better to set up the tab size twice bigger then spaces have
	 *	- Example:
	 *	  - spaces per indent = 4 => tab size = 8
	 *	  - spaces per indent = 8 => tab size = 16
	 *	  - etc...
	 * @param string $ln
	 * @return integer $lvl
	 */
	private function getLnLvl ($ln)
	{
		$method = $this->indentMethod;
		$spacesRe = "/ {".$this->spacesPerIndent."}/";
		$spaces = 0;
		$tabulators = 0;

		preg_match_all("/^\s+/", $ln, $matches);
		$whites = implode("", $matches[0]);

		// Only for spaces and combined method
		If($method === 1 || $method === 3) {
			$spaces = preg_match_all($spacesRe, $whites);
		}

		// Only for tabulators and combined method
		if ($method === 2 || $method === 3) {
			$tabulators = preg_match_all("/\t/", $whites);

			if ($method === 3) {
				$rise = $tabulators * 2;
				$tabulators = $rise;
			}
		}

		$lvl = $spaces + $tabulators;

		return $lvl;
	}

	/**
	 * @param string $ln
	 * @return string
	 */
	private function getLnTxt ($ln)
	{
		$clearLn = ltrim($ln);

		$txt = strlen($clearLn) >= 1 ? $clearLn : NULL;

		return $txt;
	}

	/**
	 *  @param string $txt
	 *  @return array
	 */
	private function getLnAttributes ($txt)
	{
		// Replace n$*; for n:href=""
		$re = '/ n\$(.+);/';
		$nHref = preg_match($re, $txt, $matches);

		if ($nHref === 1) {

			$value = empty($matches[1]) ?  $matches[2] : $matches[1];

			$newHref = ' n:href="'.$value.'"';
			$replaced = preg_replace($re, $newHref, $txt);
			$txt = $replaced;
		}

		// Get all html attributes
		$re = '/ [\w:-]+="[^"]*"| [\w:-]+=\S+/';
		$htmlAttributes = preg_match_all($re, $txt, $matches);

		if ($htmlAttributes !== 0 && $htmlAttributes !== FALSE) {
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$htmlAttributes = implode(" ", $matches[0]);
		}
		else{
			$htmlAttributes = NULL;
		}

		// Get the id selector
		$re = '/ #(\S+)/';
		$idSelector = preg_match($re, $txt, $matches);

		if ($idSelector !== 0 && $idSelector !== FALSE) {
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$idSelector = $matches[1];
		}
		else{
			$idSelector = NULL;
		}

		// Get all class selectors
		$re = "/ \.(\S+)/";
		$clsSelectors = preg_match_all($re, $txt, $matches);

		if ($clsSelectors !== 0 && $clsSelectors !== FALSE) {
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$clsSelectors = implode(" ", $matches[1]);
		}
		else{
			$clsSelectors = NULL;
		}

		// Get all quick attributes
		$re = '/ ([\d]+)?\$(?:([^$;"]+);|(\S+)+)/';
		$qkAttributes = preg_match_all($re, $txt, $matches, PREG_SET_ORDER);
		$matches2selectors = [];

		if ($qkAttributes !== 0 && $qkAttributes !== FALSE) {
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;

			foreach ($matches as $value) {
				$paramVal = end($value);

				if (!empty($paramVal) && strtolower($paramVal) !== 'null') {
					$selector = [];

					// If quick attribute is without index
					$paramKey = is_numeric($value[1]) ? $value[1] : NULL;

					$matches2selectors[] = [
						"key" => $paramKey,
						"value" => $paramVal
					];
				}
			}

			$qkAttributes = $matches2selectors;
		}
		else{
			$qkAttributes = NULL;
		}

		// Get the text
		$getTxt = $this->getLnTxt($txt);
		$txt = $getTxt;

		// Split the txt to an array in oder to get the boolean attributes
		$txt2array = explode(" ", $txt);
		$matches2selectors = "";

		// Get boolean attributes
		foreach ($txt2array as $key => $attribute) {

			if ($this->Elements->isBoolean($attribute) === TRUE) {
				$remove = str_replace($attribute, '', $txt);
				$txt = $remove;
				$matches2selectors .= $attribute.' ';
			}
			else{
				break;
			}
		}

		$booleanAttributes = strlen($matches2selectors) > 0 ? $matches2selectors : NULL;

		// Synchronize class selectors
		$re = '/ class="([^"]+)+"| class=([\S]+)+/';
		$htmlClsSelector = preg_match($re, $htmlAttributes, $matches);

		if ($htmlClsSelector === 1 && $clsSelectors !== NULL)
		{
			$replacement = preg_replace($re, ' class="'.$matches[1].' '.$clsSelectors.'"', $htmlAttributes, 1);
			$htmlAttributes = $replacement;

			$clsSelectors = NULL;
		}

		// Synchronize id selectors
		if ($idSelector !== NULL && preg_match('/ id="[^"]+"| id=[\S]+/', $htmlAttributes) === 1)
		{
			$idSelector = NULL;
		}

		// Return all attributes
		return
		[
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
	private function addOpenTag ($element, $lvl, $attributes)
	{
		$elementSettings = $this->Elements->findElement($element, "settings");
		$match = preg_match_all('/[^'.self::STR_CONNECTORS.']+$/',$this->codeStorage);
		$spacePrefix = $match === 1 ? " " : "";

		$openTag = $spacePrefix.'<'.$element;

		if ($elementSettings['qkAttributes'] !== NULL && $attributes['qkAttributes'] !== NULL) {

			$usedKeys = [];
			$withoutKey = 0;

			foreach ($attributes['qkAttributes'] as $key => $attribute) {
				$unsetKey = NULL;
				$newAttr = NULL;
				if ($attribute["key"] !== NULL) {
					$paramKey = $attribute['key']-1;

					if (isset($elementSettings['qkAttributes'][$paramKey])) {
						$newAttr = $elementSettings['qkAttributes'][$paramKey].'="'.$attribute['value'].'"';
						$usedKeys[] = $paramKey;
					}
				}
				elseif (!in_array($withoutKey, $usedKeys)) {
						$newAttr = $elementSettings['qkAttributes'][$withoutKey].'="'.$attribute['value'].'"';
						$withoutKey ++;
					}

				if ($newAttr !== NULL) {
					$openTag .= ' '.$newAttr;
				}
			}

		}

		// Add the id attribute
		if ($attributes['id'] !== NULL) {
			if (strtolower($attributes['id']) !== "null") {
				$openTag .= ' id="'.$attributes['id'].'"';
			}
		}

		// Add classes
		if ($attributes['classes'] !== NULL) {
			$openTag .= ' class="'.$attributes['classes'].'"';
		}

		// Add html attributes
		if ($attributes['htmlAttributes'] !== NULL) {
			$openTag .= ' '.$attributes['htmlAttributes'];
		}

		// Add boolean attributes
		if ($attributes['booleanAttributes'] !== NULL) {
			$openTag .= ' '.$attributes['booleanAttributes'];
		}

		// Close the open tag, add close tags if needed
		$openTag .= '>';
		$this->addCloseTags($lvl);
		$this->codeStorage .= $openTag;

		// Add txt
		if ($attributes['txt'] !== NULL) {
			$this->codeStorage .= $attributes['txt'];
		}

		// If the tag is paired add its close tag to the storage
		if ($elementSettings['paired'] === TRUE) {
			$closeTag = '</'.$element.'>';
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	/** @param int $lvl */
	private function addCloseTags ($lvl)
	{
		$length = count($this->closeTags);
		$lastTag = $length;

		if ($length > 0) {

			for ($i = $length-1; $i >= 0; $i--) {

				if ($lvl <= $this->closeTags[$i][0]) {
					$this->codeStorage .= $this->closeTags[$i][1];
					$lastTag = $i;
				}
				else{
					break;
				}
			}
			array_splice($this->closeTags, $lastTag);
		}
	}
	/**
	 * @param type $element
	 * @return boolean
	 */
	private function detectNoCompileArea ($element)
	{
		$tagDetected = FALSE;

		$areaClosed = $this->inNoCompileArea ? FALSE : NULL;

		// For skip tag
		$closeTag = '/'.self::AREA_TAG;

		if ($element === self::AREA_TAG) {
			$tagDetected = TRUE;
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $closeTag) {
			$tagDetected = TRUE;
			$this->inNoCompileArea = FALSE;
		}

		// For style tag
		$tag = 'style';
		$open = '<'.$tag;
		$close = '</'.$tag.'>';

		if ($element === $open.'>' || $element === $open) {
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close) {
			$this->inNoCompileArea = FALSE;
		}

		// For script tag
		$tag = 'script';
		$open = '<'.$tag;
		$close = '</'.$tag.'>';

		if ($element === $open.'>' || $element === $open) {
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close) {
			$this->inNoCompileArea = FALSE;
		}

		// For php
		$open = "<?";
		$close = "?>";

		if ($element === $open."php" || $element === $open)
		{
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close)
		{
			$this->inNoCompileArea = FALSE;
		}

		// User defined or other tags

		// Set and return
		$this->noCompileAreaClosed = $areaClosed;

		return $tagDetected;
	}
}
