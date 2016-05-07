<?php

use Tester\Assert;

require 'bootstrap.php';

$o->setup->ncaRegExpInlineTags = ['\<(?:skipthisarea) *[^>]*\>.*\<\/skipthisarea\>'];
$o->setup->ncaRegExpOpenTags = ['\<(?:skipthisarea) *[^>]*\>'];
$o->setup->ncaCloseTags = ['</skipthisarea>'];
$o->setup->skipElements = 'table h6';

file_put_contents(__DIR__."/../myLog/log.html", $o->compile(file_get_contents("test-templates/skip-areas-a.html")));
Assert::matchFile(
	"test-templates/skip-areas-b.html", 
	$o->compile(file_get_contents("test-templates/skip-areas-a.html"))
);
