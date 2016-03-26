<?php 
use Tester\Assert;

require '../bootstrap.php';

Assert::same('<div id="id" class="class" data-label=label></div>', $o->compileContent('div id="id" class="class" data-label=label'));
Assert::same('<div data-mix=;+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\# id=mix class="class"></div>', $o->compileContent('div data-mix=;+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\# id=mix class="class"'));