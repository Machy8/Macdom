<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/skip-areas-a.html");
$result = file_get_contents("test-templates/skip-areas-b.html");

Assert::same($result, $o->compileContent($tested));
