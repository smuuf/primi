<?php

use \Tester\Assert;

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Code\SourceFile;

require __DIR__ . '/../bootstrap.php';

define('ROOT_DIR', realpath(__DIR__ . '/../../'));

function run_primi_files(string $glob, ?string $importPath): void {

	$config = Config::buildDefault();;
	$config->setTempDir(null);

	if ($importPath) {
		$config->addImportPath($importPath);
	}

	foreach (glob($glob) as $path) {

		$path = realpath($path);
		$shortpath = str_replace(ROOT_DIR, '', $path);
		echo "Running: $shortpath ...\n";

		Assert::noError(function() use ($path, $config) {
			run_primi_file($path, $config);
		});

	}


}

function run_primi_file(string $path, Config $config): void {

	$interpreter = new \Smuuf\Primi\Interpreter($config);
	$source = new SourceFile($path);
	$interpreter->run($source);

}
