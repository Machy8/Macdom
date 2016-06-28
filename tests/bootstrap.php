<?php

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Loaders/Loader.php';

const

	// Expected result
EXPECTED = __DIR__ . '/expected',

	// Compiled
ACTUAL = __DIR__ . '/actual';

$macdom = new Machy8\Macdom\Loaders\Loader;

$macdom->setup->structureHtmlSkeleton = FALSE;

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

function rewriteTest ($testName, $macdom)
{
	if (file_exists(ACTUAL . '/' . $testName . '.html') && file_exists(EXPECTED . '/' . $testName . '.html')) {

		file_put_contents(
			EXPECTED . '/' . $testName . ".html",
			$macdom->compile(file_get_contents(ACTUAL . '/' . $testName . '.html')));
	}
}
