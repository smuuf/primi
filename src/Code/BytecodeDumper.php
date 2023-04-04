<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Helpers\Func;

class BytecodeDumper {

	use \Smuuf\StrictObject;

	public static function dumpBytecodeStats(Bytecode $bc): void {

		/** @var array<string, int> */
		$instructions = []; // Gather instructions for stats.
		/** @var list<Bytecode> */
		$nested = []; // Gather nested bytecode objects.

		do {
			foreach ($bc->ops as $op) {
				$instructions[] = array_shift($op);
				self::formatArgs($op, $nested);
			}
		} while ($bc = array_shift($nested));

		$counts = array_count_values($instructions);
		asort($counts,);

		$maxNumLength = max(array_map(fn(int $n) => strlen((string) $n), $counts));

		foreach ($counts as $instruction => $count) {
			$count = str_pad((string) $count, $maxNumLength, pad_type: STR_PAD_LEFT);
			$instruction = str_pad((string) $instruction, 20);
			echo "$count $instruction\n";
		}

	}

	public static function dumpBytecode(Bytecode $bc): void {

		// Prepare array for bytecodes that will be encountered in the
		// main (or even further nested) bytecode - we also want to dump them.
		$nestedBytecodes = [];

		do {

			echo self::formatBytecode($bc) . "\n";
			foreach ($bc->ops as $index => $op) {

				self::dumpOp(
					op: $op,
					index: $index,
					opLocData: $bc->linesInfo,
					nested: $nestedBytecodes,
				);

			}

			echo "\n";

			// If we have more bytecodes to dump, let's do it.
			// We use shift instead of pop to dump the bytecodes in the order
			// they were encountered.

		} while ($bc = array_shift($nestedBytecodes));

	}

	/**
	 * @param list<mixed> $op
	 * @param list<OpLocation> $opLocData
	 * @param list<Bytecode> $nested
	 */
	public static function dumpOp(
		array $op,
		int $index,
		array $opLocData = [],
		string $suffix = '',
		array &$nested = [],
	): void {

		$opLoc = $opLocData[$index] ?? false;
		if ($opLoc) {
			$opLoc = "{$opLoc->line}:{$opLoc->pos}";
		} else {
			$opLoc = "?:?";
		}

		$instruction = str_pad((string) array_shift($op), 20);
		$opLoc = str_pad($opLoc, 8, pad_type: STR_PAD_LEFT);
		$index = str_pad((string) $index, 6, pad_type: STR_PAD_LEFT);
		$args = str_pad(self::formatArgs($op, $nested), 20);

		echo "{$opLoc}  {$index}  $instruction  $args\t{$suffix}\n";

	}

	/**
	 * Format opcode arguments into some human-readable form.
	 *
	 * @param list<mixed> $args
	 * @param list<Bytecode> $nested
	 */
	public static function formatArgs(
		array $args,
		array &$nested = [],
	): string {

		return implode(', ', array_map(function($arg) use (&$nested) {

			// If scalar PHP value, just print its PHP representation.
			if (is_scalar($arg)) {
				return var_export($arg, return: true);
			}

			// Render instances of Primi objects in some friendly format of
			// "<Primi type> <core PHP value>"
			if (is_a($arg, \Smuuf\Primi\Values\AbstractValue::class, true)) {
				return $arg->getStringRepr();
			}

			// Bytecode DDL object (used as op argument for OP_CREATE_FUNCTION).
			if ($arg instanceof Bytecode) {
				$nested[] = $arg;
				return self::formatBytecode($arg);
			}

			// BUILD_LIST can receive a single array as an argument.
			if (is_array($arg)) {
				return "[" . self::formatArgs($arg) . "]";
			}

			if (is_object($arg)) {
				return get_debug_type($arg);
			}

			// Represent other values as JSON encoded string, which is a good
			// default way.
			return json_encode($arg);

		}, $args));

	}

	private static function formatBytecode(Bytecode $bc): string {

		return sprintf(
			"<bytecode %s [%d ops]>",
			Func::object_hash($bc),
			count($bc->ops),
		);

	}

}
