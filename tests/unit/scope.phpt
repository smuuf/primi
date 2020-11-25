<?php

use \Tester\Assert;

use \Smuuf\Primi\Structures\FnContainer;

require __DIR__ . '/../bootstrap.php';

//
// Variables
//

$scope = new \Smuuf\Primi\Scope;

// Pool of variables is empty.
Assert::type('array', $v = $scope->getVariables());
Assert::falsey($v);

$varA = new \Smuuf\Primi\Structures\NumberValue(123);
$varB = new \Smuuf\Primi\Structures\StringValue("foo");
$scope->setVariable('var_a', $varA);
$scope->setVariable('var_b', $varB);

// The returned value instances Scope returned are the same objects as inserted.
Assert::same($varA, $scope->getVariable('var_a'));
Assert::same($varB, $scope->getVariable('var_b'));
Assert::same([
	'var_a' => $varA,
	'var_b' => $varB,
], $scope->getVariables());

// Pool of variables is not empty.
Assert::truthy($scope->getVariables());

$multi = [
	'var_c' => ($varC = new \Smuuf\Primi\Structures\BoolValue(false)),
	'var_d' => ($varD = new \Smuuf\Primi\Structures\RegexValue("[abc]")),
];

$scope->setVariables($multi);

// Test that all variables are present (and in correct order).
Assert::same([
	'var_a' => $varA,
	'var_b' => $varB,
	'var_c' => $varC,
	'var_d' => $varD,
], $scope->getVariables());

// Test accessing undefined variable.
Assert::null($scope->getVariable('some_undefined_variable'));

// Test automatic value creation from scalars.
$scope->setVariables([
	'var_e' => 123,
	'var_f' => "hello there!",
	'var_g' => [1, 2, 3],
	'var_h' => ['a' => 1, 'b' => 2, 'c' => 3],
]);

Assert::type(\Smuuf\Primi\Structures\NumberValue::class, $scope->getVariable('var_e'));
Assert::type(\Smuuf\Primi\Structures\StringValue::class, $scope->getVariable('var_f'));
Assert::type(\Smuuf\Primi\Structures\ListValue::class, $scope->getVariable('var_g'));
Assert::type(\Smuuf\Primi\Structures\DictValue::class, $scope->getVariable('var_h'));
Assert::same("123", $scope->getVariable('var_e')->getInternalValue());
Assert::same("hello there!", $scope->getVariable('var_f')->getInternalValue());
Assert::type('array', $scope->getVariable('var_g')->getInternalValue());

//
// Functions
//

$scope = new \Smuuf\Primi\Scope;

// Pool of functions is empty.
Assert::type('array', $v = $scope->getVariables());
Assert::falsey($v);

// Create empty function container for testing purposes.
$fnContainer = FnContainer::buildFromClosure(function() {});

$funcA = new \Smuuf\Primi\Structures\FuncValue($fnContainer);
$scope->setVariable('func_a', $funcA);

// The returned function instance Scope returned is the same object as inserted.
Assert::same($funcA, $scope->getVariable('func_a'));

// Pool of variables is not empty.
Assert::truthy($scope->getVariables());

$multi = [
	'func_b' => ($funcB = new \Smuuf\Primi\Structures\FuncValue($fnContainer)),
	'func_c' => ($funcC = new \Smuuf\Primi\Structures\FuncValue($fnContainer)),
];

$scope->setVariables($multi);

// Test that all variables are present (and in correct order).
Assert::same([
	'func_a'=> $funcA,
	'func_b'=> $funcB,
	'func_c'=> $funcC,
], $scope->getVariables());

// Test accessing undefined function.
Assert::null($scope->getVariable('some_undefined_function'));
