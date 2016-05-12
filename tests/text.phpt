<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/text-b.html",
	$o->compile(file_get_contents(TEMPLATES."/text-a.html"))
);
