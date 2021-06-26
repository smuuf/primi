<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$bench->addBench(function() {

	$res = [];
	$res[] = $x = 123 + 456;
	$res[] = $x * 789;
	$res[] = $x / 1024;
	$res[] = $x - 1024;
	$res[] = $x * 123456789;
	$res[] = $x * 987654321;

	return array_map('intval', $res);

});

$bench->addBench(function() {

	$res = [];
	$res[] = $x = "123" + "456";
	$res[] = $x * "789";
	$res[] = $x / "1024";
	$res[] = $x - "1024";
	$res[] = $x * "123456789";
	$res[] = $x * "987654321";

	return array_map('intval', $res);

});

$bench->addBench(function() {

	$res = [];
	$res[] = $x = \bcadd("123", "456");
	$res[] = \bcmul($x, "789");
	$res[] = \bcdiv($x, "1024");
	$res[] = \bcsub($x, "1024");
	$res[] = \bcmul($x, "123456789");
	$res[] = \bcmul($x, "987654321");

	return array_map('intval', $res);

});

$bench->addBench(function() {

	$res = [];
	$res[] = $x = \gmp_add("123", "456");
	$res[] = \gmp_mul($x, "789");
	$res[] = \gmp_div($x, "1024");
	$res[] = \gmp_sub($x, "1024");
	$res[] = \gmp_mul($x, "123456789");
	$res[] = \gmp_mul($x, "987654321");

	return array_map('intval', $res);

});

$bench->run(1000000);
