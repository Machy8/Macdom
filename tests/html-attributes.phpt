<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/booleans-b.html", 
	$o->compile(file_get_contents("test-templates/booleans-a.html"))
);

Assert::matchFile(
	"test-templates/html-attributes-b.html", 
	$o->compile(file_get_contents("test-templates/html-attributes-a.html"))
);
