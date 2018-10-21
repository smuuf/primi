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

function run_test(string $file) {

	$context = new \Smuuf\Primi\Context;
	$interpreter = new \Smuuf\Primi\Interpreter($context);
	$outputFile = dirname($file) . "/" . basename($file, ".primi") . ".expect";

	$src = normalize(file_get_contents($file));
	$options = parse_pragmas($src);

	ob_start();

	// Run interpreter. Do whole source at once or one-line-at-a-time,
	// based on the "one_liners" pragma option.

	if (empty($options["one_liners"])) {

		try {
			$interpreter->run($src);
		} catch (\Smuuf\Primi\ErrorException $e) {
			printf("EX:%s\n", get_class($e));
		}

		$vars = $context->getVariables();
		array_walk($vars, function($x, $k) {
			printf("%s:%s:%s\n", $k, main_class($x), $x->getStringValue());
		});

	} else {

		$lines = explode("\n", $src);
		foreach ($lines as $line) {
			try {
				$context->reset();
				$interpreter->run($line);
				$vars = $context->getVariables();
				array_walk($vars, function($x, $k) {
					printf("%s:%s:%s\n", $k, main_class($x), $x->getStringValue());
				});
			} catch (\Smuuf\Primi\ErrorException $e) {
				printf("EX:%s\n", get_class($e));
			}
		}

	}

	$output = normalize(ob_get_clean());
	$expected = normalize(file_get_contents($outputFile));

	Assert::same($expected, $output);

}

function normalize(string $string) {
	return trim(preg_replace('~\r?\n~', "\n", $string));
}

function main_class($instance) {
	return basename(str_replace('\\', '/', get_class($instance)));
}

function parse_pragmas(string $src) {

	$options = [
		"one_liners" => false,
	];

	$lines = explode("\n", $src);
	foreach ($lines as $line) {
		if (preg_match("~^// pragma:(.*)$~", $line, $m)) {

			$name = $m[1];

			if ($name === "one_liners") {
				$options["one_liners"] = true;
			}

		}
	}

	return $options;

}
