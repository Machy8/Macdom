<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::same("<html></html>", $o->compileContent('html'));
Assert::same("<input />", $o->compileContent('input'));
Assert::same("<html></html><input />", $o->compileContent("html\ninput"));
