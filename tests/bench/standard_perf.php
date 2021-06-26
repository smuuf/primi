<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
const MAX_ITER = 50000;

//
// Run some CPU intensive tasks and measure their duration in pure PHP.
//

function measure($fn) {
	$start = microtime(true);
	$fn();
	return microtime(true) - $start;
}

function bench_function_calls() {

	$adder = function($x, $y) {
		return $x + $y;
	};

	$result = -1024;
	$c = 0;
	while ($c < MAX_ITER) {
		$result = $result + $adder($c, 1);
		$c = $c + 1;
	}

	return $c;

}

function bench_regex_matches() {

	$haystack = "Když začínáme myslet, nemáme k dispozici nic jiného než myšlenku v " .
				"její čisté neurčenosti, neboť k určení již patří jedno nebo nějaké " .
				"jiné, ale na začátku ještě nemáme žádné jiné...";

	$result = 0;
	$c = 0;
	while ($c < MAX_ITER) {
		$result += preg_match('#^.*(zač).*(,)?.*?(\.)$#', $haystack, $m);
		$c = $c + 1;
	}

	return $c;

}

function bench_dicts() {

	$c = 0;
	$result = [];

	while ($c < MAX_ITER) {

		$dict = [
			'a' => true,
			'b' => false,
			'c' => null,
			'd' => 'áhojky, plantážníku!',
			'keys' => ['a', 'b', 'c'],
		];

		foreach ($dict['keys'] as $name) {
			$result[] = $dict[$name];
		}

		$c = $c + 1;

	}

	return $result;

}

$results = [
	'bench_function_calls' => measure('bench_function_calls'),
	'bench_regex_matches' => measure('bench_regex_matches'),
	'bench_dicts' => measure('bench_dicts'),
];

//
// Print results in INI format for easy parsing, if needed.
//

$total = 0;
foreach ($results as $name => $time) {
	echo "$name=$time\n";
	$total += $time;
}
echo "total=$total\n";
