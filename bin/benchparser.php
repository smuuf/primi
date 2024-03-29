#!/usr/bin/env php
<?php

// NOTE: Score is measured against the speed of some arbitrary PHP code that is
// used to measure an approximate machine speed.
// Therefore, the final "score" number is just a scalar value without any
// meaning other than that it could be compared against other "score" values
// measured the same way.

const SOURCE_PATH = __DIR__ . '/../tests/bench/bench_parser.primi';
const MULTI_COUNT = 10;

// Composer's autoload.
require __DIR__ . "/../vendor/autoload.php";

error_reporting(E_ALL);

$primiSource = file_get_contents(SOURCE_PATH);

// Measure speed of PHP itself on current machine, so we can measure Primi's
// parser speed against some "standard value". The measured PHP code is some
// pretty arbitrary code, by the way.
function machine_standard() {

	global $primiSource;

	$bench = function() use ($primiSource) {

		for ($x = 0; $x++ < 100;) { // Not MULTI_COUNT on purpose.

			preg_match_all('#[a-z\s]+#u', $primiSource, $m, PREG_SET_ORDER);
			$result = [];

			foreach ($m as $v) {
				$v = trim($v[0]);
				$int = (int) $v;
				$str = (string) $v;
				$bool = (bool) $v;
				$tmpA = $int + (is_numeric($str) ? $str : 1);
				$tmpB = $str . $int;
				$result[] = $bool ? [$tmpA, $tmpB] : [$tmpB, $tmpA];
			}

		}

	};

	return timer('Machine', $bench, MULTI_COUNT);

}

/**
 * Run the function passed as the second argument N times, while displaying some
 * kind of "pretty progress output".
 */
function timer(string $name, callable $fn, int $count = 1) {
	$start = microtime(true);
	$c = 0;
	echo "Measuring: $name ";
	while ($c++ < $count) {
		$fn();
		echo ".";
	}
	echo "\n";
	return microtime(true) - $start;
}

// Parser callable.
$bench = function() use ($primiSource) {

	$parser = new \Smuuf\Primi\Code\AstProvider;
	$source = new \Smuuf\Primi\Code\Source($primiSource);
	return $parser->getAst($source);

};

$results = [
	'machine_std' => machine_standard(),
	'first_avg' => timer('First parse', $bench),
	'multi_avg' => timer('Multi parse', $bench, MULTI_COUNT) / MULTI_COUNT,
];

printf(
	"PHP perf standard: %.4f s\n",
	$results['machine_std']
);

// First parse will be probably always slower, when parsing involves finding
// and loading handler classes for each PEG rule, because each handler can
// provide its own node reduction logic.
printf(
	"First parse: %.4f s, score: %.4f (↑ better)\n",
	$results['first_avg'],
	$results['machine_std'] / $results['first_avg'] * 100
);

printf(
	"Multi parse: %.4f s, score: %.4f (↑ better)\n",
	$results['multi_avg'],
	$results['machine_std'] / $results['multi_avg'] * 100
);

printf(
	"First parse: %.4f %% of multi parse\n",
	$results['first_avg'] / $results['multi_avg'] * 100
);
