<?php



require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Loader.php';

$o = new Machy8\Macdom\Loader;
$o->compressCode();

Tester\Environment::setup();

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT, __DIR__ . '/../../../../log');
date_default_timezone_set('Europe/Prague');
