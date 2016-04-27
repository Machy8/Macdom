<?php

use Tester\Assert;

require 'bootstrap.php';

$testedA = 'html';
$resultA = '<html></html>';

$testedB = 'input';
$resultB = '<input>';

$testedC = "div\ninput";
$resultC = "<div></div><input>";

$o->setup->compressCode = TRUE;

Assert::same($resultA, $o->compile($testedA));
Assert::same($resultB, $o->compile($testedB));
Assert::same($resultC, $o->compile($testedC));
