<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$macdom->setup->addBooleanAttributes = "beer steak muhehe";
$macdom->setup->compressCode = TRUE;

Assert::matchFile(
	EXPECTED . "/add-boolean-attributes.html",
	$macdom->compile(file_get_contents(ACTUAL . "/add-boolean-attributes.html"))
);
