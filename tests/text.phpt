<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/text.html",
	$macdom->compile(file_get_contents(ACTUAL . "/text.html"))
);
