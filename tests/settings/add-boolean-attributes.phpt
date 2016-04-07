<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = 'input $text beer steak muhehe';
$result = '<input type="text" beer steak muhehe />';

$o->setup->addBooleanAttributes = "beer steak muhehe";

Assert::same($result, $o->compileContent($tested));
