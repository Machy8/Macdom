<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/replicator-a.html");
$result = file_get_contents("test-templates/replicator-b.html");

Assert::same($result, $o->compile($tested));
