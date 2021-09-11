<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark(new \Smuuf\Phpcb\ChaoticEngine);

const VALUES = [100, false, true, "ahoj", 0, "damn"];

function testA() {

	$val = VALUES[array_rand(VALUES)];

	if ($val === 100) {
		$x = 1;
	} elseif ($val === false) {
		$x = 1;
	} elseif ($val === true) {
		$x = 1;
	} elseif ($val === "ahoj") {
		$x = 1;
	} elseif ($val === 0) {
		$x = 1;
	} elseif ($val === "damn") {
		$x = 1;
	} else {
		$x = 0;
	}

	return $x;

};

function testB() {

	$val = VALUES[array_rand(VALUES)];

	switch ($val) {
		case 100:
			$x = 1;
			break;
		case false:
			$x = 1;
			break;
		case true:
			$x = 1;
			break;
		case "ahoj":
			$x = 1;
			break;
		case 0:
			$x = 1;
			break;
		case "damn":
			$x = 1;
			break;
		default:
			$x = 0;
			break;
	}

	return $x;

};

function testC() {

	$val = VALUES[array_rand(VALUES)];

	switch (\true) {
		case $val === 100:
			$x = 1;
			break;
		case $val === false:
			$x = 1;
			break;
		case $val === true:
			$x = 1;
			break;
		case $val === "ahoj":
			$x = 1;
			break;
		case $val === 0:
			$x = 1;
			break;
		case $val === "damn":
			$x = 1;
			break;
		default:
			$x = 0;
			break;
	}

	return $x;

};

function testD() {

	$val = VALUES[array_rand(VALUES)];

	switch (\true) {
		case $val == 100:
			$x = 1;
			break;
		case $val == false:
			$x = 1;
			break;
		case $val == true:
			$x = 1;
			break;
		case $val == "ahoj":
			$x = 1;
			break;
		case $val == 0:
			$x = 1;
			break;
		case $val == "damn":
			$x = 1;
			break;
		default:
			$x = 0;
			break;
	}

	return $x;

};

$bench->addBench('testA');
$bench->addBench('testB');
$bench->addBench('testC');
$bench->addBench('testD');
$bench->run(100000);
