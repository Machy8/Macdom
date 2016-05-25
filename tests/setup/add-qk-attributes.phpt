<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->addQkAttributes = [
	'span' => 'data-first data-second',
	'div' => 'data-first data-second data-third'
];

Assert::matchFile(
	TEMPLATES."/add-qk-attributes-b.html",
	$o->compile(file_get_contents(TEMPLATES."/add-qk-attributes-a.html"))
);