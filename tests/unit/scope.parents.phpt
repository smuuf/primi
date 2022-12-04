<?php

use \Tester\Assert;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\NumberValue;

require __DIR__ . '/../bootstrap.php';

//
// Scopes and parents: setting and getting parents.
//

/**
 * Scope does not expose the "parent" property, so let's have a helper function
 * for inspecting it when testing.
 */
function get_scope_parent(Scope $s) {
	$reflectionProperty = new \ReflectionProperty(Scope::class, 'parent');
	$reflectionProperty->setAccessible(true);
	return $reflectionProperty->getValue($s);
}

$scopeNoParent = new Scope;
Assert::null(get_scope_parent($scopeNoParent), 'Scope without parent has no parent');

$scopeB = new Scope;
$scopeA = new Scope(parent: $scopeB);

Assert::same($scopeB, get_scope_parent($scopeA), 'Scope A has scope B as parent');
Assert::null(get_scope_parent(get_scope_parent($scopeA)), 'Scope B has no parent');

$scopeC = new Scope;
$scopeB = new Scope(parent: $scopeC);
$scopeA = new Scope(parent: $scopeB);

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

// Getting all variables from a scope - without parent scopes.
// Scope A is the bottom one (its parents are B and C).
$scopeA->setVariable('scope_a_var1', new NumberValue('1'));
$scopeA->setVariable('scope_a_var2', new NumberValue('2'));
$scopeB->setVariable('scope_b_var1', new NumberValue('3'));
$scopeB->setVariable('scope_b_var2', new NumberValue('4'));
$scopeC->setVariable('scope_c_var1', new NumberValue('5'));
$scopeC->setVariable('scope_c_var2', new NumberValue('6'));
Assert::count(2, $scopeA->getVariables(), 'Getting all variables from a scope - without parent scopes.');
Assert::count(7, $scopeA->getVariables(true), 'Getting all variables from a scope - with parent scopes.');
