<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/booleans.html",
	$macdom->compile(file_get_contents(ACTUAL . "/booleans.html"))
);