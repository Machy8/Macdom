<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::same('<a id="homepageLink" class="link" n:href="Homepage:default, id => $id, lang => cs">Homepage link</a>', $o->compileContent('a n$Homepage:default, id => $id, lang => cs; #homepageLink .link Homepage link'));
