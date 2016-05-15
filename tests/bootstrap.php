<?php

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Loaders/Loader.php';

const TEMPLATES = __DIR__.'/test-templates';

$o = new Machy8\Macdom\Loaders\Loader;
$o->setup->structureHtmlSkeleton = FALSE;

function rewriteTest($testName, $loader) {
	if(file_exists(TEMPLATES.'/'.$testName.'-b.html') && file_exists(TEMPLATES.'/'.$testName.'-a.html')) {
		file_put_contents(TEMPLATES . '/' . $testName . "-b.html", $loader->compile(file_get_contents(TEMPLATES . '/' . $testName . '-a.html')));
	}
}
