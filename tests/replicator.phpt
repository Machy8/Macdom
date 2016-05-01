<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::matchFile(
	"test-templates/replicator-b.html",
	$o->compile(file_get_contents("test-templates/replicator-a.html"))
);
