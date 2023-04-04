<?php

use Tester\Assert;

use Smuuf\Primi\Interpreter;
use Smuuf\Primi\Tasks\Types\PosixSignalTask;

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

$interpreterResult = $i->run($src);
$mainScope = $interpreterResult->getScope();
$ctx = $interpreterResult->getContext();

Assert::same('60', $mainScope->getVariable('result')->getCoreValue());

assert_piggyback_exception(
	function() use ($i, $ctx, $src) {
		// Add SIGINT event to event queue.
		$ctx->getTaskQueue()->addTask(new PosixSignalTask(SIGINT));
		// Exception will be thrown based on the simulated "SIGINT" signal task job.
		$i->run($src, context: $ctx);
	},
	'SystemException',
	'#Received SIGINT#i',
);

assert_piggyback_exception(
	function() use ($i, $ctx, $src) {
		$ctx->getTaskQueue()->addTask(new PosixSignalTask(SIGTERM));
		$i->run($src, context: $ctx);
	},
	'SystemException',
	'#Received SIGTERM#i',
);
