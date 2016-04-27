<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = 'svg $100 $100 Inner text';
$resultA = '<svg width="100" height="100">Inner text</svg>';

$testedB = 'elementxy $Some data content;';
$resultB = '<elementxy data-somedata="Some data content">';

$o->setup->addElements = [
	'svg' => [
		'qkAttributes' => ['width', 'height']
	],
	'elementxy' => [
		'unpaired',
		'qkAttributes' => ['data-somedata']
	]
];

$o->setup->compressCode = TRUE;

Assert::same($resultA, $o->compile($testedA));
Assert::same($resultB, $o->compile($testedB));
