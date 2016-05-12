<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	TEMPLATES."/replicator-b.html",
	$o->compile(file_get_contents(TEMPLATES."/replicator-a.html"))
);
