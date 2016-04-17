<?php

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Loader.php';

$o = new Machy8\Macdom\Loader;

\Tracy\Debugger::enable(\Tracy\Debugger::DETECT, __DIR__."/../myLog");

