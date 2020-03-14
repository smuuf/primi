#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\ChaoticEngine);

// Was the winner.
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

$bench->run(1e8);
