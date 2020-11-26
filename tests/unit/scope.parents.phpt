<?php

use \Tester\Assert;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\ExtensionScope;
use \Smuuf\Primi\Structures\NumberValue;

require __DIR__ . '/../bootstrap.php';

//
// Scopes and parents: setting and getting parents.
//

$scopeA = new Scope;
Assert::null($scopeA->getParent(), 'New scope has no parent');

$scopeB = new Scope;
$scopeA->setParent($scopeB);

Assert::same($scopeB, $scopeA->getParent(), 'Scope A has scope B as parent');
Assert::null($scopeA->getParent()->getParent(), 'Scope B has no parent');

$scopeC = new Scope;
$scopeB->setParent($scopeC);

Assert::same($scopeB, $scopeA->getParent(), 'Scope A has scope B as parent');
Assert::same($scopeC, $scopeA->getParent()->getParent(), 'Scope B has scope C as parent');
Assert::null($scopeA->getParent()->getParent()->getParent(), 'Scope C has no parent');

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
Assert::same($valB, $scopeA->getVariable('some_val'), 'Scope A: Value assigned to scope B overrides value from scope C');
Assert::same($valB, $scopeB->getVariable('some_val'), 'Scope B: Value assigned to scope B overrides value from scope C');
Assert::same($valA, $scopeC->getVariable('some_val'), 'Scope C: Still has the original value');

//
// Scopes and parents: extension hub will add extension scope on the top.
//

$eh = new ExtensionHub;

// We'll apply the EH to the most bottom node - ScopeA. New instance of
// ExtensionScope will be added as parent to the top node (which is ScopeC).
$eh->apply($scopeA);

Assert::same($scopeB, $scopeA->getParent(), 'After applying extension hub to scope A: Scope A still has scope B as parent');
Assert::same($scopeC, $scopeB->getParent(), 'After applying extension hub to scope A: Scope B still has scope C as parent');
$extScopeA = $scopeC->getParent();
Assert::type(ExtensionScope::class, $extScopeA, 'After applying extension hub to scope A: Scope C now has extension scope as parent');

// Applying EH to any of the scopes will not do nothing, because there already
// is some ExtensionScope present in the hierarchy.
$eh->apply($scopeA);

Assert::same($scopeB, $scopeA->getParent(), 'After second applying extension hub to scope A: Scope A still has scope B as parent');
Assert::same($scopeC, $scopeB->getParent(), 'After second applying extension hub to scope A: Scope B still has scope C as parent');
$extScopeB = $scopeC->getParent();
Assert::same($extScopeA, $extScopeB, 'After second applying extension hub to scope A: Scope C still has the same extension scope as parent as before');
Assert::null($extScopeB->getParent(), 'Extension scope has no parent scope');

// Even applying EH to the scope which is the ExtensionScope will not do anything.
$eh->apply($extScopeB);

Assert::same($scopeB, $scopeA->getParent());
Assert::same($scopeC, $scopeB->getParent());
Assert::same($extScopeB, $scopeC->getParent(), 'Nothing happens after applying extension hub to the extension scope, as there already is an extension scope in the hierearchy');
Assert::null($extScopeB->getParent());
