<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

function run(Tester\TestCase $testCase)
{
	$testCase->run();
}
