<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$macdom->setup->addElements = [
	'svg' => [
		'qkAttributes' => ['width', 'height']
	],

	'elementxy' => [
		'unpaired',
		'qkAttributes' => ['data-somedata']
	]
];

$macdom->setup->compressText = TRUE;

Assert::matchFile(
	EXPECTED . "/add-elements.html",
	$macdom->compile(file_get_contents(ACTUAL . "/add-elements.html"))
);
