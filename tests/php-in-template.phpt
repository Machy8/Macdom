<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/php-in-template.html",
	$macdom->compile(file_get_contents(ACTUAL . "/php-in-template.html"))
);
