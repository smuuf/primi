<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use Smuuf\Primi\Values\StringValue;

class ImportStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$dotPath = $node['module'];
		$symbol = $node['symbol'] ?? false;

		$moduleValue = $context->getImporter()->fetchModule($dotPath);

		// Import only a specific symbol (variable) from the module.
		if ($symbol) {
			$moduleValue = $moduleValue->attrGet(StringValue::build($symbol));
			$name = $symbol;
		} else {

			// Importing the whole module - save it into current scope as
			// variable named after the last part of the module's dot path.
			$parts = explode('.', $dotPath);
			$name = end($parts);

		}

		$context->getCurrentScope()->setVariable($name, $moduleValue);

	}

	public static function reduce(array &$node): void {
		$node['module'] = $node['module']['text'];
		$node['symbol'] = $node['symbol']['text'] ?? false;
	}

}
