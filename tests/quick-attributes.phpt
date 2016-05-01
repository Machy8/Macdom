<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/quick-attributes-b.html", 
	$o->compile(file_get_contents("test-templates/quick-attributes-a.html"))
);
