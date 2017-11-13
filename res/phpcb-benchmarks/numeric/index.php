<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

$data = [false, 0, 1, 42, -1024, "ahoj", "0.1", "-4.2", "-1000000", "false", "75", "125"];

$bench->addBench(function() use ($data) {
	foreach ($data as $x) {
		preg_match("#\d+#", $x);
	}
});

$bench->addBench(function() use ($data) {
	foreach ($data as $x) {
		(string) (int) $x === (string) $x;
	}
});

$bench->run();
