<?php

use Tester\Assert;

require '../bootstrap.php';

$o->indentMethod(1);
Assert::same("<div><div></div></div>", $o->compileContent("div\n    div"));
Assert::same("<div></div><div></div>", $o->compileContent("div\n\tdiv"));
$o->indentMethod(2);
Assert::same("<div></div><div></div>", $o->compileContent("div\n    div"));
Assert::same("<div><div></div></div>", $o->compileContent("div\n\tdiv"));
$o->indentMethod(3);
Assert::same("<div><div></div></div>", $o->compileContent("div\n    div"));
Assert::same("<div><div></div></div>", $o->compileContent("div\n\tdiv"));
Assert::same("<div><div><div></div></div></div>", $o->compileContent("div\n    div\n    \tdiv"));


