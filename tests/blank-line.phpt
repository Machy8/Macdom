<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$testedA = "input";
$expectedA = "<input>";

$testedB = "input";
$expectedB = "<input>\n";

Assert::same($expectedA, $macdom->compile($testedA));

$macdom->setup->blankLine = TRUE;

Assert::same($expectedB, $macdom->compile($testedB));