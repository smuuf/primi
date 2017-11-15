<?php

use \Tester\Assert;
use \Smuuf\Primi\HandlerFactory;

require __DIR__ . '/bootstrap.php';

// Existing handler, strict mode.
$h = HandlerFactory::get('program');
Assert::true(is_a($h, \Smuuf\Primi\Handlers\IHandler::class, true));

// Existing handler, non-strict mode.
$h = HandlerFactory::get('program', false);
Assert::true(is_a($h, \Smuuf\Primi\Handlers\IHandler::class, true));

// Non-existing handler, strict mode. Should throw exception.
Assert::exception(function() {
    $h = HandlerFactory::get('nonexisting_handler');
}, \LogicException::class);

// Non-existing handler, no strict mode. Should return false.
Assert::false(HandlerFactory::get('nonexisting_handler', false));
