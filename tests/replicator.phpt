<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/replicator-a.html");
file_put_contents("test-templates/replicator-b.html",  $o->compileContent($tested));
$result = file_get_contents("test-templates/replicator-b.html");

Assert::same($result, $o->compileContent($tested));
