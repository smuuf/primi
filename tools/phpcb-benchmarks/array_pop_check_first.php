#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\SerialEngine);

$arrayFilled = [1, 2, 3, 4, 5];
$arrayEmpty = [];

// Was the winner.
$bench->addBench(function() use ($arrayEmpty) {

	if ($arrayEmpty) {
		return array_pop($arrayEmpty);
	}

	return null;

});

$bench->addBench(function() use ($arrayEmpty) {
	return array_pop($arrayEmpty);
});

$bench->addBench(function() use ($arrayFilled) {

	if ($arrayFilled) {
		return array_pop($arrayFilled);
	}

	return null;

});

$bench->addBench(function() use ($arrayFilled) {
	return array_pop($arrayFilled);
});

$bench->run(1e7);
