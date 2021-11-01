<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Values\TypeValue;

class ClassLiteral extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$className = $node['cls'];
		$parentTypeName = $node['parent'];

		if ($parentTypeName !== false) {
			$parentType = $context->getVariable($parentTypeName);
		} else {
			$parentType = $context->getTypesModule()->getVariable('object');
		}

		// Create a new scope for this class.
		$classScope = new Scope;
		$classScope->setParent($context->getCurrentScope());

		// Execute the class's insides with the class scope.
		// Variables (and functions) declared inside the class will then
		// be attributes
		$wrapper = new ContextPushPopWrapper($context, null, $classScope);
		$wrapper->wrap(fn($ctx) => HandlerFactory::runNode($node['def'], $ctx));

		$result = new TypeValue($className, $parentType, $classScope->getVariables());

		$context->getCurrentScope()->setVariable($className, $result);

	}

	public static function reduce(array &$node): void {
		$node['cls'] = $node['cls']['text'];
		$node['parent'] = $node['parent']['text'] ?? false;
	}

}
