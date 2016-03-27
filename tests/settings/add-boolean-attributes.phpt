<?php

use Tester\Assert;

require '../bootstrap.php';

$o->addBooleanAttributes([
	'beer', 'steak', 'muhehe'
]);

Assert::same('<input type="text" beer steak muhehe />', $o->compileContent('input $text beer steak muhehe'));
