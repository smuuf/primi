<?php

use \Tester\Assert;

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\ErrorException;
use \Smuuf\Primi\Tasks\Types\PosixSignalTask;

require __DIR__ . '/../bootstrap.php';

//
// Context events.
//

$i = new Interpreter;

$src = <<<SRC
result = 0
for (num in [10, 20, 30]) {
	for (_ in range(num)) {
		result = result + 1
	}
}
SRC;

$mainScope = $i->run($src);
$ctx = $i->getLastContext();

Assert::same('60', $mainScope->getVariable('result')->getInternalValue());

Assert::exception(function() use ($i, $ctx, $src) {
	// Add SIGINT event to event queue.
	$ctx->getTaskQueue()->addTask(new PosixSignalTask(SIGINT));
	// Exception will be thrown based on the simulated "SIGINT" signal task job.
	$i->run($src);
}, ErrorException::class, '#Received SIGINT#');

Assert::exception(function() use ($i, $ctx, $src) {
	$ctx->getTaskQueue()->addTask(new PosixSignalTask(SIGTERM));
	$i->run($src);
}, ErrorException::class, '#Received SIGTERM#');
