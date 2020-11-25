<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\SerialEngine);

const FLAG_C_BOOL = true;

define('FLAG_A_INT', 1);
define('FLAG_B_BOOL', true);

abstract class Flagger {

	public static $flagAInt = 1;
	public static $flagBBool = true;

}

$bench->addBench(function() {

	if (FLAG_B_BOOL) {
		$x = 1;
	}

	if (!FLAG_B_BOOL) {
		$x = 2;
	}

	return $x;

});

$bench->addBench(function() {

	if (FLAG_A_INT) {
		$x = 1;
	}

	if (!FLAG_A_INT) {
		$x = 2;
	}

	return $x;

});

$bench->addBench(function() {

	if (FLAG_C_BOOL) {
		$x = 1;
	}

	if (!FLAG_C_BOOL) {
		$x = 2;
	}

	return $x;

});

$bench->addBench(function() {

	if (Flagger::$flagAInt) {
		$x = 1;
	}

	if (!Flagger::$flagAInt) {
		$x = 2;
	}

	return $x;

});

$bench->addBench(function() {

	if (Flagger::$flagBBool) {
		$x = 1;
	}

	if (!Flagger::$flagBBool) {
		$x = 2;
	}

	return $x;

});

$bench->run(10000000);
