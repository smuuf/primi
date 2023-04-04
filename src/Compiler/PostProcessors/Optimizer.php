<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors;

use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\ConstList;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\ConstantFolding;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\ConstDict;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\UselessCondJump;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\UselessStoreLoad;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\UselessDupTopPop;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\OptimizerInterface;

class Optimizer implements PostProcessorInterface {

	use \Smuuf\StrictObject;

	/**
	 * @var array<string>
	 * @phpstan-var array<class-string<OptimizerInterface>>
	 */
	private const OPTIMIZERS = [
		ConstantFolding::class,
		ConstList::class,
		ConstDict::class,
		UselessDupTopPop::class,
		UselessStoreLoad::class,
		UselessCondJump::class,
	];

	/**
	 * @param ?array<OptimizerInterface> $pipeline Optional pipeline to use
	 *     instead of default one.
	 */
	public function process(
		BytecodeDLL $bytecode,
		?array $pipeline = null,
	): void {

		$optimizers = $pipeline ?? self::getPipeline();

		do {

			// Let each of the optimizers do their job and do it repeatedly,
			// until none of the optimizers tells that it changed something.
			$changed = false;
			foreach ($optimizers as $optimizer) {
				$changed |= $optimizer->optimize($bytecode);
			}

		} while ($changed);

	}

	/**
	 * @return array<OptimizerInterface>
	 */
	private static function getPipeline(): array {

		$result = [];
		foreach (self::OPTIMIZERS as $className) {
			$result[] = new $className;
		}

		return $result;

	}

}
