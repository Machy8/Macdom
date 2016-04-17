<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = "!5\nutf-8";
$result = "!5utf-8";
$o->setup->compressCode = TRUE;
$o->setup->removeMacros = "!5 utf-8";

Assert::same($result, $o->compileContent($tested));