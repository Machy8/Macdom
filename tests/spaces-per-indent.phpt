<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$testedA = "div\n    div #innerDiv";
$resultA = '<div><div id="innerDiv"></div></div>';

$testedB = "div\n   div #innerDiv";
$resultB = '<div><div id="innerDiv"></div></div>';

$testedC = "div\n  div #innerDiv";
$resultC = '<div><div id="innerDiv"></div></div>';

$testedD = "div\n div #innerDiv";
$resultD = '<div><div id="innerDiv"></div></div>';

$macdom->setup->compressCode = TRUE;

Assert::same($resultA, $macdom->compile($testedA));

$macdom->setup->spacesPerIndent = 3;

Assert::same($resultB, $macdom->compile($testedB));

$macdom->setup->spacesPerIndent = 2;

Assert::same($resultC, $macdom->compile($testedC));

$macdom->setup->spacesPerIndent = 1;

Assert::same($resultD, $macdom->compile($testedD));
