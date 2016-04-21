<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = file_get_contents("../test-templates/prefer-xhtml-a.html");
$result = file_get_contents("../test-templates/prefer-xhtml-b.html");

$o->setup->preferXhtml = TRUE;

Assert::same($result, $o->compileContent($tested));
