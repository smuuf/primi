#!/usr/bin/php
<?php

// Composer's autoload.
require __DIR__ . "/vendor/autoload.php";

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(__DIR__ . "/temp/");
$loader->addDirectory(__DIR__ . "/src")->register();

$success = true;
foreach (glob(__DIR__ . "/tests/*.primi") as $file) {
	info("Running test '$file' ... ", false);
	$success &= run_test($file);
}

exit((int) !$success);

function run_test($file): bool {

	$context = new \Smuuf\Primi\Context;
	$interpreter = new \Smuuf\Primi\Interpreter($context);
	$outputFile = dirname($file) . "/" . basename($file, ".primi") . ".expect";

	ob_start();

		// Run interpreter
		$interpreter->run(file_get_contents($file));

		$vars = $context->getVariables();
		array_walk($vars, function($x, $k) {
			printf("%s:%s:%s\n", $k, get_class($x), $x->getPhpValue());
		});

	$output = ob_get_clean();

	$expected = @file_get_contents($outputFile);

	$expected = normalize($expected);
	$output = normalize($output);

	$lastExpectedFile = __DIR__ . '/last_expected.out';
	$lastActualFile = __DIR__ . '/last_actual.out';

	if ($expected !== $output) {
		info("FAIL", true, false);
		info("Diff:");
		info("", true, "░");
		file_put_contents($lastExpectedFile, $expected);
		file_put_contents($lastActualFile, $output);
		system("diff --unchanged-line-format='' --old-line-format='<(%dn) %L' --new-line-format='>(%dn) %L' $lastExpectedFile $lastActualFile");
		info("", true, "░");
		return false;
	} else {
		info("OK", true, false);
		return true;
	}

}

function info(string $string, $nl = true, $block = "█") {
	echo ($block ? "$block " : null) . $string . ($nl ? "\n" : null);
}

function normalize(string $string) {
	return preg_replace('~\r\n?~', "\n", $string);
}
