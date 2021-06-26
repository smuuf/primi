<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$arr =  ['x' => 1, 'y' => 2, 'z' => false, "key" => 1, 'a' => 'b', 'c' => 0];
$arr2 = ['x' => 1, 'y' => 2, 'z' => false, "foo" => 1, 'a' => 'b', 'c' => 0];
$arr3 = ['x' => 1, 'y' => 2, 'z' => false, "key" => null, 'a' => 'b', 'c' => 0];

$bench->addBench(function() use ($arr) {
	$x = isset($arr['key']);
});

$bench->addBench(function() use ($arr2) {
	$x = isset($arr2['key']);
});

$bench->addBench(function() use ($arr3) {
	$x = isset($arr3['key']);
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
