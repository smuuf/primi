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

		// Prepare dict array for passing specifics about parameters expected
		// by the function.
		// Parameters will be prepared as a dict array with names of parameters
		// being the keys - with false as their values.
		// This makes handling the "invoke" logic used later quite easier.
		$params = [
			'names' => [],
			'defaults' => [],
		];

		// Make sure this is always list, even with one item.
		$paramsNodes = Func::ensure_indexed($paramsNode);

		$foundStarred = \false;
		$foundDoubleStarred = \false;

		foreach ($paramsNodes as $node) {

			$paramName = $node['param']['text'];

			// Detect duplicate param names - they are forbidden.
			// For this we need to use parameter names stripped of any stars,
			// because using "f(c, *c)" should still be a "duplicate parameter"
			// error.
			//
			// This happens if defining function parameters like:
			// > function f(a, b, b) { ... }

			if (isset($paramNames[$paramName])) {
				throw new InternalPostProcessSyntaxError(
					"Duplicate parameter name '$paramName'"
				);
			}

			// We track stripped param names as array keys so we can just
			// use isset().
			$paramNames[$paramName] = \false;

			// Detect wrongly positioned arguments. Non-variadics must be before
			// variadics.
			if (
				$node['stars'] === StarredExpression::STARS_NONE
				&& ($foundStarred || $foundDoubleStarred)
			) {
				throw new InternalPostProcessSyntaxError(
					"Non-variadic parameter found after variadic parameters"
				);
			}

			if (
				$node['stars'] === StarredExpression::STARS_ONE
				&& $foundDoubleStarred
			) {
				throw new InternalPostProcessSyntaxError(
					"Non-variadic positional parameter found after variadic keyword parameters"
				);
			}

			$foundStarred |= $node['stars'] === StarredExpression::STARS_ONE;
			$foundDoubleStarred |= $node['stars'] === StarredExpression::STARS_TWO;

			// FnContainer expects list of arguments WITH stars - so it can
			// know which parameters actually are variadic.
			$withStars = str_repeat('*', $node['stars']) . $paramName;
			$params['names'][$withStars] = \false;

			// If this parameter has a specified default value (internally
			// an AST node), place its AST node in the storage for defaults -
			// for later use when invoking the function.
			if (!empty($node['default'])) {
				$params['defaults'][$paramName] = $node['default'];
			} elseif ($params['defaults']) {
				// Current parameter has no default value, but we already
				// encountered some parameter with default value.
				// This will not stand! Throw a syntax error.
				throw new InternalPostProcessSyntaxError(
					"Non-default parameter is after default parameter"
				);
			}

		}

		return $params;

	}

}
