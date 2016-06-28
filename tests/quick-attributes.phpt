<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/quick-attributes.html",
	$macdom->compile(file_get_contents(ACTUAL . "/quick-attributes.html"))
);
