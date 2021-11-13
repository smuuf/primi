<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\InternalPostProcessSyntaxError;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Structures\FnContainer;

class FunctionDefinition extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {

		$name = "{$node['fnName']}()";
		$currentScope = $context->getCurrentScope();

		// If a function is defined as a method inside a class (directly
		// first-level function definition in the class definition), we do not
		// want the function to have direct access to its class's scope.
		// (All access to class' attributes should be done by accessing class
		// reference inside the function).
		// So, in that case, instead of current scope we'll pass null as
		// the $currentScope as the definition scope.
		$parentScope = $currentScope->getType() === Scope::TYPE_CLASS
			? \null
			: $currentScope;

		$fnc = FnContainer::build(
			$node['body'],
			$name,
			$context->getCurrentModule(),
			$node['params'],
			$parentScope,
		);

		$currentScope->setVariable($node['fnName'], new FuncValue($fnc));

	}

	public static function reduce(array &$node): void {

		// Prepare function name.
		$node['fnName'] = $node['function']['text'];
		unset($node['function']);

		if (isset($node['params'])) {
			$node['params'] = self::prepareParameters($node['params']);
		} else {
			$node['params'] = [];
		}

	}

	public static function prepareParameters(array $paramsNode): array {

		// Prepare list of parameters.
		// Parameters will be prepared as a dict array with names of parameters
		// being the keys - with null as their values.
		// This makes handling the "invoke" logic used later quite easier.
		$params = [];
		if (isset($paramsNode)) {

			// Make sure this is always list, even with one item.
			$paramsNodes = Func::ensure_indexed($paramsNode);
			$paramNames = \array_column($paramsNodes, 'text');
			foreach ($paramNames as $paramName) {

				// Detect duplicate param names - they are forbidden.
				if (isset($params[$paramName])) {
					throw new InternalPostProcessSyntaxError(
						"Duplicate parameter '$paramName' in function"
					);
				}

				$params[$paramName] = \true;

			}

		}

		return $params;

	}

}
