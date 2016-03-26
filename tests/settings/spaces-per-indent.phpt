<?php

use Tester\Assert;

require '../bootstrap.php';

Assert::same('<div><div id="innerDiv"></div></div>', $o->compileContent("div\n    div #innerDiv"));
$o->spacesPerIndent(3);
Assert::same('<div><div id="innerDiv"></div></div>', $o->compileContent("div\n   div #innerDiv"));
$o->spacesPerIndent(2);
Assert::same('<div><div id="innerDiv"></div></div>', $o->compileContent("div\n  div #innerDiv"));
$o->spacesPerIndent(1);
Assert::same('<div><div id="innerDiv"></div></div>', $o->compileContent("div\n div #innerDiv"));