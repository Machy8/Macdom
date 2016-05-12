<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/php-in-template-b.html",
	$o->compile(file_get_contents(TEMPLATES."/php-in-template-a.html"))
);
