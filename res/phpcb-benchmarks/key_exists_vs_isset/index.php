<?php

require __DIR__ . "/../../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$arr = ["key" => 1];
$arr2 = ["foo" => 1];
$arr3 = ["key" => null];

$bench->addBench(function() use ($arr) {
	if (!isset($arr['key'])) {
		$x = array_key_exists("key", $arr);
	}
});

$bench->addBench(function() use ($arr2) {
	if (!isset($arr2['key'])) {
		$x = array_key_exists("key", $arr2);
	}
});

$bench->addBench(function() use ($arr3) {
	if (!isset($arr3['key'])) {
		$x = array_key_exists("key", $arr3);
	}
});

$bench->addBench(function() use ($arr) {
	$x = array_key_exists("key", $arr);
});

$bench->addBench(function() use ($arr2) {
	$x = array_key_exists("key", $arr2);
});

$bench->addBench(function() use ($arr3) {
	$x = array_key_exists("key", $arr3);
});

$bench->run();
