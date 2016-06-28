<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$tested = "!5\nutf-8";
$result = "!5utf-8";

$macdom->setup->compressCode = TRUE;
$macdom->setup->removeMacros = "!5 utf-8";

Assert::same($result, $macdom->compile($tested));
