<?php

use \Tester\Assert;

require __DIR__ . '/bootstrap.php';

/** @testcase **/
(new class extends \Tester\TestCase {

    public function testVariables() {

        $context = new \Smuuf\Primi\Context;

        // Pool of variables is empty.
        Assert::type('array', $v = $context->getVariables());
        Assert::falsey($v);

        $varA = new \Smuuf\Primi\Structures\NumberValue(123);
        $varB = new \Smuuf\Primi\Structures\StringValue("foo");
        $context->setVariable('var_a', $varA);
        $context->setVariable('var_b', $varB);

        // The returned value instances Context returned are the same objects as inserted.
        Assert::same($varA, $context->getVariable('var_a'));
        Assert::same($varB, $context->getVariable('var_b'));

        // Pool of variables is not empty.
        Assert::truthy($context->getVariables());

    }

    public function testFunctions() {

        $context = new \Smuuf\Primi\Context;

        // Pool of functions is empty.
        Assert::type('array', $v = $context->getFunctions());
        Assert::falsey($v);

        $funA = new \Smuuf\Primi\Structures\Func('func_1', [], []);
        $context->setFunction('fun_a', $funA);

        // The returned function instance Context returned is the same object as inserted.
        Assert::same($funA, $context->getFunction('fun_a'));

        // Pool of variables is not empty.
        Assert::truthy($context->getFunctions());

    }

})->run();
