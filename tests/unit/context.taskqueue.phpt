<?php

use \Tester\Assert;

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Tasks\Types\PosixSignalTask;

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
	$ctx->getTaskQueue()->addTask(new PosixSignalTask(SIGINT));
	// Exception will be thrown based on the simulated "SIGINT" signal task job.
	$i->run($src);
}, RuntimeError::class, '#Received SIGINT#');

Assert::exception(function() use ($i, $ctx, $src) {
	$ctx->getTaskQueue()->addTask(new PosixSignalTask(SIGTERM));
	$i->run($src);
}, RuntimeError::class, '#Received SIGTERM#');
