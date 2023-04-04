<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Scope;
use Smuuf\Primi\Context;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Compiler\CodeType;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Helpers\Types;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\ScopeComposite;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;

class ClassDefinition extends Handler {

	public static function handleCreateClass(
		string $className,
		?string $parentTypeName,
		Bytecode $classBodyCode,
		Context $ctx,
	): TypeValue {

		$scope = $ctx->getCurrentFrame()->getScope();
		$scopeComp = new ScopeComposite($scope, $ctx->getBuiltins());

		if ($parentTypeName !== \null) {
			$parentType = Variable::fetch($parentTypeName, $scopeComp);
			if (!$parentType instanceof TypeValue) {
				Exceptions::piggyback(
					StaticExceptionTypes::getTypeErrorType(),
					"Specified parent class '$parentTypeName' is not a type object",
				);
			}
		} else {
			$parentType = StaticTypes::getObjectType();
		}

		// Create a new scope for this class.
		// Set scope's type to be 'class scope', so that functions defined as a
		// method inside a class won't have direct access to its class's scope.
		// (All access to class' attributes should be done by accessing class
		// reference inside the function).
		$classScope = new Scope(type: Scope::TYPE_CLASS, parent: $scope);

		$frame = $ctx->buildFrame(
			name: "<class: $className>",
			bytecode: $classBodyCode,
			scope: $classScope,
		);

		// Execute the body of the class.
		$ctx->runFrame($frame);
		$classAttrs = Types::prepareTypeMethods($classScope->getVariables());

		return new TypeValue(
			$className,
			$parentType,
			$classAttrs,
			isFinal: \false,
			isMutable: \true,
		);

	}

	public static function compile(Compiler $bc, array $node): void {

		$compiler = new Compiler($node['def'], codeType: CodeType::CodeClass);
		$classBytecode = $compiler->compile();

		$className = $node['cls'];
		$parentName = $node['parent'] ?? \null;

		$bc->add(
			Machine::OP_CREATE_CLASS,
			$className,
			$parentName,
			$classBytecode->toFinalBytecode()
		);

		$bc->add(Machine::OP_STORE_NAME, $className);

	}

	public static function reduce(array &$node): void {
		$node['cls'] = $node['cls']['text'];
		$node['parent'] = $node['parent']['text'] ?? \null;
	}

}
