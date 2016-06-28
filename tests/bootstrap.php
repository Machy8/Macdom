<?php

require __DIR__ . '/../vendor/autoload.php';

// Expected
define('EXPECTED', __DIR__ . '/expected');

// Compiled
define('ACTUAL', __DIR__ . '/actual');

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
