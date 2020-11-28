<?php

use \Tester\Assert;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Interpreter;

require __DIR__ . '/../bootstrap.php';

//
// Context events.
//

$i = new Interpreter;
$ctx = $i->getContext();

$src = <<<SRC
result = 0
for (num in [10, 20, 30]) {
	for (_ in range(1, num)) {
		result = result + 1
	}
}
SRC;

$i->run($src);
Assert::same('60', $ctx->getVariable('result')->getInternalValue());

Assert::exception(function() use ($i, $ctx, $src) {

	// Add SIGINT event to event queue.
	$ctx->addEvent('SIGINT');

	// SimpleHandler::run() will raise exception based on the SIGINT event.
	$i->run($src);

}, RuntimeError::class, '#Received SIGINT#');
