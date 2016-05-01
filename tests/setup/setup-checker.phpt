<?php

use Tester\Assert;
use Machy8\Macdom\Setup\SetupChecker;
use Machy8\Macdom\Setup\Setup;
require '../bootstrap.php';

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->setup->addBooleanAttributes = TRUE;
	$checker->check($s->setup);
}, 'Exception', 'Variable $addBooleanAttributes must have a STRING value.');

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->setup->addElements = TRUE;
	$checker->check($s->setup);
}, 'Exception', 'Variable $addElements must have an ARRAY value.');

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->setup->booleansWithValue = "";
	$checker->check($s->setup);
}, 'Exception', 'Variable $booleansWithValue must have a BOOLEAN value.');

Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->setup->indentMethod = 10;
	$checker->check($s->setup);
}, 'Exception', 'Variable $indentMethod has illegal value. Options are: 1, 2, 3.');


Assert::exception(function(){
	$s = new Setup;
	$checker = new SetupChecker($s);
	$s->setup->outputIndentation = "hamburger";
	$checker->check($s->setup);
}, 'Exception', 'Variable $outputIndentation has illegal value. Options are: spaces, tabs.');
