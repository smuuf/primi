<?php

declare(strict_types=1);

namespace Smuuf\Primi\Extensions;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

use \Smuuf\Primi\Ex\EngineError;

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

			$attr = $ref->getAttributes(PrimiFunc::class);
			if (!$attr) {
				continue;
			}

			if (\count($attr) > 1) {
				throw new EngineError(\sprintf(
					"There must be only one '%s' attribute present",
					PrimiFunc::class
				));
			}

			/** @var PrimiFunc */
			$attr = $attr[0]->newInstance();
			$fnFlags = [];

			if ($attr->hasCallConv(PrimiFunc::CONV_CALLARGS)) {
				$fnFlags[] = FnContainer::FLAG_CALLCONV_CALLARGS;
			}

			if ($attr->hasToStack()) {
				$fnFlags[] = FnContainer::FLAG_TO_STACK;
			}

			$result[$name] = new FuncValue(
				FnContainer::buildFromClosure([$obj, $name], $fnFlags));

		}

		return $result;

	}

}
