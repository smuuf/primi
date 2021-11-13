<?php

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Helpers\ValueFriends;
use \Smuuf\Primi\Structures\FnContainer;

use \Smuuf\DocBlockParser\Parser as DocBlockParser;

abstract class TypeExtension extends ValueFriends {

	/**
	 * @return array<string, AbstractValue|mixed> Dict array that represents the
	 * contents of the module.
	 */
	public static function execute(): array {

		// Extract additional values via meta info.
		return self::extract(new static);

	}

	private static function extract(self $ext): array {

		$result = [];
		$extRef = new \ReflectionClass($ext);

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

				$result[$name] = new FuncValue(
					FnContainer::buildFromClosure([$ext, $name], $fnFlags)
				);

			}

		}

		return $result;

	}

}
