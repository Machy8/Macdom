<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$macdom->setup->skipElements = 'table h6 skipthisarea';
$macdom->setup->compressText = TRUE;

Assert::matchFile(
	EXPECTED . "/compress-text.html",
	$macdom->compile(file_get_contents(ACTUAL . "/compress-text.html"))
);
