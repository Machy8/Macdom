<?php
use Tester\Assert;

require 'bootstrap.php';
$tested = file_get_contents("test-templates/replicator-a.php");
$result = file_get_contents("test-templates/replicator-b.php");
Assert::same($result, $o->compileContent($tested));

