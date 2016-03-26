<?php

use Tester\Assert;

require '../bootstrap.php';

Assert::same('<input type=text placeholder="Just a placeholder" disabled />', $o->compileContent('input type=text disabled placeholder="Just a placeholder"'));
Assert::same('<input type=text value="Text inside" readonly />', $o->compileContent('input type=text value="Text inside" readonly'));
Assert::same('<input type=text value="Text inside" required disabled readonly />', $o->compileContent('input required type=text disabled value="Text inside" readonly'));
 

