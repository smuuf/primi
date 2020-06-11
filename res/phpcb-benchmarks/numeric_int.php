<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$data = [false, '+1', '-1', +1, 0, 1, 42, -1024, 0.2, -0.2, -0.7, 0.7, true, "ahoj", "0.1", "-4.2", "-4.7", "-1000000", "false", "75", "125"];

$bench->addBench(function() use ($data) {
	$result = [];
	foreach ($data as $x) {
		$result[] = (bool) preg_match("#^[+-]?\d+$#", $x);
	}
	return $result;
});

$bench->addBench(function() use ($data) {
	$result = [];
	foreach ($data as $x) {
		$result[] = (string) (int) $x === (string) \ltrim($x, "+");
	}
	return $result;
});

$bench->addBench(function() use ($data) {
	$result = [];
	foreach ($data as $x) {
		$result[] = \ctype_digit(\ltrim($x, "+-"));
	}
	return $result;
});

$bench->run(5e5);
