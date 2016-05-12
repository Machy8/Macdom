<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/class-b.html",
	$o->compile(file_get_contents(TEMPLATES."/class-a.html"))
);

Assert::matchFile(
	TEMPLATES."/id-b.html",
	$o->compile(file_get_contents(TEMPLATES."/id-a.html"))
);