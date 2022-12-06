<?php

use \Tester\Assert;

use \Smuuf\Primi\Handlers\KnownHandlers;
use \Smuuf\Primi\Handlers\HandlerFactory;

require __DIR__ . '/../bootstrap.php';

// Existing handler, strict mode.
$h = HandlerFactory::getFor(KnownHandlers::fromName('Program'));
Assert::true(is_a($h, \Smuuf\Primi\Handlers\Handler::class, true));
