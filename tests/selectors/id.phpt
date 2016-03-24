<?php

use Tester\Assert;

require '../bootstrap.php';

Assert::same('<div id="id"></div>', $o->compileContent('div #id'));
Assert::same('<div id="i_d"></div>', $o->compileContent('div #i_d'));
Assert::same('<div id=";+=´´)ú¨§ů-.,-*/+_:\\()\[]@&|\#"></div>', $o->compileContent('div #;+=´´)ú¨§ů-.,-*/+_:\\()\[]@&|\#'));
Assert::same('<div id="#id"></div>', $o->compileContent('div ##id'));
Assert::same('<div id="primary"></div>', $o->compileContent('div #primary #unused'));
Assert::same('<div id="primary"></div>', $o->compileContent('div #secondary id="primary"'));
Assert::same('<div id="primary"></div>', $o->compileContent('div id="primary" #secondary'));
