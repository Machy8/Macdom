<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';


Assert::matchFile(
	EXPECTED . "/structure-html-skeleton2.html",
	$macdom->compile(file_get_contents(ACTUAL . "/structure-html-skeleton2.html"))
);

$macdom->setup->structureHtmlSkeleton = TRUE;

Assert::matchFile(
	EXPECTED . "/structure-html-skeleton.html",
	$macdom->compile(file_get_contents(ACTUAL . "/structure-html-skeleton.html"))
);
