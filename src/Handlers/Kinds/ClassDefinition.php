<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Helpers\Types;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

class ClassDefinition extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$className = $node['cls'];
		$parentTypeName = $node['parent'];

		if ($parentTypeName !== \false) {
			$parentType = $context->getVariable($parentTypeName);
		} else {
			$parentType = BuiltinTypes::getObjectType();
		}

		if (!$parentType instanceof TypeValue) {
			throw new RuntimeError(
				"Specified parent class '$parentTypeName' is not a type object"
			);
		}

		// Create a new scope for this class.
		// Set scope's type to be 'class scope', so that functions defined as a
		// method inside a class won't have direct access to its class's scope.
		// (All access to class' attributes should be done by accessing class
		// reference inside the function).
		$classScope = new Scope(
			[],
			type: Scope::TYPE_CLASS,
			parent: $context->getCurrentScope()
		);

		// Execute the class's insides with the class scope.
		// Variables (and functions) declared inside the class will then
		// be attributes
		$wrapper = new ContextPushPopWrapper($context, \null, $classScope);
		$wrapper->wrap(static fn($ctx) => HandlerFactory::runNode($node['def'], $ctx));

		$classAttrs = Types::prepareTypeMethods($classScope->getVariables());

		$result = new TypeValue(
			$className,
			$parentType,
			$classAttrs,
			isFinal: \false,
			isMutable: \true,
		);

		$context->getCurrentScope()->setVariable($className, $result);

	}

	public static function reduce(array &$node): void {
		$node['cls'] = $node['cls']['text'];
		$node['parent'] = $node['parent']['text'] ?? \false;
	}

}
