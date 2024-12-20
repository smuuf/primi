<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Ex\WrongEscapeSequenceException;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Helpers\StringEscaping;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class StringLiteral extends Handler {

	/**
	 * @phpstan-param TypeDef_AstNode $node
	 */
	public static function reduce(array &$node): void {

		try {
			$node['text'] = StringEscaping::unescapeString(
				$node['core']['text'],
			);
		} catch (WrongEscapeSequenceException $exc) {
			throw InternalSyntaxError::fromNode($node, $exc->getMessage());
		}

		unset($node['quote']);
		unset($node['core']);

	}

	/**
	 * @phpstan-param TypeDef_AstNode $node
	 */
	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_CONST, Interned::string($node['text']));
	}

}
