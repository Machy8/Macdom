<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->outputIndentation = "spaces";
$o->setup->structureHtmlSkeleton = TRUE;

Assert::matchFile(
	TEMPLATES."/output-indentation-b.html",
	$o->compile(file_get_contents(TEMPLATES."/output-indentation-a.html"))
);
