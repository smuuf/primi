#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\SerialEngine);

$hugeArr = array_fill(0, 1_000_000, false);
$bigArr = array_fill(0, 100_000, false);
$smallArr = array_fill(0, 100, false);

$bench->addBench(function() use ($smallArr) {

	$result = [];
	foreach ($smallArr as $k => $_) {
		$result[] = $k;
	}

	return $result;

});

$bench->addBench(function() use ($bigArr) {

	$result = [];
	foreach ($bigArr as $k => $_) {
		$result[] = $k;
	}

	return $result;

});

$bench->addBench(function() use ($hugeArr) {

	$result = [];
	foreach ($hugeArr as $k => $_) {
		$result[] = $k;
	}

	return $result;

});

$bench->addBench(function() use ($smallArr) {

	$result = [];
	foreach (array_keys($smallArr) as $k) {
		$result[] = $k;
	}

	return $result;

});

$bench->addBench(function() use ($bigArr) {

	$result = [];
	foreach (array_keys($bigArr) as $k) {
		$result[] = $k;
	}

	return $result;

});

$bench->addBench(function() use ($hugeArr) {

	$result = [];
	foreach (array_keys($hugeArr) as $k) {
		$result[] = $k;
	}

	return $result;

});

$bench->run(100);
