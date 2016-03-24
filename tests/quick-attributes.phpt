<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::same('<input type="text" value="Value inside" />', $o->compileContent('input $text $Value inside;'));
Assert::same('<input type="text" value="+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\#" />', $o->compileContent('input $text $+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\#'));
Assert::same('<input value="Disabled button" type="submit" disabled />', $o->compileContent('input disabled 2$Disabled button; 1$submit'));
Assert::same('<a href="http://www.google.com" target="blank">Link somewhere</a>', $o->compileContent('a $http://www.google.com $blank Link somewhere'));
Assert::same('<a role="button">Just a button</a>', $o->compileContent('a 3$button Just a button'));
