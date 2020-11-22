<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

class SomeClass {

	static function doStuff($a, $b, $c = null) {
		$x = $a . $b . $c;
		if ($x[0] == $a) {
			$x = strrev($x);
		}
		return $x;
	}

}

function do_stuff($a, $b, $c = null) {
	$x = $a . $b . $c;
	if ($x[0] == $a) {
		$x = strrev($x);
	}
	return $x;
}

$bench->addBench(function() {
	$x = do_stuff(1, 2, 3);
});

$bench->addBench(function() {
	$x = SomeClass::doStuff(1, 2, 3);
});

$bench->run(2000000);
