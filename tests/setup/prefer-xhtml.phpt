<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->preferXhtml = TRUE;

Assert::matchFile(
	"../test-templates/prefer-xhtml-b.html",
	$o->compile(file_get_contents("../test-templates/prefer-xhtml-a.html"))
);
