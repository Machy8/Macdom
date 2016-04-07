<?php

use Tester\Assert;

require 'bootstrap.php';

$testedA = 'html';
$resultA = '<html></html>';

$testedB = 'input';
$resultB = '<input />';

$testedC = "html\ninput";
$resultC = "<html></html><input />";

Assert::same($resultA, $o->compileContent($testedA));
Assert::same($resultB, $o->compileContent($testedB));
Assert::same($resultC, $o->compileContent($testedC));
