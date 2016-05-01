<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/text-b.html",
	$o->compile(file_get_contents("test-templates/text-a.html"))
);
