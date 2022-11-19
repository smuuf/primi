#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$emptyList = [];
$nonEmptyList = range(1, 100);
$boolTrue = true;
$boolFalse = false;

$bench->addBench(function() use ($emptyList) {
	$x = 0;
	if ($emptyList) {
		$x += 1;
	} else {
		$x += 1;
	}
});

$bench->addBench(function() use ($nonEmptyList) {
	$x = 0;
	if ($nonEmptyList) {
		$x += 1;
	} else {
		$x += 1;
	}
});

$bench->addBench(function() use ($boolTrue) {
	$x = 0;
	if ($boolTrue) {
		$x += 1;
	} else {
		$x += 1;
	}
});

$bench->addBench(function() use ($boolFalse) {
	$x = 0;
	if ($boolFalse) {
		$x += 1;
	} else {
		$x += 1;
	}
});

$bench->run(1e7);
