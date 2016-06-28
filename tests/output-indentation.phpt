<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$macdom->setup->outputIndentation = "spaces";
$macdom->setup->structureHtmlSkeleton = TRUE;

Assert::matchFile(
	EXPECTED . "/output-indentation.html",
	$macdom->compile(file_get_contents(ACTUAL . "/output-indentation.html"))
);
