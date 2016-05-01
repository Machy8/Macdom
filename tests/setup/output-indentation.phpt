<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->outputIndentation = "spaces";

Assert::matchFile(
	"../test-templates/output-indentation-b.html",
	$o->compile(file_get_contents("../test-templates/output-indentation-a.html"))
);
