<?php

require __DIR__ . '/../../vendor/autoload.php';

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(__DIR__ . "/../../temp/");
$loader->addDirectory(__DIR__ . "/../../src")->register();
