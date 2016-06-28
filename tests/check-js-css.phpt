<?php
use Tester\Assert;


require __DIR__ . '/bootstrap.php';

Assert::matchFile(
	EXPECTED . "/check-js-css.html",
	$macdom->compile(file_get_contents(ACTUAL . "/check-js-css.html"))
);