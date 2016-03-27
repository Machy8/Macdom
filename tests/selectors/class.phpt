<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = file_get_contents("../test-templates/class-a.html");
$result = file_get_contents("../test-templates/class-b.html");

Assert::same($result, $o->compileContent($tested));
