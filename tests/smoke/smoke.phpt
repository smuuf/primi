<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

const ROOT_DIR = __DIR__ . '/../../';

// During Tester run, this will be false, so Primi files parsing and AST
// preprocessing will be tested (and included in coverage), too.
$useAstCache = (bool) getenv('X_PRIMI_TESTS_ASTCACHE');

function run_primi_source(string $source) {

	global $useAstCache;
	$cachePath = $useAstCache
		? ROOT_DIR . "/temp/"
		: null;

	$interpreter = new \Smuuf\Primi\Interpreter(null, $cachePath);

	// Run interpreter
	$interpreter->run($source);

}

foreach (glob(ROOT_DIR . '/example/*.primi') as $path) {

	$path = realpath($path);
	echo "Running: $path ...\n";

	Assert::noError(function() use ($path) {
		$source = file_get_contents($path);
		run_primi_source($source);
	});

}
