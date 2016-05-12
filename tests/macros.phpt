<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/macros-b.html",
	$o->compile(file_get_contents(TEMPLATES."/macros-a.html"))
);
