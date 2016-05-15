<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->skipElements = 'table h6 skipthisarea';
$o->setup->compressText = TRUE;

Assert::matchFile(
	TEMPLATES."/compress-text-B.html",
	$o->compile(file_get_contents(TEMPLATES."/compress-text-a.html"))
);
