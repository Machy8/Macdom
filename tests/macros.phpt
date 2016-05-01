<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/macros-b.html",
	$o->compile(file_get_contents("test-templates/macros-a.html"))
);
