<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::same("<p>Short text conected with another long text closed by a DOT.</p>", $o->compileContent("p Short text \n\tconected with \n\tanother long text \n\tclosed by a DOT.")); 
Assert::same("<p>Just-<b>short</b>-text.</p>", $o->compileContent("p Just-\n\tb short\n\t-text."));
Assert::same('<p>Long text with a<a href="http://www.google.com" target="blank"><b><i>bold link</i>on Google</b></a></p>', $o->compileContent("p Long text with a\n\t".'a $http://www.google.com $blank'."\n\t\tb\n\t\t\ti bold link\n\t\t\ton Google")); 
Assert::same('<p>Short text with a <b class="boldtext">bold text that have an <i>"italic" word</i></b> on the same line.</p>', $o->compileContent('p Short text with a <b class="boldtext">bold text that have an <i>"italic" word</i></b> on the same line.'));
