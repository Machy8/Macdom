<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/php-in-template-a.html");
$result = file_get_contents("test-templates/php-in-template-b.html");

Assert::same($result, $o->compileContent($tested));
