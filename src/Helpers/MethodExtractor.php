<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

use \Smuuf\DocBlockParser\Parser as DocBlockParser;
use Smuuf\Primi\Ex\EngineError;

abstract class MethodExtractor {

	use StrictObject;

	/**
	 * @return array<string, FuncValue> Dict array of public methods as mapping
	 * `[<method name> => FuncValue]` that are present in an object.
	 */
	public static function extractMethods(object $obj): array {

		$result = [];
		$extRef = new \ReflectionClass($obj);

		//
		// Extract functions.
		//

		$methods = $extRef->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $ref) {

			$name = $ref->getName();

			// Skip PHP magic "__methods", but allow Primi magic "__methods__".
			if (\str_starts_with($name, '__') && !\str_ends_with($name, '__')) {
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
					FnContainer::buildFromClosure([$obj, $name], $fnFlags)
				);

			}

		}

		return $result;

	}

}
