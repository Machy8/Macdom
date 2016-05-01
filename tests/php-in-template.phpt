<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/php-in-template-b.html",
	$o->compile(file_get_contents("test-templates/php-in-template-a.html"))
);
