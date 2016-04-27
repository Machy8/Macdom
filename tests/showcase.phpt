<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/showcase-a.html");
$result = file_get_contents("test-templates/showcase-b.html");

Assert::same($result, $o->compile($tested));
