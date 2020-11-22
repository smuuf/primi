<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$arr = array_combine(range(0, 50000), range(0, 50000));

function gen() {

	global $arr;

	foreach ($arr as $k => $v) {
		yield $k => $v;
	}

}

$bench->addBench(function() {

	$result = [];
	foreach (gen() as $k => $v) {
		$result[$k] = $v;
	}
	return $result;

});

$bench->addBench(function() use ($arr) {

	$result = [];
	foreach (new \ArrayIterator($arr) as $k => $v) {
		$result[$k] = $v;
	}
	return $result;

});

$bench->run(10000);
