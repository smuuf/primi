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
Assert::null($scopeA->getParent());

$scopeB = new Scope;
$scopeA->setParent($scopeB);

Assert::same($scopeB, $scopeA->getParent());
Assert::null($scopeA->getParent()->getParent());

$scopeC = new Scope;
$scopeB->setParent($scopeC);

Assert::same($scopeB, $scopeA->getParent());
Assert::same($scopeC, $scopeA->getParent()->getParent());
Assert::null($scopeA->getParent()->getParent()->getParent());

//
// Scopes and parents: setting variables and getting variables from parent scopes.
//

$valA = new NumberValue('123');
$valB = new NumberValue('456');

// ScopeC is the top parent of the scope hierarchy. Assign a variable to it.
$scopeC->setVariable('some_val', $valA);
Assert::same($valA, $scopeC->getVariable('some_val'));
Assert::same($valA, $scopeB->getVariable('some_val'));
Assert::same($valA, $scopeA->getVariable('some_val'));

// ScopeB is the middle scope. Variable in lower scopes have priority over
// variables from top ones.
$scopeB->setVariable('some_val', $valB);
Assert::same($valA, $scopeC->getVariable('some_val')); // Original value.
Assert::same($valB, $scopeB->getVariable('some_val')); // New value overrides original.
Assert::same($valB, $scopeA->getVariable('some_val')); // New value overrides original.

//
// Scopes and parents: extension hub will add extension scope on the top.
//

$eh = new ExtensionHub;

// We'll apply the EH to the most bottom node - ScopeA. New instance of
// ExtensionScope will be added as parent to the top node (which is ScopeC).
$eh->apply($scopeA);

Assert::same($scopeB, $scopeA->getParent());
Assert::same($scopeC, $scopeB->getParent());
$extScopeA = $scopeC->getParent();
Assert::type(ExtensionScope::class, $extScopeA);

// Applying EH to any of the scopes will not do nothing, because there already
// is some ExtensionScope present in the hierarchy.
// We'll apply the EH to the most bottom node - ScopeA. New instance of
// ExtensionScope will be added as parent to the top node (which is ScopeC).
$eh->apply($scopeA);

Assert::same($scopeB, $scopeA->getParent());
Assert::same($scopeC, $scopeB->getParent());
$extScopeB = $scopeC->getParent();
Assert::same($extScopeA, $extScopeB); // Same ExtensionScope instance as before.
Assert::null($extScopeB->getParent());

// Even applying EH to the scope which is the ExtensionScope will not do anything.
$eh->apply($extScopeB);

Assert::same($scopeB, $scopeA->getParent());
Assert::same($scopeC, $scopeB->getParent());
Assert::same($extScopeB, $scopeC->getParent());
Assert::null($extScopeB->getParent());
