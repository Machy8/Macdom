<?php

use Tester\Assert;

require '../bootstrap.php';

Assert::same('<div class="class"></div>', $o->compileContent('div .class'));
Assert::same('<div class="c_l_a_s_s"></div>', $o->compileContent('div .c_l_a_s_s'));
Assert::same('<div class=";+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\# behind"></div>', $o->compileContent('div .;+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\# .behind'));
Assert::same('<div class="....class another"></div>', $o->compileContent('div .....class .another'));
Assert::same('<div class="class"></div>', $o->compileContent('div .class'));
Assert::same('<div class="class1 class2"></div>', $o->compileContent('div .class1 .class2'));
Assert::same('<div class="normal shortcut"></div>', $o->compileContent('div .shortcut class="normal"'));
Assert::same('<div class="normal2 shortcut2"></div>', $o->compileContent('div class="normal2" .shortcut2'));
