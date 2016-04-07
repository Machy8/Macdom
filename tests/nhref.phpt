<?php

use Tester\Assert;

require 'bootstrap.php';

$tested = 'a n$Homepage:default, id => $id, lang => cs; #homepageLink .link Homepage link';
$result = '<a n:href="Homepage:default, id => $id, lang => cs" id="homepageLink" class="link">Homepage link</a>';

Assert::same($result, $o->compileContent($tested));
