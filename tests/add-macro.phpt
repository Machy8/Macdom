<?php

use Tester\Assert;


require __DIR__ . '/bootstrap.php';

$inputFunction = function ($line) {
	return '<input type="password" data-user="' . $line . '" placeholder="New password">';
};

$macdom->setup->addMacros = [
	'title1' => function ($line) {
		return '<h1>' . $line . '</h1>';
	},
	'passwordInput' => $inputFunction
];

Assert::matchFile(
	EXPECTED . "/add-macro.html",
	$macdom->compile(file_get_contents(ACTUAL . "/add-macro.html"))
);