<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

use Smuuf\Primi\Compiler\PostProcessors\Optimizer;
use Smuuf\Primi\Compiler\PostProcessors\LabelResolver;
use Smuuf\Primi\Compiler\PostProcessors\PostProcessorInterface;

abstract class PostProcessor {

	use \Smuuf\StrictObject;

	/**
	 * @var list<string>
	 * @phpstan-var list<class-string<PostProcessorInterface>>
	 */
	private const PROCESSORS = [
		Optimizer::class,
		LabelResolver::class,
	];

	public static function doPostProcessing(
		BytecodeDLL $bytecode,
	): void {

		foreach (self::getPipeline() as $postProcessor) {
			$postProcessor->process($bytecode);
		}

	}

	/**
	 * @return list<PostProcessorInterface>
	 */
	private static function getPipeline(): array {

		$result = [];
		foreach (self::PROCESSORS as $className) {
			$result[] = new $className;
		}

		return $result;

	}

}
