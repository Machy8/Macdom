<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$testedA = "div Some text ";
$expectedA = "<div>Some text </div>";

$testedB = "div Some text ";
$expectedB = "<div>Some text</div>";

$macdom->setup->compressText = TRUE;

Assert::same($expectedA, $macdom->compile($testedA));

$macdom->setup->trim = 'both';

Assert::same($expectedB, $macdom->compile($testedB));