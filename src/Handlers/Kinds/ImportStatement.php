<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\VariableImportError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;

class ImportStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$dotpath = $node['module'];
		$symbols = $node['symbols'] ?? [];

		$moduleValue = $context->getImporter()->getModule($dotpath);
		$currentScope = $context->getCurrentScope();

		// Import only a specific symbol (variable) from the module.
		if ($symbols) {

			foreach ($symbols as $symbol) {

				$value = $moduleValue->attrGet($symbol);
				if ($value === \null) {
					throw new VariableImportError($symbol, $dotpath);
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

}
