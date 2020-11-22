#!/usr/bin/env php
<?php

declare(strict_types=1);

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Structures\Value;

define('ROOT_DIR', realpath(__DIR__ . '/../..'));
const OUTPUT_FILE = ROOT_DIR . '/docs/functions.md';

// Composer's autoload.
require ROOT_DIR . '/vendor/autoload.php';

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(ROOT_DIR . "/temp/");
$loader->addDirectory(ROOT_DIR . "/src")->register();

// Strict errors.
error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line) {
	throw new \ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

$warnings = [];

function out($text): void {
	echo "$text\n";
}

function warn($text): void {
	global $warnings;
	$warnings[] = "$text";
}

function err($text): void {
	echo "█ Error: $text\n";
	die(1);
}

// Polyfills for pre-PHP 8 versions.
if (!function_exists('str_ends_with')) {
	function str_starts_with(string $haystack, string $needle): bool {
		return \strncmp($haystack, $needle, \strlen($needle)) === 0;
	}
	function str_ends_with(string $haystack, string $needle): bool {
		return $needle === '' || $needle === \substr($haystack, -\strlen($needle));
	}
}

out('█ DocGen: Primi Standard Library Docs Generator');

out('Parsing extension files...');
const EXTENSIONS_GLOB = ROOT_DIR . '/src/extensions/psl/*.php';
if (!$extensionFiles = glob(EXTENSIONS_GLOB)) {
	err(sprintf("No extension files found at '%s'\n", EXTENSIONS_GLOB));
}

function get_relevant_methods(string $className): array {

	$classRef = new \ReflectionClass("\Smuuf\Primi\Psl\\$className");

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
	// Unify newlines.
	// Remove @annotations
	$doc = preg_replace('#^\h*\*+\h*\h@\w+\h*$#m', '', $doc);
	$doc = preg_replace('#\r?\n#', "\n", $doc);
	// Remove whitespace and '*' and '/' from the start
	$doc = preg_replace('#^[\/\s\*]*+#', '', $doc);
	// Remove whitespace and '*' and '/' at the end.
	$doc = preg_replace('#[\/\s\*]*+$#', '', $doc);
	// Remove startofline-horizontalwhitespace-asterisk-whitespace
	// Handles also empty lines after the asterisk (removes those empty lines).
	$doc = preg_replace('#^\h*\**(\h+|\n)#m', '', $doc);
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

			$isValue = is_a($paramType, Value::class, true);
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

			$paramType = ($paramType)::TYPE;
			$params[$paramName] = $paramType;

		}
	}

	return $params;

}

$data = [];
foreach ($extensionFiles as $filepath) {

	$filename = basename($filepath);
	$className = substr($filename, 0, strrpos($filename, '.'));

	$methods = get_relevant_methods($className);
	$data[$className] = [];

	foreach ($methods as $methodRef) {
		$methodName = $methodRef->getName();

		$docComment = $methodRef->getDocComment();
		$text = $docComment ? clean_doc_whitespace($docComment) : '';
		$params = extract_params($methodRef);

		// Extract return type, which must be specified.
		$returnType = false;
		if ($returnTypeRef = $methodRef->getReturnType()) {
			try {
				$returnType = ($returnTypeRef->getName())::TYPE;
			} catch (\Throwable $e) {
				warn("Class '$className, method '$methodName', referencing non-existent type having class " . $returnTypeRef->getName());
			}
		} else {
			warn("Class '$className, method '$methodName', return type does not hint Value|Context");
		}

		$data[$className][$methodName] = [
			'doctext' => $text,
			'parameters' => $params ?: false,
			'returns' => $returnType ?: false,
		];

	}

}

out('Building docs...');

function build_markdown(array $data): string {

	$indent = "\t";
	$md = '';

	foreach ($data as $extName => $extData) {

		$md .= "## $extName\n";
		foreach ($extData as $funcName => $funcData) {

			$parameters = [];
			if ($funcData['parameters']) {
				foreach ($funcData['parameters'] as $paramName => $paramType) {
					$parameters[] = "_{$paramType}_ `$paramName`";
				}
			}

			$parameters = implode(', ', $parameters);
			$returnType = $funcData['returns'] ?: '';
			$md .= "- **`$funcName`**($parameters) "
				. ($returnType ? " → _{$returnType}_\n" : "\n");

			$doctext = $funcData['doctext'] ?? '' ?: '_Missing description._';

			// Process each line separately to aid advanced formatting.
			$codeBlock = false;
			$buffer = '';
			foreach (preg_split('#\n#', $doctext) as $line) {

				$buffer .= rtrim($line, '\\');
				if (str_ends_with($line, '\\')) {
					// Continue with gathering the next line into our buffer.
					continue;
				}

				$modeChanged = false;
				if (!$codeBlock && str_starts_with($line, '```')) {
					$codeBlock = true;
					$modeChanged = true;
				}

				$md .= $indent
					. ($codeBlock ? '' : "- ")
					. "$buffer\n";

				// End codeblock after adding the text to buffer, so the last
				// ``` codeblock line doesn't have bullet point.
				if (!$modeChanged && $codeBlock && str_starts_with($line, '```')) {
					$codeBlock = false;
				}

				$buffer = '';

			}

			$md .= "\n";

		}
	}

	return $md;

}

$md = build_markdown($data);
file_put_contents(OUTPUT_FILE, $md);

out('Done.');

// Print warnings at the end, if there were any.
if ($warnings) {
	out("Warnings:");
	foreach ($warnings as $warning) {
		echo "- $warning\n";
	}
}
