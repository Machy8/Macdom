<?php

use Tester\Assert;

require '../bootstrap.php';

$tested = "div\nspan\na";
$result = "divspana";

$o->setup->compressCode = TRUE;
$o->setup->removeElements = "a span div";

Assert::same($result, $o->compileContent($tested));
