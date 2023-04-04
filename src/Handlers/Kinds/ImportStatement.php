<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Context;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class ImportStatement extends Handler {

	public static function handleImport(
		Context $ctx,
		string $dotpath,
		array $symbols = [],
	): void {

		$frame = $ctx->getCurrentFrame();
		$currentScope = $frame->getScope();
		$moduleValue = $ctx->getImporter()->getModule($dotpath);

		// Import only a specific symbol (variable) from the module.
		if ($symbols) {

			foreach ($symbols as $symbol) {

				$value = $moduleValue->attrGet($symbol);
				if ($value === \null) {
					Exceptions::set(
						$ctx,
						StaticExceptionTypes::getImportErrorType(),
						"Cannot find '{$symbol}' in module '{$dotpath}'",
					);
					return;
				}

				$currentScope->setVariable($symbol, $value);

			}

		} else {

			// Importing the whole module - save it into current scope as
			// variable named after the last part of the module's dot path.
			$parts = \explode('.', $dotpath);
			$name = \end($parts);

			$currentScope->setVariable($name, $moduleValue);

		}

	}

	public static function reduce(array &$node): void {

		$node['module'] = $node['module']['text'];

		if (!empty($node['symbols'])) {

			$symbols = [];
			foreach (Func::ensure_indexed($node['symbols']) as $s) {
				$symbols[] = $s['text'];
			}

			$node['symbols'] = $symbols;

		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$dotpath = $node['module'];
		$symbols = $node['symbols'] ?? [];

		$bc->add(Machine::OP_IMPORT, $dotpath, $symbols);

	}

}
