<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/macros-a.html");
$result = file_get_contents("test-templates/macros-b.html");

Assert::same($result, $o->compile($tested));
