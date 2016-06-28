<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/html-attributes.html",
	$macdom->compile(file_get_contents(ACTUAL . "/html-attributes.html"))
);
