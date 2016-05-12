<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/showcase-b.html",
	$o->compile(file_get_contents(TEMPLATES."/showcase-a.html"))
);
