<?php

use Tester\Assert;

require '../bootstrap.php';

$o->addElements([
	'svg' => [
      'qkAttributes' => ['width', 'height']
   ],
	'elementxy' => [
		'unpaired',
		'qkAttributes' => ['data-somedata']
	]
]);

Assert::same('<svg width="100" height="100">Inner text</svg>', $o->compileContent("svg $100 $100 Inner text"));
Assert::same('<elementxy data-somedata="Some data content" />', $o->compileContent('elementxy $Some data content;'));