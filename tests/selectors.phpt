<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/class.html",
	$macdom->compile(file_get_contents(ACTUAL . "/class.html"))
);

Assert::matchFile(
	EXPECTED . "/id.html",
	$macdom->compile(file_get_contents(ACTUAL . "/id.html"))
);