<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

trait StaticTypesContainer {

	private static function extractGetters(int $flags = 0): array {

		$result = [];
		$rc = new \ReflectionClass(static::class);

		$methods = $rc->getMethods();
		foreach ($methods as $ref) {

			if (!$ref->isStatic() || !$ref->isPublic()) {
				continue;
			}

			$attrs = $ref->getAttributes(StaticTypeGetter::class);
			if (!$attrs) {
				continue;
			}

			$attr = end($attrs);
			if ($flags & ~$attr->newInstance()->flags) {
				continue;
			}

			$result[] = static::{$ref->name}(...);

		}

		return $result;

	}

	public static function extractBuiltins(): array {
		return self::extractGetters(StaticTypeGetter::INJECT_AS_BUILTIN);
	}

	public static function extractAll(): array {
		return self::extractGetters();
	}

}
