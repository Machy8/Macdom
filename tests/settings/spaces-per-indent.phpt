<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = "div\n    div #innerDiv";
$resultA = '<div><div id="innerDiv"></div></div>';

$testedB = "div\n   div #innerDiv";
$resultB = '<div><div id="innerDiv"></div></div>';

$testedC = "div\n  div #innerDiv";
$resultC = '<div><div id="innerDiv"></div></div>';

$testedD = "div\n div #innerDiv";
$resultD = '<div><div id="innerDiv"></div></div>';

$o->setup->compressCode = TRUE;

Assert::same($resultA, $o->compileContent($testedA));

$o->setup->spacesPerIndent = 3;

Assert::same($resultB, $o->compileContent($testedB));

$o->setup->spacesPerIndent = 2;

Assert::same($resultC, $o->compileContent($testedC));

$o->setup->spacesPerIndent = 1;

Assert::same($resultD, $o->compileContent($testedD));
