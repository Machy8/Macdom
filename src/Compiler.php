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

		/**
		 * 1 = only spaces
		 * 2 = only tabulators
		 * 3 = combined
		 * @const int
		 */
		INDENT_METHOD = 3,

		/**
		 *  For 1. and 3. method
		 *  @const integer
		 */
		SPACES_PER_INDENT = 4;

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

	/**
	 * @param Macros $Macros
	 * @param Elements $Elements
	 */
	public function __construct ($Elements, $Macros, $Replicator)
	{
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

		foreach ($lns as $key => $value){
			$ln = $value;
			$lvl = $this->getLnLvl($ln);
			$txt = $this->getLnTxt($ln);

			$element = $this->getElement($txt);

			$noCompileAreaTag = $this->detectNoCompileArea($element);

			if($this->inNoCompileArea === FALSE and $noCompileAreaTag === FALSE and $this->Elements->findElement($element, "exists") === FALSE){
				$replicatorResult = $this->Replicator->detect($lvl, $element, $txt);

				if($replicatorResult['replicate'] === TRUE){
					$txt = $this->getLnTxt($replicatorResult['line']);
					$element = $this->getElement($txt);
				}

				if($replicatorResult['clearLine'] === TRUE){
					$txt = NULL;
					$element = FALSE;
				}
			}

			if ($this->Elements->findElement($element, "exists") === TRUE and $this->inNoCompileArea === FALSE){
				$clearedText = preg_replace('/'.$element.'/', '', $txt, 1);
				$attributes = $this->getLnAttributes($clearedText);
				$this->addOpenTag($element, $lvl, $attributes);
			}
			else{

				if ($txt !== NULL and $this->inNoCompileArea === FALSE){

					if($noCompileAreaTag === FALSE){
						$macro = $this->Macros->replace($element, $txt);
						$macroExists = $macro['exists'];

						if($macroExists === FALSE){
							$this->addCloseTags($lvl);

							$spacePrefix = "";
							$match = preg_match_all("/[^_\->\/\\\\&]+$/",$this->codeStorage);
							$match2 = preg_match_all("/^[^\-_\/\\\\&]+/", $txt);

							if($match === 1 and $match2 === 1){
								$spacePrefix = " ";
							}

							$this->codeStorage .= $spacePrefix.$txt;
						}
						elseif($macroExists === TRUE){
							$this->codeStorage .= $macro['replacement'];
						}
					}
				}
				elseif($txt !== NULL and $this->inNoCompileArea === TRUE){

					if($noCompileAreaTag === FALSE){
						$this->codeStorage .= $ln;
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
		$ln2array = explode(" ", trim($txt));

		// Element is the first word on line
		$element = $ln2array[0];

		return $element;
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
		$method = self::INDENT_METHOD;
		$spacesRe = "/ {".self::SPACES_PER_INDENT."}/";
		$spaces = 0;
		$tabulators = 0;

		preg_match_all("/^\s+/", $ln, $matches);
		$whites = implode("", $matches[0]);

		// Only for spaces and combined method
		If($method === 1 or $method === 3){

			// Get the number of spaces on the line
			$spaces = preg_match_all($spacesRe, $whites);
		}

		// Only for tabulators and combined method
		if($method === 2 or $method === 3){
			$tabulators = preg_match_all("/\t/", $whites);

			if($method === 3){
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
		return ltrim($ln);
	}

	/**
	 *  @param string $txt
	 *  @return array
	 */
	private function getLnAttributes ($txt)
	{
		// Replace n$*; for n:href=""
		$re = '/ n\${1}(.+);{1}/';
		$nHref = preg_match($re, $txt, $matches);

		if ($nHref === 1){

			if (empty($matches[1])){
				$value = $matches[2];
			}
			else{
				$value = $matches[1];
			}

			$newHref = ' n:href="'.$value.'"';
			$replaced = preg_replace($re, $newHref, $txt);
			$txt = $replaced;
		}

		// Get all html attributes
		$re = '/ [\w:-]+={1}\"{1}[^"]*\"{1}| [\w:-]+={1}\S+/';
		$htmlAttributes = preg_match_all($re, $txt, $matches);
		$matches2selectors = "";

		if ($htmlAttributes !== 0 and $htmlAttributes !== FALSE){
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;

			foreach ($matches[0] as $key => $value){
				$matches2selectors .= $value.' ';
			}

			$htmlAttributes = trim($matches2selectors);
		}
		else{
			$htmlAttributes = NULL;
		}

		// Get the id selector
		$re = "/ \#{1}(\S+)/";
		$idSelector = preg_match($re, $txt, $matches);

		if ($idSelector !== 0 and $idSelector !== FALSE){
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$idSelector = $matches[1];
		}
		else{
			$idSelector = NULL;
		}

		// Get all class selectors
		$re = "/ \.{1}(\S+)/";
		$clsSelectors = preg_match_all($re, $txt, $matches);

		if ($clsSelectors !== 0 and $clsSelectors !== FALSE){
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$matches2selectors = '';

			foreach ($matches[1] as $key => $value){
				$matches2selectors .= $value.' ';
			}

			$clsSelectors = trim($matches2selectors);
		}
		else{
			$clsSelectors = NULL;
		}

		// Get all quick attributes
		$re = '/ ([\d]*)\${1}([^$;"]+);{1}| ([\d]*)\${1}(\S+)/';
		$qkAttributes = preg_match_all($re, $txt, $matches, PREG_SET_ORDER);
		$matches2selectors = [];

		if ($qkAttributes !== 0 and $qkAttributes !== FALSE){
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;

			foreach ($matches as $key => $value){
				$selector = [];
				$valLength = count($value);

				foreach($value as $keyB => $qkAttrParam){

					if (!empty($qkAttrParam) and $keyB !== 0){
						$match = preg_match("/\d/", $qkAttrParam);

						// If quick attribute is without index
						if($match === 1 and $keyB < $valLength){
							$selector[] = $qkAttrParam;
						}
						else{
							array_unshift($selector, $qkAttrParam);
						}
					}
				}

				if (!empty($selector)){
					$matches2selectors[] = $selector;
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
		foreach ($txt2array as $key => $attribute){

			if ($this->Elements->isBoolean($attribute) === TRUE){
				$remove = str_replace($attribute, '', $txt);
				$txt = $remove;
				$matches2selectors .= $attribute.' ';
			}
			else{
				break;
			}
		}

		if (strlen($matches2selectors) > 0){
			$booleanAttributes = $matches2selectors;
		}
		else{
			$booleanAttributes = NULL;
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
		$spacePrefix = "";

		$match = preg_match_all("/[^ >\n\-_\/\\\\&]+$/",$this->codeStorage);
		if($match === 1){
			$spacePrefix = " ";
		}

		$openTag = $spacePrefix.'<'.$element;

		if ($elementSettings['qkAttributes'] !== NULL and $attributes['qkAttributes'] !== NULL){
			$usedParameters = [];

			/*
			 * For each quick attribute in the array
			 * of recieved quick attributes from the actual line
			 * Only for quick attributes with an index before
			 */
			foreach($attributes['qkAttributes'] as $key => $parameter){

				if(count($parameter) === 2){
					$qkAttributeKey = $parameter[1] - 1;

					if(array_key_exists($qkAttributeKey, $elementSettings['qkAttributes'])){

						if (strtolower($parameter[0]) !== 'null' and strlen($parameter[0]) > 0){
							$attribute = $elementSettings['qkAttributes'][$qkAttributeKey].'="'.$parameter[0].'"';
							$openTag .= ' '.$attribute;
							$usedParameters[] = $key;
						}
					}
				}
			}

			// For each quick attribute without an index before
			foreach ($elementSettings['qkAttributes'] as $key => $attribute){

				if (array_key_exists($key, $attributes['qkAttributes']) and !in_array($key, $usedParameters)){

					if (strtolower($attributes['qkAttributes'][$key][0]) !== 'null' and strlen($attributes['qkAttributes'][$key][0]) > 0){
						$attribute = $elementSettings['qkAttributes'][$key].'="'.$attributes['qkAttributes'][$key][0].'"';
						$openTag .= ' '.$attribute;
					}
				}
			}
		}

		// Add the id attribute
		if ($attributes['id'] !== NULL){
			$openTag .= ' id="'.$attributes['id'].'"';
		}

		// Add classes
		if ($attributes['classes'] !== NULL){
			$openTag .= ' class="'.$attributes['classes'].'"';
		}

		// Add html attributes
		if ($attributes['htmlAttributes'] !== NULL){
			$openTag .= ' '.$attributes['htmlAttributes'];
		}

		// Add boolean attributes
		if ($attributes['booleanAttributes'] !== NULL){
			$openTag .= ' '.$attributes['booleanAttributes'];
		}

		// Close the open tag, add close tags if needed
		$openTag .= '>';
		$this->addCloseTags($lvl);
		$this->codeStorage .= $openTag;

		// Add txt
		if ($attributes['txt'] !== NULL){
			$this->codeStorage .= $attributes['txt'];
		}

		// If the tag is paired add its close tag to the storage
		if ($elementSettings['paired'] === TRUE){
			$closeTag = '</'.$element.'>';
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	/** @param int $lvl */
	private function addCloseTags ($lvl)
	{
		$length = count($this->closeTags);
		$lastTag = $length;

		if ($length > 0){

			for ($i = $length-1; $i >= 0; $i--){

				if ($lvl <= $this->closeTags[$i][0]){
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

		// For skip tag
		$closeTag = '/'.self::AREA_TAG;

		if ($element === self::AREA_TAG){
			$tagDetected = TRUE;
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $closeTag){
			$tagDetected = TRUE;
			$this->inNoCompileArea = FALSE;
		}

		// For style tag
		$tag = 'style';
		$open = '<'.$tag;
		$close = '</'.$tag.'>';

		if ($element === $open.'>' or $element === $open){
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close){
			$this->inNoCompileArea = FALSE;
		}

		// For script tag
		$tag = 'script';
		$open = '<'.$tag;
		$close = '</'.$tag.'>';

		if ($element === $open.'>' or $element === $open){
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close){
			$this->inNoCompileArea = FALSE;
		}

		// User defined or other tags

		return $tagDetected;
	}
}
