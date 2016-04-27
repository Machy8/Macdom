<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = 'title1 Some text in the h1 element';
$resultA = '<h1>Some text in the h1 element</h1>';

$testedB = 'passwordInput user12345';
$resultB = '<input type="password" data-user="user12345" placeholder="New password">';

$inputFunction = function ($line) {
	return '<input type="password" data-user="' . $line . '" placeholder="New password">';
};

$o->setup->addMacros = [
	'title1' => function ($line) {
		return '<h1>' . $line . '</h1>';
	},
	'passwordInput' => $inputFunction
];

$o->setup->compressCode = TRUE;

Assert::same($resultA, $o->compile($testedA));
Assert::same($resultB, $o->compile($testedB));
