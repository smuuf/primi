<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$bench->addBench(function() {

	$x = 123 + 456;
	$x = $x * 789;
	$x = $x / 1024;
	$x = $x - 1024;
	$x = $x * 123456789;
	$x = $x * 987654321;

});

$bench->addBench(function() {

	$x = "123" + "456";
	$x = $x * "789";
	$x = $x / "1024";
	$x = $x - "1024";
	$x = $x * "123456789";
	$x = $x * "987654321";

});

$bench->addBench(function() {

	$x = \bcadd("123", "456");
	$x = \bcmul($x, "789");
	$x = \bcdiv($x, "1024");
	$x = \bcsub($x, "1024");
	$x = \bcmul($x, "123456789");
	$x = \bcmul($x, "987654321");

});

$bench->addBench(function() {

	$x = \gmp_add("123", "456");
	$x = \gmp_mul($x, "789");
	$x = \gmp_div($x, "1024");
	$x = \gmp_sub($x, "1024");
	$x = \gmp_mul($x, "123456789");
	$x = \gmp_mul($x, "987654321");

});

$bench->run(1000000);
