<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

use \Smuuf\DocBlockParser\Parser as DocBlockParser;

class NativeModuleExecutor {

	use StrictObject;

	/**
	 * @return array<string, AbstractValue|mixed> Dict array that represents the
	 * contents of the module.
	 */
	public static function execute(Context $ctx, NativeModule $module): array {

		// Basic execution.
		$result = $module->execute($ctx);

		// Extract additional values via meta info.
		$result = \array_merge($result, self::extractReflected($module));

		return $result;

	}

	private static function extractReflected(NativeModule $module): array {

		$result = [];
		$extRef = new \ReflectionClass($module);

		//
		// Extract functions.
		//

		$methods = $extRef->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $ref) {

			$name = $ref->getName();

			// Skip magic methods.
			if (\strpos($name, '__') === 0) {
				continue;
			}

			$doc = $ref->getDocComment() ?: '';
			$db = DocBlockParser::parse($doc);

			$fnFlags = [];
			if ($fnTag = $db->getTag('primi.function')) {

				if ($fnTag->hasArg('inject-context')) {
					$fnFlags[] = FnContainer::FLAG_INJECT_CONTEXT;
				}

				if ($fnTag->hasArg('no-stack')) {
					$fnFlags[] = FnContainer::FLAG_NO_STACK;
				}

				if ($arg = $fnTag->getArg('call-convention')) {
					if ($arg->getValue() === 'object') {
						$fnFlags[] = FnContainer::FLAG_CALLCONVENTION_ARGSOBJECT;
					} else {
						throw new EngineError("Invalid value for argument 'call-convention'");
					}
				}

				$result[$name] = new FuncValue(
					FnContainer::buildFromClosure([$module, $name], $fnFlags)
				);

			}

		}

		return $result;

	}

}
