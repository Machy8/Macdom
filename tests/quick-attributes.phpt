<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/quick-attributes-b.html", 
	$o->compile(file_get_contents(TEMPLATES."/quick-attributes-a.html"))
);
