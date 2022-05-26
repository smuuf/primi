<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;

class ClassDefinition extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$className = $node['cls'];
		$parentTypeName = $node['parent'];

		if ($parentTypeName !== \false) {
			$parentType = $context->getVariable($parentTypeName);
		} else {
			$parentType = StaticTypes::getObjectType();
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
		$classScope = new Scope([], Scope::TYPE_CLASS);
		$classScope->setParent($context->getCurrentScope());

		// Execute the class's insides with the class scope.
		// Variables (and functions) declared inside the class will then
		// be attributes
		$wrapper = new ContextPushPopWrapper($context, \null, $classScope);
		$wrapper->wrap(fn($ctx) => HandlerFactory::runNode($node['def'], $ctx));

		$result = new TypeValue(
			$className,
			$parentType,
			$classScope->getVariables(),
			\false,
		);

		$context->getCurrentScope()->setVariable($className, $result);

	}

	public static function reduce(array &$node): void {
		$node['cls'] = $node['cls']['text'];
		$node['parent'] = $node['parent']['text'] ?? \false;
	}

}
