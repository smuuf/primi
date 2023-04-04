<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Helpers\StringEscaping;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class FStringLiteral extends Handler {

	public static function reduce(array &$node): void {

		// There can be ordinary non-expression-text nodes and FStringText
		// nodes, so let's build a unified list containing either:
		// 1. Ordinary text as string.
		// 2. FStringExpr nodes as node array.
		// ... which will be easy to handle during compilation

		$parts = [];
		foreach (Func::ensure_indexed($node['core']['parts']) as $part) {

			// Extract "just text" from the non-expr nodes, so we don't have
			// to deal with them later.
			if ($part['name'] === 'FStringTxt') {

				// Convert double-curly-braces in ordinary text subnodes
				// to single ones.
				$part = \str_replace(['{{', '}}'], ['{', '}'], $part['text']);
				$part = StringEscaping::unescapeString($part);
				$parts[] = $part;

			} elseif ($part['name'] === 'FStringExpr') {

				// Keep f-string's nested-expression nodes as-is.
				$parts[] = $part;

			} else {
				throw new EngineInternalError(
					"Unknown node type encountered when parsing f-string"
				);
			}

		}

		$node['parts'] = $parts;

		// Remove unnecessary parts of the node.
		unset($node['text']);
		unset($node['quote']);
		unset($node['core']);

	}

	public static function compile(Compiler $bc, array $node): void {

		$parts = $node['parts'];
		$partCount = count($parts);

		foreach ($parts as $part) {
			if (is_array($part)) {
				// Compile expression nodes inside the f-string.
				$bc->inject($part);
			} else {
				// Compile ordinary strings.
				$bc->add(Machine::OP_LOAD_CONST, Interned::string($part));
			}
		}

		$bc->add(Machine::OP_BUILD_STRING, $partCount);

	}

}
