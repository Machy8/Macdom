<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/class-b.html",
	$o->compile(file_get_contents("test-templates/class-a.html"))
);

Assert::matchFile(
	"test-templates/id-b.html",
	$o->compile(file_get_contents("test-templates/id-a.html"))
);