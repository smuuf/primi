<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

function test_dir(string $dir) {

	foreach (glob(__DIR__ . sprintf("/suites/%s/*.primi", $dir)) as $file) {

		run_test($file);

		// Avoid excessive RAM usage when gathering code coverage.
		// (PHPDBG is quite greedy.)
		\Tester\CodeCoverage\Collector::flush();
		gc_collect_cycles();

	}

}

function run_test($file) {

	$context = new \Smuuf\Primi\Context;
	$interpreter = new \Smuuf\Primi\Interpreter($context);
	$outputFile = dirname($file) . "/" . basename($file, ".primi") . ".expect";

	ob_start();

	try {

		// Run interpreter
		$interpreter->run(file_get_contents($file));

	} catch (\Smuuf\Primi\ErrorException $e) {

		printf("EX:%s\n", get_class($e));

	} finally {

		$vars = $context->getVariables();
		array_walk($vars, function($x, $k) {
			printf("%s:%s:%s\n", $k, get_class($x), return_string_value($x->getPhpValue()));
		});

	}

	$output = normalize(ob_get_clean());
	$expected = normalize(file_get_contents($outputFile));

	Assert::same($expected, $output);

}

function normalize(string $string) {
	return preg_replace('~\r\n?~', "\n", $string);
}

function return_string_value($value) {

	if (is_array($value)) {
		$return = "[";
		foreach ($value as $key => $item) {
			$return .= sprintf("%s:%s,", $key, return_string_value($item->getPhpValue()));
		}
		$return = rtrim($return, ',') . "]";
	} elseif (is_bool($value)) {
		$return = $value ? 1 : 0;
	} else {
		$return = (string) $value;
	}

	return $return;

}
