<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$macdom->setup->addQkAttributes = [
	'span' => 'data-first data-second',
	'div' => 'data-first data-second data-third'
];

Assert::matchFile(
	EXPECTED . "/add-qk-attributes.html",
	$macdom->compile(file_get_contents(ACTUAL . "/add-qk-attributes.html"))
);
