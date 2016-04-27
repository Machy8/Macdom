<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = 'a $google.com $blank Some text';
$resultA = '<a target="google.com" href="blank">Some text</a>';

$testedB = 'a $google.com $blank Some text';
$resultB = '<a>Some text</a>';

$o->setup->compressCode = TRUE;
$o->setup->changeQkAttributes = [
	'a' => [
		'target' => 'href',
		'href' => 'target',
	]
];

Assert::same($resultA, $o->compile($testedA));

$o->setup->changeQkAttributes = [
	'a' => [
		'target' => NULL,
		'href' => NULL
	]
];

Assert::same($resultB, $o->compile($testedB));
