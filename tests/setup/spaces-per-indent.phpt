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

Assert::same($resultA, $o->compile($testedA));

$o->setup->spacesPerIndent = 3;

Assert::same($resultB, $o->compile($testedB));

$o->setup->spacesPerIndent = 2;

Assert::same($resultC, $o->compile($testedC));

$o->setup->spacesPerIndent = 1;

Assert::same($resultD, $o->compile($testedD));
