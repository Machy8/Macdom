<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/quick-attributes-a.html");
$result = file_get_contents("test-templates/quick-attributes-b.html");

Assert::same($result, $o->compileContent($tested));
