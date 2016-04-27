<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = file_get_contents("../test-templates/id-a.html");
$result = file_get_contents("../test-templates/id-b.html");

Assert::same($result, $o->compile($tested));
