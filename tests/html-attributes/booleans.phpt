<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = file_get_contents("../test-templates/booleans-a.html");
$result = file_get_contents("../test-templates/booleans-b.html");

Assert::same($result, $o->compileContent($tested));
