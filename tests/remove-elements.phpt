<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$tested = "div\nspan\na";
$result = "divspana";

$macdom->setup->compressCode = TRUE;
$macdom->setup->removeElements = "a span div";

Assert::same($result, $macdom->compile($tested));
