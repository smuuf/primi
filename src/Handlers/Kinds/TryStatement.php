<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Handlers\Handler;

class TryStatement extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		$cleanupLabel = $bc->createLabel();
		$endLabel = $bc->createLabel();

		// Gather info about catch blocks:
		// 1) the type of exception they'll catch.
		// 2) the catch-block node, so we can compile them down below.
		// 3) build a targeting label, so we can jump around if there's a catch.

		/** @var array<int, array{string, Label}> */
		$catchSpecs = [];
		/** @var array<int, TypeDef_AstNode> */
		$catchBlocks = [];
		foreach ($node['catches'] as $i => $catchNode) {
			['type' => $excType, 'block' => $catchBlock] = $catchNode;
			$catchSpecs[$i] = [$excType, $bc->createLabel(), $catchNode['as'] ?? null];
			$catchBlocks[$i] = $catchBlock;
		}

		$bc->add(Machine::OP_TRYBLK_PUSH, $catchSpecs);

		$bc->inject($node['main']);
		$bc->add(Machine::OP_JUMP, $cleanupLabel);

		foreach ($catchBlocks as $i => $blockNode) {

			$bc->insertLabel($catchSpecs[$i][1]);
			// Pop the try-block we actually just caught from.
			$bc->add(Machine::OP_TRYBLK_POP);
			// Run the catch block.
			$bc->inject($blockNode);
			// Jump straight to the end.
			$bc->add(Machine::OP_JUMP, $endLabel);

		}

		$bc->insertLabel($cleanupLabel);
		$bc->add(Machine::OP_TRYBLK_POP);
		$bc->insertLabel($endLabel);

	}

	public static function reduce(array &$node): void {

		if (!isset($node['catches'])) {
			throw InternalSyntaxError::fromNode($node, "Try without catch");
		}

		$node['catches'] = Func::ensure_indexed($node['catches']);

		$lastIndex = count($node['catches']) - 1;
		foreach ($node['catches'] as $i => &$catch) {

			// If there's a catch without specified exception type and it's
			// not the last catch, then it's a syntax error.
			if (!isset($catch['type']) && $i !== $lastIndex) {
				throw InternalSyntaxError::fromNode(
					$catch,
					"Only the final catch can have no exception type specified",
				);
			}

			$catch['type'] = $catch['type']['text'] ?? null;
			$catch['as'] = $catch['as']['text'] ?? null;
		}

	}

}
