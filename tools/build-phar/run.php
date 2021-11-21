#!/usr/bin/env php
<?php

use \Smuuf\Primi\Helpers\Colors;

if (!extension_loaded('phar')) {
	die("Cannot create Phar files. Phar extension not enabled");
}

if (!\Phar::canWrite()) {
	die("Cannot create Phar files: 'phar.readonly' must be set to 0");
}

// Composer's autoload.
require __DIR__ . '/../../vendor/autoload.php';

define('ROOT_DIR', dirname(__DIR__, 2));
chdir(ROOT_DIR);

const TARGET_PHAR = ROOT_DIR . '/build/primi.phar';
const APP_DIR = ROOT_DIR . '/src';
$tempDir = ROOT_DIR . '/temp/_builder-phar';

print_header();

info("Removing existing Phar ...");
shell_exec(sprintf("rm %s 2>/dev/null", TARGET_PHAR));

info("Copying files to temporary directory ...");
shell_exec("mkdir $tempDir 2>/dev/null");
shell_exec("rm -rf $tempDir/*");
shell_exec("cp --preserve -r ./primi ./src ./composer.* $tempDir/");

info("Removing first line (shebang) from Primi entrypoint ...");
shell_exec("tail -n +2 '$tempDir/primi' > '$tempDir/primi.tmp' && mv '$tempDir/primi.tmp' '$tempDir/primi'");

info("Installing Composer dependencies ...");
shell_exec("composer install -q -o --no-dev -d $tempDir");

info("Building Phar ...");
$p = new Phar(TARGET_PHAR);
$p->startBuffering();
$p->buildFromDirectory($tempDir . "/");
$p->compressFiles(\Phar::GZ);
$p->setStub(get_stub());
$p->stopBuffering();

info("Finishing up ...");
exec(sprintf("chmod +x %s", TARGET_PHAR)); // Mark as executable.
info("Done.");

function get_stub() {

	$date = new \DateTime('now', new \DateTimeZone('UTC'));
	$datetime = $date->format('r');

	return <<<STUB
#!/usr/bin/env php
<?php
const COMPILED_AT = "$datetime";
if (extension_loaded('phar')) {
	set_include_path('phar://' . __FILE__ . '/' . get_include_path());
	Phar::mapPhar(basename(__FILE__));
	include 'phar://' . __FILE__ . '/primi';
	die;
} else {
	die("Cannot execute Phar archive. Phar extension is not enabled.");
}
__HALT_COMPILER();
STUB;

}

function info(string $string) {
	$date = date('Y-m-d H:i:s');
	echo Colors::get("{darkgrey}[{$date}]{_} {$string}\n");
}

function print_header(): void {

	$php = PHP_VERSION;
	$string = "Phar Builder: Primi Phar Builder\n"
		. "{yellow}Running on PHP {$php}{_}\n";

	echo Colors::get($string);

}
