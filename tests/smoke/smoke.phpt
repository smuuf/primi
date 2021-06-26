<?php

use Smuuf\Primi\Source;
use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

define('ROOT_DIR', realpath(__DIR__ . '/../../'));

// During Tester run, this will be false, so Primi files parsing and AST
// preprocessing will be tested (and included in coverage), too.
$useAstCache = (bool) getenv('X_PRIMI_TESTS_ASTCACHE');

function run_primi_file(string $path) {

	global $useAstCache;
	$cachePath = $useAstCache
		? ROOT_DIR . "/temp/"
		: null;

	$interpreter = new \Smuuf\Primi\Interpreter(null, $cachePath);

	// Run interpreter
	$source = new Source($path, true);
	$interpreter->run($source);

}

foreach (glob(ROOT_DIR . '/example/*.primi') as $path) {

	$path = realpath($path);
	$shortpath = str_replace(ROOT_DIR, '', $path);
	echo "Running: $shortpath ...\n";

	Assert::noError(function() use ($path) {
		run_primi_file($path);
	});

}
