<?php

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Loaders/Loader.php';

$o = new Machy8\Macdom\Loaders\Loader;

\Tracy\Debugger::enable(\Tracy\Debugger::DETECT, __DIR__."/../myLog");

