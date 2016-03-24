<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::same("<html></html>", $o->compileContent('html')); // Paired tag test
Assert::same("<input />", $o->compileContent('input')); // Self closing tag
Assert::same("<html></html><input />", $o->compileContent("html\ninput")); // Combined

