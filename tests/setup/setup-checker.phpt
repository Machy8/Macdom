<?php

use Tester\Assert;
use Machy8\Macdom\Setup\SetupChecker;
use Machy8\Macdom\Setup\Setup;
require '../bootstrap.php';

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->addBooleanAttributes = TRUE;
	$checker->check($s);
}, 'Exception', 'Variable $addBooleanAttributes must have a STRING value.');

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->addElements = TRUE;
	$checker->check($s);
}, 'Exception', 'Variable $addElements must have an ARRAY value.');

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->booleansWithValue = "";
	$checker->check($s);
}, 'Exception', 'Variable $booleansWithValue must have a BOOLEAN value.');

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->indentMethod = 10;
	$checker->check($s);
}, 'Exception', 'Variable $indentMethod has illegal value. Options are: 1, 2, 3.');


Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->outputIndentation = "hamburger";
	$checker->check($s);
}, 'Exception', 'Variable $outputIndentation has illegal value. Options are: spaces, tabs.');
