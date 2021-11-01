<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Helpers\StringEscaping;

class FStringLiteral extends StringLiteral {

	protected static function handle(array $node, Context $context) {

		$result = '';
		foreach ($node['parts'] as $part) {

			if (\is_array($part)) {

				// Expr node (no other thing would be an array at this point).
				$value = HandlerFactory::runNode($part['core'], $context);
				$result .= $value->getStringValue();

			} else {

				// Just text - add it to the string.
				$result .= $part;

			}

		}

		// Unescape the whole result string - to expand escape sequences - which
		// is expected (this for example allows "\n" to be used as newline char
		// in f-strings).
		return Interned::string(StringEscaping::unescapeString($result));

	}

	public static function reduce(array &$node): void {

		// There can be ordinary non-expression-text nodes and FStringText
		// nodes, so let's build a unified list containing either:
		// 1. Ordinary text as string.
		// 2. FStringExpr nodes as array.
		// ... which will be easy to handle at runtime.

		$unified = [];
		foreach (Func::ensure_indexed($node['core']['parts']) as $part) {

			// Extract "just text" from the non-expr nodes, so we don't have
			// to deal with them later.
			if ($part['name'] !== 'FStringExpr') {
				// Convert double-curly-braces to single ones.
				$unified[] = \str_replace(['{{', '}}'], ['{', '}'], $part['text']);
			} else {
				// Keep expr nodes as-is.
				$unified[] = $part;
			}

		}

		$node['parts'] = $unified;

		// Remove unnecessary parts of the node.
		unset($node['text']);
		unset($node['quote']);
		unset($node['core']);


	}

}
