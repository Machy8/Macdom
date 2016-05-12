<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->preferXhtml = TRUE;

Assert::matchFile(
	TEMPLATES."/prefer-xhtml-b.html",
	$o->compile(file_get_contents(TEMPLATES."/prefer-xhtml-a.html"))
);
