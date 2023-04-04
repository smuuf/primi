<?php

declare(strict_types=1);

use Tester\Assert;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;

require __DIR__ . '/../../bootstrap.php';

function get_ast(string $source) {
	return (new \Smuuf\Primi\Parser\ParserHandler($source))->run();
}

$ast = get_ast("1 + 2; 3 + 4;");
$compiler = new Compiler($ast);
$bytecode = $compiler->compile()->toFinalBytecode();
$opNames = array_column($bytecode->ops, 0);

$expected = [
	Machine::OP_LOAD_CONST,
	Machine::OP_POP,
	Machine::OP_LOAD_CONST,
	Machine::OP_POP,
	Machine::OP_RETURN,
];

Assert::same($expected, $opNames);

$bytecode = $compiler->compile(keepValue: true)->toFinalBytecode();
$opNames = array_column($bytecode->ops, 0);

$expected = [
	Machine::OP_LOAD_CONST,
	Machine::OP_POP,
	Machine::OP_LOAD_CONST,
	Machine::OP_RETURN,
];

Assert::same($expected, $opNames);
