<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$tested = 'input disabled hidden';
$result = '<input>';

$macdom->setup->compressCode = TRUE;
$macdom->setup->removeBooleanAtributes = "disabled hidden";

Assert::same($result, $macdom->compile($tested));
