<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = file_get_contents("../test-templates/finall-code-indentation-a.html");
$result = file_get_contents("../test-templates/finall-code-indentation-b.html");

$o->setup->finallCodeIndentation = "spaces";
Assert::same($result, $o->compileContent($tested));