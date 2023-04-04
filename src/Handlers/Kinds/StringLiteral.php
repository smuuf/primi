<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Helpers\StringEscaping;
use Smuuf\Primi\Handlers\Handler;

class StringLiteral extends Handler {

	public static function reduce(array &$node): void {

		$node['text'] = StringEscaping::unescapeString($node['core']['text']);
		unset($node['quote']);
		unset($node['core']);

	}

	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_CONST, Interned::string($node['text']));
	}

}
