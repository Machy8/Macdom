<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$testedA = 'a $google.com $blank Some text';
$resultA = '<a target="google.com" href="blank">Some text</a>';

$testedB = 'a $google.com $blank Some text';
$resultB = '<a>Some text</a>';

$macdom->setup->compressCode = TRUE;
$macdom->setup->changeQkAttributes = [
	'a' => [
		'target' => 'href',
		'href' => 'target',
	]
];

Assert::same($resultA, $macdom->compile($testedA));

$macdom->setup->changeQkAttributes = [
	'a' => [
		'target' => NULL,
		'href' => NULL
	]
];

Assert::same($resultB, $macdom->compile($testedB));
