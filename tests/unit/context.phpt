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
        Assert::same([
            'var_a' => $varA,
            'var_b' => $varB,
        ], $context->getVariables());

        // Pool of variables is not empty.
        Assert::truthy($context->getVariables());

        $multi = [
            'var_c' => ($varC = new \Smuuf\Primi\Structures\BoolValue(false)),
            'var_d' => ($varD = new \Smuuf\Primi\Structures\RegexValue("[abc]")),
        ];

        $context->setVariables($multi);

        // Test that all variables are present (and in correct order).
        Assert::same([
            'var_a' => $varA,
            'var_b' => $varB,
            'var_c' => $varC,
            'var_d' => $varD,
        ], $context->getVariables());

    }

    public function testFunctions() {

        $context = new \Smuuf\Primi\Context;

        // Pool of functions is empty.
        Assert::type('array', $v = $context->getFunctions());
        Assert::falsey($v);

        $funcA = new \Smuuf\Primi\Structures\Func('func_a', [], []);
        $context->setFunction('func_a', $funcA);

        // The returned function instance Context returned is the same object as inserted.
        Assert::same($funcA, $context->getFunction('func_a'));

        // Pool of variables is not empty.
        Assert::truthy($context->getFunctions());

        $multi = [
            'func_b' => ($funcB = new \Smuuf\Primi\Structures\Func('func_b', [], [])),
            'func_c' => ($funcC = new \Smuuf\Primi\Structures\Func('func_c', [], []))
        ];

        $context->setFunctions($multi);

        // Test that all funciables are present (and in correct order).
        Assert::same([
            'func_a'=> $funcA,
            'func_b'=> $funcB,
            'func_c'=> $funcC,
        ], $context->getFunctions());

    }

})->run();