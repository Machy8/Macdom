<?php

use Tester\Assert;

require 'bootstrap.php';

$o->setup->skipElements = 'table h6 skipthisarea';

Assert::matchFile(
	TEMPLATES."/skip-areas-b.html", 
	$o->compile(file_get_contents(TEMPLATES."/skip-areas-a.html"))
);
