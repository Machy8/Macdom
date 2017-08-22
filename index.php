<?php

require_once "vendor/autoload.php";

use Tracy\Debugger;

Debugger::$strictMode = TRUE;
Debugger::enable(DEBUGGER::DEVELOPMENT);

$macdom = new Macdom\Engine;
$macdomPanel = new Macdom\Bridges\MacdomTracy\MacdomPanel;
$macdomPanel->setMacdom($macdom);

$compiled = $macdom->compile(file_get_contents('input.html'));
file_put_contents('output.html', $compiled);

echo $compiled;
