<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

define('SOME_CONST', md5(random_bytes(100)));

class Something {
	public function method(): string {
		return md5('whatever');
	}
}

$obj = new Something;

$bench->addBench(function() use ($obj) {
	return SOME_CONST . '.' . $obj->method();
});

$bench->addBench(function() use ($obj) {
	return SOME_CONST . ".{$obj->method()}";
});

// Add more benchmarks...

// Run the benchmark (with default number of iterations)
$bench->run(1e7);
