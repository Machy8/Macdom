<?php

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Loader.php';

$o = new Machy8\Macdom\Loader;
$o->setup->compressCode = TRUE;

Tester\Environment::setup();

date_default_timezone_set('Europe/Prague');
