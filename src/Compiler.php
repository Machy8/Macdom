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

class Compiler extends ElementsHelpers
{

	// @var string
	private $codeStorage = "";

	// @var array
	private $closeTags = [];

	// @var integer
	private $spacesInTab = 4;

	// @var regular expression
	private $sRegExp;

	// @var string
	private $noCompileAreaTag = "SKIP";

	// @var bool
	private $inNoCompileArea = FALSE;

	public function __construct ()
	{
		parent::__construct();
		$this->sRegExp = "/ {".$this->spacesInTab."}/";
	}

	// @param string $content
	// @return string $this->codeStorage
	public function compile ($content)
	{
		$lns = preg_split("/\n/", $content);

		foreach ($lns as $key => $value)
		{
			$ln = $value;
			$lvl = $this->getLnLvl($ln);
			$txt = $this->getLnText($ln);
			$ln2array = explode(" ", trim($txt));

			// Element is the first word on line
			$element = $ln2array[0];
			$noCompileAreaTag = $this->detectNoCompileArea($element);

			if ($this->findElement($element, "exists") === TRUE and $this->inNoCompileArea === FALSE)
			{
				$removeElement = preg_replace('/'.$element.'/', '', trim($txt), 1);
				$txt = $removeElement;
				$attributes = $this->getLnAttributes($txt);
				$this->addOpenTag($element, $lvl, $attributes);
			}
			else
			{
				if ($txt !== NULL and $this->inNoCompileArea === FALSE)
				{
				    if($noCompileAreaTag === FALSE)
				    {
					$this->addCloseTags($lvl);
					$this->codeStorage .= $txt;
				    }
				}
				elseif($txt !== NULL and $this->inNoCompileArea === TRUE)
				{
				    if($noCompileAreaTag === FALSE)
				    {

					$this->codeStorage .= $ln;
				    }
				}
			}
		}

		$this->addCloseTags(0);

		return $this->codeStorage;
	}

	// @param string $ln
	// @return string $lvl
	private function getLnLvl ($ln)
	{
		$spaces = preg_match_all($this->sRegExp, $ln);

		// Get the number of tabulators on the line
		// One tabulators = 2 levels
		$tabulators = preg_match_all("/\t/", $ln)*2;
		$lvl = $spaces + $tabulators;

		return $lvl;
	}

	// @param string $ln
	// @return string $txt
	private function getLnText ($ln)
	{
		$replaceTabulators = preg_replace("/\t/", "", trim($ln));
		$replaceSpaces = preg_replace($this->sRegExp, "", $replaceTabulators);
		$txt = $replaceSpaces;

		return $txt;
	}

