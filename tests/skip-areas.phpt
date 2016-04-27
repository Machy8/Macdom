<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = file_get_contents("test-templates/skip-areas-a.html");
$result = file_get_contents("test-templates/skip-areas-b.html");

$o->setup->ncaRegExpInlineTags = ['\<(?:skipthisarea) *[^>]*\>.*\<\/skipthisarea\>'];
$o->setup->ncaRegExpOpenTags = ['\<(?:skipthisarea) *[^>]*\>'];
$o->setup->ncaCloseTags = ['</skipthisarea>'];

Assert::same($result, $o->compile($tested));
