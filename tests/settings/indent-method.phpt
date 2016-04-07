<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = "div\n    div";
$resultA = '<div><div></div></div>';

$testedB = "div\n\tdiv";
$resultB = '<div></div><div></div>';

$testedC = "div\n\tdiv";
$resultC = '<div><div></div></div>';

$testedD = "div\n    div";
$resultD = '<div></div><div></div>';

$testedE = "div\n    div";
$resultE = '<div><div></div></div>';

$testedF = "div\n\tdiv";
$resultF = '<div><div></div></div>';

$testedG = "div\n    div\n    \tdiv";
$resultG = '<div><div><div></div></div></div>';


$o->setup->indentMethod = 1;
Assert::same($resultA, $o->compileContent($testedA));
Assert::same($resultB, $o->compileContent($testedB));
$o->setup->indentMethod = 2;
Assert::same($resultC, $o->compileContent($testedC));
Assert::same($resultD, $o->compileContent($testedD));
$o->setup->indentMethod = 3;
Assert::same($resultE, $o->compileContent($testedE));
Assert::same($resultF, $o->compileContent($testedF));
Assert::same($resultG, $o->compileContent($testedG));
