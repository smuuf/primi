<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Values;
use \Smuuf\Primi\Ex\EngineInternalError;

abstract class TypeResolver {

	private const BASIC_TYPES = [
		Values\AbstractValue::class => 'object',
		Values\InstanceValue::class => 'object',
		Values\NullValue::class => 'null',
		Values\BoolValue::class => 'bool',
		Values\NumberValue::class => 'number',
		Values\StringValue::class => 'string',
		Values\ListValue::class => 'list',
		Values\DictValue::class => 'dict',
		Values\TypeValue::class => 'type',
		Values\FuncValue::class => 'function',
	];

	public static function resolve(string $class): string {

		// We have types stored in dict array without prefix backslash, so
		// remove it from the argument's start.
		$absolute = ltrim($class, '\\');

		if (!isset(self::BASIC_TYPES[$absolute])) {
			throw new EngineInternalError("Unable to resolve basic type name for class '$class'");
		}

		return self::BASIC_TYPES[$absolute];

	}

}
