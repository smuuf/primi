<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Compiler\CodeType;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Compiler\Compiler;

class AnonymousFunction extends Handler {

	public static function reduce(array &$node): void {

		if (isset($node['params'])) {
			$node['params'] =
				FunctionDefinition::prepareParameters($node['params']);
		} else {
			$node['params'] = [];
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$compiler = new Compiler($node['body'], codeType: CodeType::CodeFunction);
		$bytecode = $compiler->compile();

		$bc->add(
			Machine::OP_CREATE_FUNCTION,
			"<anonymous>",
			$bytecode->toFinalBytecode(),
			$node['params'],
		);

	}

}
