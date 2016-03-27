<?php

use Tester\Assert;

require '../bootstrap.php';

$o->addMacro('title1', function($line) {
	return '<h1>' . $line . '</h1>';
})->addMacro('passwordInput', function($line) {
	return '<input type="password" data-user="' . $line . '" placeholder="New password" />';
});

Assert::same('<h1>Some text in the h1 element</h1>', $o->compileContent("title1 Some text in the h1 element"));
Assert::same('<input type="password" data-user="user12345" placeholder="New password" />', $o->compileContent("passwordInput user12345"));
