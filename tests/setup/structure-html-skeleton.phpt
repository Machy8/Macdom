<?php

use Tester\Assert;

require '../bootstrap.php';


Assert::matchFile(
	TEMPLATES."/structure-html-skeleton2-b.html",
	$o->compile(file_get_contents(TEMPLATES."/structure-html-skeleton2-a.html"))
);

$o->setup->structureHtmlSkeleton = TRUE;

Assert::matchFile(
	TEMPLATES."/structure-html-skeleton-b.html", 
	$o->compile(file_get_contents(TEMPLATES."/structure-html-skeleton-a.html"))
);
