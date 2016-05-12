<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/booleans-b.html", 
	$o->compile(file_get_contents(TEMPLATES."/booleans-a.html"))
);

Assert::matchFile(
	TEMPLATES."/html-attributes-b.html", 
	$o->compile(file_get_contents(TEMPLATES."/html-attributes-a.html"))
);
