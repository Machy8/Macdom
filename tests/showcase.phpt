<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/showcase-b.html",
	$o->compile(file_get_contents("test-templates/showcase-a.html"))
);
