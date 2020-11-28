#!/usr/bin/env php
<?php

declare(strict_types=1);

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Colors;

define('PRIMI_ROOT_DIT', realpath(__DIR__ . '/../..'));

// Composer's autoload.
require PRIMI_ROOT_DIT . '/vendor/autoload.php';

// Strict errors.
error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line) {
	throw new \ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);


// Helper functions.

function line($text = ''): void {
	echo "$text\n";
}

function out($text): void {
	echo $text;
}

function warn($text): void {
	global $warnings;
	$warnings[] = "$text";
}

function err($text): void {
	echo Colors::get("{-red}█ Error:{_} $text\n");
	die(1);
}

// Docgen.

line('█ DocGen: Primi Standard Library Docs Generator');
line();

array_shift($argv);

if ($argc !== 3) {
	line('Usage:');
	line('php ./docgen.php "<glob_path_to_PHP_files>" <path_to_result_markdown_file.md>');
	die;
}

$phpFilesGlob = $argv[0];
$outputFile = $argv[1];

$warnings = [];

// Polyfills for pre-PHP 8 versions.
if (!function_exists('str_ends_with')) {
	function str_starts_with(string $haystack, string $needle): bool {
		return \strncmp($haystack, $needle, \strlen($needle)) === 0;
	}
	function str_ends_with(string $haystack, string $needle): bool {
		return $needle === '' || $needle === \substr($haystack, -\strlen($needle));
	}
}

line(Colors::get("Parsing files at {cyan}$phpFilesGlob{_} ..."));
if (!$extensionFiles = glob($phpFilesGlob)) {
	err("No files found at $phpFilesGlob");
}

function get_relevant_methods(string $className): array {

	$classRef = new \ReflectionClass("\Smuuf\Primi\StdLib\\{$className}");

	// We want methods that are both public AND static AND non-PHP-magic.
	return array_filter(
		$classRef->getMethods(\ReflectionMethod::IS_STATIC),
		function ($ref) {
			return $ref->isPublic()
				&& \strpos($ref->getName(), '__') !== 0;
		}
	);

}

function clean_doc_whitespace(string $doc): string {

	// Unify NL
	$doc = preg_replace('#\r?\n#', "\n", $doc);
	// Remove "/**"-NL
	$doc = preg_replace('#^\/\*\*\s*\n#', '', $doc);
	// Remove NL-WHITESPACE-"*/"
	$doc = preg_replace('#\s*\*\/#', '', $doc);

	$doc = preg_replace('#^\s*\*\h*#m', '', $doc);

	$doc = trim($doc);

	return $doc;

}

function extract_params(\ReflectionMethod $methodRef): array {

	// For potential warning messages.
	$methodName = $methodRef->getName();
	$className = $methodRef->getDeclaringClass()->getName();

	// Extract parameter types. info.
	$params = [];
	if ($paramsRef = $methodRef->getParameters()) {
		foreach ($paramsRef as $paramRef) {

			$paramName = $paramRef->getName();

			$paramType = false;
			if ($paramTypeRef = $paramRef->getType()) {
				$paramType = $paramTypeRef->getName();
			}

			$isOptional = $paramRef->isOptional();

			$isValue = is_a($paramType, AbstractValue::class, true);
			$isCtx = is_a($paramType, Context::class, true);
			if (!$isValue && !$isCtx) {
				warn("Class '$className, method '$methodName', parameter '$paramName' does not hint Value|Context");
				continue;
			}

			// Context is automatically injected to functions that need it.
			// This has no place in function's signature. Skip this param type.
			if ($isCtx) {
				continue;
			}

			$params[] = [
				'type' => ($paramType)::TYPE,
				'name' => $paramName,
				'optional' => $isOptional,
			];

		}
	}

	return $params;

}

$data = [];
foreach ($extensionFiles as $filepath) {

	line(Colors::get("- File {cyan}$filepath{_}"));

	$filename = basename($filepath);
	$className = substr($filename, 0, strrpos($filename, '.'));

	$methods = get_relevant_methods($className);
	$data[$className] = [];

	foreach ($methods as $methodRef) {

		$methodName = $methodRef->getName();
		out(Colors::get("{darkgrey}  - Method{_} $className::$methodName{darkgrey} ... {_}"));

		$docComment = $methodRef->getDocComment();
		if (strpos($docComment ?: '', '@docgenSkip') !== false) {
			line(Colors::get("{lightyellow}Skipped via @docgenSkip"));
			continue;
		}

		$text = $docComment ? clean_doc_whitespace($docComment) : '';

		// Extract return type, which must be specified.
		$returnType = false;
		if ($returnTypeRef = $methodRef->getReturnType()) {
			try {
				$returnType = ($returnTypeRef->getName())::TYPE;
			} catch (\Throwable $e) {
				warn("Class '$className, method '$methodName', referencing non-existent Primi type having class " . $returnTypeRef->getName());
			}
		} else {
			warn("Class '$className, method '$methodName', return type does not hint Value|Context");
		}

		if (!$text) {
			line(Colors::get("{red}Missing or empty docstring"));
		} else {
			line(Colors::get("{green}OK"));
		}

		$data[$className][$methodName] = [
			'doctext' => $text,
			'parameters' => extract_params($methodRef) ?: false,
			'returns' => $returnType ?: false,
		];

	}

}

line('Building docs...');

function remove_doc_tags(string $text) {
	return preg_replace('#@[^\s]+#', '', $text);
}

function build_markdown(array $data): string {

	$fnBullet = "###";
	$md = '';

	// List extensions alphabetically.
	ksort($data);

	foreach ($data as $extName => $extData) {

		$md .= "## $extName\n";

		// List functions alphabetically.
		ksort($extData);

		foreach ($extData as $funcName => $funcData) {

			$parameters = [];
			if ($funcData['parameters']) {
				foreach ($funcData['parameters'] as $param) {
					$paramName = $param['name'];
					$paramType = $param['type'];

					$tmp = "_{$paramType}_ **`$paramName`**";
					if ($param['optional']) {
						$tmp = "*[$tmp]*";
					}

					$parameters[] = $tmp;

				}
			}

			$parameters = implode(', ', $parameters);
			$returnType = $funcData['returns'] ?: '';
			$md .= "$fnBullet **`$funcName`**($parameters) "
				. ($returnType ? " → _{$returnType}_" : "")
				. "\n\n";

			$doctext = $funcData['doctext'] ?? '' ?: '_Missing description._';

			// Process each line separately to aid advanced formatting.
			$wasEmpty = false;
			$indent = "";
			foreach (preg_split('#\n#', $doctext) as $line) {

				$isEmpty = trim($line) === '';

				// Reduce multiple empty lines into a single empty line.
				if ($wasEmpty && $isEmpty) {
					continue;
				}

				$wasEmpty = $isEmpty;
				$line = remove_doc_tags($line);

				$md .= "{$indent}{$line}\n";

			}

			$md .= "\n---\n";

		}
	}

	return $md;

}

$md = build_markdown($data);
line(Colors::get("Saving into {lightblue}$outputFile{_} ..."));

file_put_contents($outputFile, $md);

line('Done.');

// Print warnings at the end, if there were any.
if ($warnings) {
	line(Colors::get("{yellow}Warnings: "));
	foreach ($warnings as $warning) {
		echo "- $warning\n";
	}
}
