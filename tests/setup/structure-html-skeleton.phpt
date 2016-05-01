<?php

use Tester\Assert;

require '../bootstrap.php';

Assert::matchFile(
	"../test-templates/structure-html-skeleton-b.html",
	$o->compile(file_get_contents("../test-templates/structure-html-skeleton-a.html"))
);

$o->setup->structureHtmlSkeleton = FALSE;

Assert::matchFile(
	"../test-templates/structure-html-skeleton2-b.html", 
	$o->compile(file_get_contents("../test-templates/structure-html-skeleton2-a.html"))
);
