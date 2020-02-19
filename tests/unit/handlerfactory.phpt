<?php

use \Tester\Assert;
use \Smuuf\Primi\HandlerFactory;

require __DIR__ . '/../bootstrap.php';

// Existing handler, strict mode.
$h = HandlerFactory::get('program');
Assert::true(is_a($h, \Smuuf\Primi\Helpers\BaseHandler::class, true));
