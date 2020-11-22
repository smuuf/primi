#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\ChaoticEngine);

$bench->addBench(function() {
	$c = 0;
	$bool = true;
	if ($bool) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$bool = true;
	if ($bool === true) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$string = '0';
	if ($string) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$string = '1';
	if ($string) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$string = '';
	if ($string) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$string = 'X';
	if ($string) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$int = 0;
	if ($int) {
		$c++;
	}
});

$bench->addBench(function() {
	$c = 0;
	$int = 1;
	if ($int) {
		$c++;
	}
});


$bench->run(1e6);
