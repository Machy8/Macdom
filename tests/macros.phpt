<?php

use Tester\Assert;

require 'bootstrap.php';

Assert::same('<meta name="viewport" content="width=device-width" />', $o->compileContent("viewport width=device-width"));
Assert::same('<link rel="shortcut icon" href="favicon.ico" />', $o->compileContent("favicon favicon.ico"));
Assert::same('<link rel="shortcut icon" href="+=´´)ú¨§ů-.,-*/+_:\\()[]@&|\#" />', $o->compileContent("favicon +=´´)ú¨§ů-.,-*/+_:\\()[]@&|\#"));
Assert::same('<meta name="twitter:author" content="@8machy" />', $o->compileContent("tw author @8machy"));