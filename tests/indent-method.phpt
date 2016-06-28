<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

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

$macdom->setup->compressCode = TRUE;
$macdom->setup->indentMethod = 'spaces';

Assert::same($resultA, $macdom->compile($testedA));
Assert::same($resultB, $macdom->compile($testedB));

$macdom->setup->indentMethod = 'tabs';

Assert::same($resultC, $macdom->compile($testedC));
Assert::same($resultD, $macdom->compile($testedD));

$macdom->setup->indentMethod = 'combined';

Assert::same($resultE, $macdom->compile($testedE));
Assert::same($resultF, $macdom->compile($testedF));
Assert::same($resultG, $macdom->compile($testedG));
