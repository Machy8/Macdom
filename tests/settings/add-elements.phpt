<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = 'svg $100 $100 Inner text';
$resultA = '<svg width="100" height="100">Inner text</svg>';

$testedB = 'elementxy $Some data content;';
$resultB = '<elementxy data-somedata="Some data content" />';

$o->setup->addElements = [
	'svg' => [
		'qkAttributes' => ['width', 'height']
	],
	'elementxy' => [
		'unpaired',
		'qkAttributes' => ['data-somedata']
	]
];

Assert::same($resultA, $o->compileContent($testedA));
Assert::same($resultB, $o->compileContent($testedB));
