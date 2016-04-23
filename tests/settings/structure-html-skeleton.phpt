<?php

use Tester\Assert;

require '../bootstrap.php';

$testedA = file_get_contents("../test-templates/structure-html-skeleton-a.html");
$resultA = file_get_contents("../test-templates/structure-html-skeleton-b.html");

$testedB = file_get_contents("../test-templates/structure-html-skeleton2-a.html");
$resultB = file_get_contents("../test-templates/structure-html-skeleton2-b.html");

Assert::same($resultA, $o->compileContent($testedA));

$o->setup->structureHtmlSkeleton = FALSE;

Assert::same($resultB, $o->compileContent($testedB));
