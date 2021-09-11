<?php

use \Tester\Assert;

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Code\SourceFile;

require __DIR__ . '/../bootstrap.php';

define('ROOT_DIR', realpath(__DIR__ . '/../../'));

$config = Config::buildDefault();;
$config->setTempDir(null);
$config->addImportPath(ROOT_DIR . '/example/');

function run_primi_file(string $path) {

	global $config;
	$interpreter = new \Smuuf\Primi\Interpreter($config);

	// Run interpreter
	$source = new SourceFile($path);
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
