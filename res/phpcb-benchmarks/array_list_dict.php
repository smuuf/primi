#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\SerialEngine);

$arr = [
	[1, 2, 3, 4, 5, 6, 7, 8, 9],
	[1, 2, 3, 4, 5, 6, 7, 8, 9, 'c'],
	[1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm'],
	[1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm' => 4],
	['x' => 100, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm' => 4],
	range(0, 100000),
	array_merge(...[range(0, 10000), array_fill(50000, 10000, 'x')]),
	array_merge(...[range(0, 10000), ['a' => 1, 'b' => 2]]),
	[1, 2, 3, 4, 'c' => true, 6, 7, 8, 9],
];

function is_array_dict_A(array $input): bool {

	// Let's say that empty PHP array is not a dictionary.
	if (!$input) {
		return false;
	}

	return array_keys($input) !== range(0, count($input) - 1);

}

function is_array_dict_B(array $input): bool {

	// Let's say that empty PHP array is not a dictionary.
	if (!$input) {
		return false;
	}

	$length = count($input);
	for ($i = 0; $i < $length; $i++) {
		if (!array_key_exists($i, $input)) {
			return false;
		}
	}

	return true;

}

function is_array_dict_C(array $input): bool {

	// Let's say that empty PHP array is not a dictionary.
	if (!$input) {
		return false;
	}

	$c = 0;
	foreach ($input as $i => $_) {
		if ($c++ !== $i) {
			return false;
		}
	}

	return true;

}

$bench->addBench(function() use ($arr) {
	$results = [];
	foreach ($arr as $a) {
		$results[] = is_array_dict_A($a);
	}
	return $results;
});

$bench->addBench(function() use ($arr) {
	$results = [];
	foreach ($arr as $a) {
		$results[] = is_array_dict_B($a);
	}
	return $results;
});

$bench->addBench(function() use ($arr) {
	$results = [];
	foreach ($arr as $a) {
		$results[] = is_array_dict_C($a);
	}
	return $results;
});

$bench->run(1e3);
