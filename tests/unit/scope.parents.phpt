<?php

use \Tester\Assert;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Values\NumberValue;

require __DIR__ . '/../bootstrap.php';

//
// Scopes and parents: setting and getting parents.
//

$scopeA = new Scope;

/**
 * Scope does not expose the "parent" property, so let's have a helper function
 * for inspecting it when testing.
 */
function get_scope_parent(Scope $s) {
	$reflectionProperty = new \ReflectionProperty(Scope::class, 'parent');
	$reflectionProperty->setAccessible(true);
	return $reflectionProperty->getValue($s);
}

Assert::null(get_scope_parent($scopeA), 'New scope has no parent');

$scopeB = new Scope;
$scopeA->setParent($scopeB);

Assert::same($scopeB, get_scope_parent($scopeA), 'Scope A has scope B as parent');
Assert::null(get_scope_parent(get_scope_parent($scopeA)), 'Scope B has no parent');

$scopeC = new Scope;
$scopeB->setParent($scopeC);

Assert::same($scopeB, get_scope_parent($scopeA), 'Scope A has scope B as parent');
Assert::same($scopeC, get_scope_parent(get_scope_parent($scopeA)), 'Scope B has scope C as parent');
Assert::null(get_scope_parent(get_scope_parent(get_scope_parent($scopeA))), 'Scope C has no parent');

//
// Scopes and parents: setting variables and getting variables from parent scopes.
//

$valA = new NumberValue('123');
$valB = new NumberValue('456');

// ScopeC is the top parent of the scope hierarchy. Assign a variable to it.
$scopeC->setVariable('some_val', $valA);
Assert::same($valA, $scopeA->getVariable('some_val'), 'Scope A: Access to value from scope C');
Assert::same($valA, $scopeB->getVariable('some_val'), 'Scope C: Access to value from scope C');
Assert::same($valA, $scopeC->getVariable('some_val'), 'Scope C: Access to value from scope C');

// ScopeB is the middle scope. Variable in lower scopes have priority over
// variables from top ones.
$scopeB->setVariable('some_val', $valB);
Assert::same($valB, $scopeA->getVariable('some_val'), 'Scope A: AbstractValue assigned to scope B overrides value from scope C');
Assert::same($valB, $scopeB->getVariable('some_val'), 'Scope B: AbstractValue assigned to scope B overrides value from scope C');
Assert::same($valA, $scopeC->getVariable('some_val'), 'Scope C: Still has the original value');