	// @param string $txt
	// @return array
	private function getLnAttributes ($txt)
	{
		// Replace n$*; for n:href=""
		$re = '/ n\${1}(.+);{1}/';
		$nHref = preg_match($re, $txt, $matches);

		if ($nHref !== 0 and $nHref !== FALSE)
		{
			$value = $matches[1];

			if (empty($value))
			{
				$value = $matches[2];
			}

			$newHref = ' n:href="'.$value.'"';
			$replaced = preg_replace($re, $newHref, $txt);
			$txt = $replaced;
		}

		// Get all html attributes
		$re = '/ [\w:-]+={1}\"{1}.*\"{1}| [\w:-]+={1}\S+/';
		$htmlAttributes = preg_match_all($re, $txt, $matches);
		$matches2selectors = "";

		if ($htmlAttributes !== 0 and $htmlAttributes !== FALSE)
		{
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;

			foreach ($matches[0] as $key => $value)
			{
				$matches2selectors .= $value.' ';
			}

			$htmlAttributes = trim($matches2selectors);
		}
		else
		{
			$htmlAttributes = NULL;
		}

		// Get the id selector
		$re = "/ \#{1}(\S+)/";
		$idSelector = preg_match($re, $txt, $matches);

		if ($idSelector !== 0 and $idSelector !== FALSE)
		{
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$idSelector = $matches[1];
		}
		else
		{
			$idSelector = NULL;
		}

		// Get all class selectors
		$re = "/ \.{1}(\S+)/";
		$clsSelectors = preg_match_all($re, $txt, $matches);

		if ($clsSelectors !== 0 and $clsSelectors !== FALSE)
		{
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;
			$matches2selectors = '';

			foreach ($matches[1] as $key => $value)
			{
				$matches2selectors .= $value.' ';
			}

			$clsSelectors = trim($matches2selectors);
		}
		else
		{
			$clsSelectors = NULL;
		}

		// Get all quick attributes
		$re = '/ ([\d]*)\$([^$]+);{1}| ([\d]*)\$(\S+)/';
		$qkAttributes = preg_match_all($re, $txt, $matches, PREG_SET_ORDER);
		$matches2selectors = [];

		if ($qkAttributes !== 0 and $qkAttributes !== FALSE)
		{
			$remove = preg_replace($re, '', $txt);
			$txt = $remove;

			foreach ($matches as $key => $value)
			{
				$selector = [];
				$valLength = count($value);

				foreach($value as $keyB => $qkAttrParam)
				{
					if (!empty($qkAttrParam) and $keyB !== 0)
					{
						$match = preg_match("/\d/", $qkAttrParam);

						// If quick attribute is without index
						if($match === 1 and $keyB < $valLength)
						{
							$selector[] = $qkAttrParam;
						}
						else
						{
							array_unshift($selector, $qkAttrParam);
						}
					}
				}

				if (!empty($selector))
				{
					$matches2selectors[] = $selector;
				}
			}

			$qkAttributes = $matches2selectors;
		}
		else
		{
			$qkAttributes = NULL;
		}

		// Get the text
		$getTxt = $this->getLnText($txt);
		$txt = $getTxt;

		// Split the txt to an array in oder to get the boolean attributes
		$txt2array = explode(" ", $txt);
		$matches2selectors = "";

		// Get boolean attributes
		foreach ($txt2array as $key => $attribute)
		{
			if ($this->isBoolean($attribute) === TRUE)
			{
				$remove = str_replace($attribute, '', $txt);
				$txt = $remove;
				$matches2selectors .= $attribute.' ';
			}
			else
			{
				break;
			}
		}

		if (strlen($matches2selectors) > 0)
		{
			$booleanAttributes = $matches2selectors;
		}
		else
		{
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

	// @param string $element
	// @param int $lvl
	// @param array $attributes
	private function addOpenTag ($element, $lvl, $attributes)
	{
		$elementSettings = $this->findElement($element, "settings");
		$openTag = '<'.$element;

		if ($elementSettings['qkAttributes'] !== NULL and $attributes['qkAttributes'] !== NULL)
		{
			$usedParameters = [];

			// For each quick attribute in the array
			// of recieved quick attributes from the actual line
			// Only for quick attributes with an index before
			foreach($attributes['qkAttributes'] as $key => $parameter)
			{
				if(count($parameter) === 2)
				{
					$qkAttributeKey = $parameter[1] - 1;
					if(array_key_exists($qkAttributeKey, $elementSettings['qkAttributes']))
					{
						if (strtolower($parameter[0]) !== 'null' and strlen($parameter[0]) > 0)
						{
							$attribute = $elementSettings['qkAttributes'][$qkAttributeKey].'="'.$parameter[0].'"';
							$openTag .= ' '.$attribute;
							$usedParameters[] = $key;
						}
					}
				}
			}

			// For each quick attribute without an index before
			foreach ($elementSettings['qkAttributes'] as $key => $attribute)
			{
				if (array_key_exists($key, $attributes['qkAttributes']) and !in_array($key, $usedParameters))
				{
					if (strtolower($attributes['qkAttributes'][$key][0]) !== 'null' and strlen($attributes['qkAttributes'][$key][0]) > 0)
					{
						$attribute = $elementSettings['qkAttributes'][$key].'="'.$attributes['qkAttributes'][$key][0].'"';
						$openTag .= ' '.$attribute;
					}
				}
			}
		}

		// Add the id attribute
		if ($attributes['id'] !== NULL)
		{
			$openTag .= ' id="'.$attributes['id'].'"';
		}

		// Add classes
		if ($attributes['classes'] !== NULL)
		{
			$openTag .= ' class="'.$attributes['classes'].'"';
		}

		// Add html attributes
		if ($attributes['htmlAttributes'] !== NULL)
		{
			$openTag .= ' '.$attributes['htmlAttributes'];
		}

		// Add boolean attributes
		if ($attributes['booleanAttributes'] !== NULL)
		{
			$openTag .= ' '.$attributes['booleanAttributes'];
		}

		// Close the open tag, add close tags if needed
		$openTag .= ' >';
		$this->addCloseTags($lvl);
		$this->codeStorage .= $openTag;

		// Add txt
		if ($attributes['txt'] !== NULL)
		{
			$this->codeStorage .= $attributes['txt'];
		}

		// If the tag is paired add its close tag to the storage
		if ($elementSettings['paired'] === TRUE)
		{
			$closeTag = '</'.$element.'>';
			$this->closeTags[] = [$lvl, $closeTag];
		}
	}

	// @param int $lvl
	private function addCloseTags ($lvl)
	{
		$length = count($this->closeTags);
		$lastTag = $length;

		if ($length > 0)
		{
			for ($i = $length-1; $i >= 0; $i--)
			{
				if ($lvl <= $this->closeTags[$i][0])
				{
					$this->codeStorage .= $this->closeTags[$i][1];
					$lastTag = $i;
				}
				else
				{
					break;
				}
			}

			array_splice($this->closeTags, $lastTag);
		}
	}

	// @param string $element
	// @return bool $detected
	private function detectNoCompileArea ($element)
	{
		$detected = FALSE;

		// For skip tag
		$closeTag = '/'.$this->noCompileAreaTag;

		if ($element === $this->noCompileAreaTag)
		{
			$detected = TRUE;
			$this->inNoCompileArea = TRUE;
		}
		elseif ($element === $closeTag)
		{
			$detected = TRUE;
			$this->inNoCompileArea = FALSE;
		}

		// For style tag
		$tag = 'style';
		$open = '<'.$tag;
		$close = '</'.$tag.'>';

		if ($element === $open.'>' or $element === $open)
		{
		    $this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close)
		{
		    $this->inNoCompileArea = FALSE;
		}

		// For script tag
		$tag = 'script';
		$open = '<'.$tag;
		$close = '</'.$tag.'>';

		if ($element === $open.'>' or $element === $open)
		{
		    $this->inNoCompileArea = TRUE;
		}
		elseif ($element === $close)
		{
		    $this->inNoCompileArea = FALSE;
		}

		// User defined or other tags...

		return $detected;
	}
}
