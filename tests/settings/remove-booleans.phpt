<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = 'input disabled hidden';
$result = '<input>';
$o->setup->compressCode = TRUE;
$o->setup->removeBooleanAtributes = "disabled hidden";

Assert::same($result, $o->compileContent($tested));
