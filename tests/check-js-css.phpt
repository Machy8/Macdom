<?php
use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/check-js-css-b.html",
	$o->compile(file_get_contents(TEMPLATES."/check-js-css-a.html"))
);