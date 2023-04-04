<?php

use Tester\Assert;

use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Handlers\HandlerFactory;

require __DIR__ . '/../bootstrap.php';

// Existing handler.
$h = HandlerFactory::getFor('Program');
Assert::true(is_a($h, \Smuuf\Primi\Handlers\Handler::class, true));

// Existing handler.
$h = HandlerFactory::tryGetFor('Program');
Assert::true(is_a($h, \Smuuf\Primi\Handlers\Handler::class, true));

// Non-existent handler.
Assert::exception(
	fn() => HandlerFactory::getFor('__some_nonexistent_handler__'),
	EngineInternalError::class,
	"Handler class for '__some_nonexistent_handler__' not found",
);

// Non-existent handler.
Assert::null(HandlerFactory::tryGetFor('__some_nonexistent_handler__'));
