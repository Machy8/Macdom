<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/text-a.html");
$result = file_get_contents("test-templates/text-b.html");

Assert::same($result, $o->compile($tested));
