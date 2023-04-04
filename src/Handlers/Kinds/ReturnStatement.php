<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\CodeType;
use Smuuf\Primi\Handlers\Handler;

class ReturnStatement extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		if ($bc->getCodeType() !== CodeType::CodeFunction) {
			throw InternalSyntaxError::fromNode($node, "'return' used outside function");
		}

		if (isset($node['retval'])) {
			$bc->inject($node['retval']);
			$bc->add(Machine::OP_RETURN);
		} else {
			$bc->add(Machine::OP_RETURN);
		}

	}

}
