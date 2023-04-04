<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Scope;
use Smuuf\Primi\Context;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Compiler\CodeType;
use Smuuf\Primi\Values\FuncValue;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Structures\FnContainer;

class FunctionDefinition extends Handler {

	public static function handleCreateFunction(
		Context $ctx,
		string $fnName,
		Bytecode $bytecode,
		array $params,
	) {

		$frame = $ctx->getCurrentFrame();
		$module = $frame->getModule();
		$name = \sprintf("%s.%s()", $module->getName(), $fnName);

		$currentScope = $frame->getScope();

		// If a function is defined as a method inside a class (directly
		// first-level function definition in the class definition), we do not
		// want the function to have direct access to its class's scope.
		// (All access to class' attributes should be done by accessing class
		// self-reference variable inside the function).
		// So, in that case, instead of current scope we'll pass the parent
		// scope of the current scope as the definition scope.
		$parentScope = $currentScope->getType() === Scope::TYPE_CLASS
			? $currentScope->getParent()
			: $currentScope;

		$fnc = FnContainer::build(
			$bytecode,
			$name,
			$module,
			$params,
			$parentScope,
		);

		return new FuncValue($fnc);

	}

	public static function compile(Compiler $bc, array $node): void {

		$compiler = new Compiler($node['body'], codeType: CodeType::CodeFunction);
		$bytecode = $compiler->compile();

		$params = $node['params'];
		$params['defaults'] = !empty($params['defaults'])
			? self::compileDefaults($params['defaults'])
			: [];

		$bc->add(
			Machine::OP_CREATE_FUNCTION,
			$node['fnName'],
			$bytecode->toFinalBytecode(),
			$params,
		);

		$bc->add(Machine::OP_STORE_NAME, $node['fnName']);

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

	/**
	 * @param array $paramsNode
	 * @phpstan-param TypeDef_AstNode $paramsNode
	 * @return array{names: array<string>, defaults: array<string, TypeDef_AstNode>}
	 */
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

		// For checking duplicate param names (without stars).
		$paramNames = [];

		$foundStarred = \false;
		$foundDoubleStarred = \false;

		foreach ($paramsNodes as $node) {

			/** @var string */
			$paramName = $node['param']['text'];

			// Detect duplicate param names - they are forbidden.
			// For this we need to use parameter names stripped of any stars,
			// because using "f(c, *c)" should still be a "duplicate parameter"
			// error.
			//
			// This happens if defining function parameters like:
			// > function f(a, b, b) { ... }

			if (\in_array($paramName, $paramNames, \true)) {
				throw InternalSyntaxError::fromNode(
					$node,
					"Duplicate parameter name '$paramName'"
				);
			}

			// We track stripped param names as array keys so we can just
			// use isset().
			$paramNames[] = $paramName;

			// Detect wrongly positioned parameters.
			if ($foundDoubleStarred) {

				if ($node['stars'] !== StarredExpression::STARS_TWO) {
					throw InternalSyntaxError::fromNode(
						$node,
						"Variadic keyword parameters must be placed after all others"
					);
				}

				throw InternalSyntaxError::fromNode(
					$node,
					"Variadic keyword parameters must be present only once"
				);

			}

			if ($foundStarred) {
				if ($node['stars'] === StarredExpression::STARS_ONE) {
					throw InternalSyntaxError::fromNode(
						$node,
						"Variadic positional parameters must be present only once"
					);
				}
			}

			$foundStarred |= $node['stars'] === StarredExpression::STARS_ONE;
			$foundDoubleStarred |= $node['stars'] === StarredExpression::STARS_TWO;

			// FnContainer expects list of arguments WITH stars - so it can
			// know which parameters actually are variadic.
			$withStars = \str_repeat('*', $node['stars']) . $paramName;
			$params['names'][] = $withStars;

			// If this parameter has a specified default value (internally
			// an AST node), place its AST node in the storage for defaults -
			// for later use when invoking the function.
			if (!empty($node['default'])) {
				$params['defaults'][$paramName] = $node['default'];
			} elseif ($params['defaults'] && !$foundStarred && !$foundDoubleStarred) {
				// Current parameter has no default value and is not
				// variadic (starred), but we already encountered some parameter
				// with default value.
				// This will not stand! Throw a syntax error.
				throw InternalSyntaxError::fromNode(
					$node,
					"Non-default non-variadic parameter placed after default parameter"
				);
			}

		}

		return $params;

	}

	/**
	 * Compile AST nodes representing expressions acting as defaults of
	 * parameter into bytecode objects.
	 *
	 * These will later be used when evaluating defaults during the function's
	 * call.
	 *
	 * @param array<string, TypeDef_AstNode>
	 * @return array<string, Bytecode>
	 */
	private static function compileDefaults(array $defaults): array {

		return array_map(function(array $node) {

			$compiler = new Compiler($node);
			$dll = $compiler->compile(keepValue: true);

			return $dll->toFinalBytecode();

		}, $defaults);

	}

}
