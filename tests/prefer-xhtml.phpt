<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$macdom->setup->preferXhtml = TRUE;

Assert::matchFile(
	EXPECTED . "/prefer-xhtml.html",
	$macdom->compile(file_get_contents(ACTUAL . "/prefer-xhtml.html"))
);
