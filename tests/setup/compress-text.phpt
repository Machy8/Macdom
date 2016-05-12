<?php

use Tester\Assert;

require '../bootstrap.php';

$o->setup->ncaRegExpInlineTags = ['\<(?:skipthisarea) *[^>]*\>.*\<\/skipthisarea\>'];
$o->setup->ncaRegExpOpenTags = ['\<(?:skipthisarea) *[^>]*\>'];
$o->setup->ncaCloseTags = ['</skipthisarea>'];
$o->setup->skipElements = 'table h6';
$o->setup->compressText = TRUE;

Assert::matchFile(
	TEMPLATES."/compress-text-B.html",
	$o->compile(file_get_contents(TEMPLATES."/compress-text-a.html"))
);
