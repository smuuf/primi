#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\SerialEngine);

// Was the winner.
$bench->addBench(function() {

	$c = 0;
	$z = null;

	if (!$z) {
		$c++;
	}

});

$bench->addBench(function() {

	$c = 0;
	$z = null;

	if ($z === null) {
		$c++;
	}

});

$bench->addBench(function() {

	$c = 0;
	$z = true;

	if ($z) {
		$c++;
	}

});

$bench->addBench(function() {

	$c = 0;
	$z = true;

	if ($z === true) {
		$c++;
	}

});

$bench->run(1e6);
