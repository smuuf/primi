<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

use Smuuf\StrictObject;
use Smuuf\Primi\Values\StaticTypeValue;
use Smuuf\Primi\Stdlib\TypeExtensions\Exc\BaseExceptionTypeExtension;

/**
 * Global provider for static/immutable builtin exception types.
 */
abstract class StaticExceptionTypes {

	use StrictObject;
	use StaticTypesContainer;

	/** @var array<string, StaticTypeValue> */
	private static $instances = [];

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getBaseExceptionType(): StaticTypeValue {
		return self::$instances['BaseException']
			??= new StaticTypeValue(
				'BaseException',
				StaticTypes::getObjectType(),
				BaseExceptionTypeExtension::execute(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getSystemExceptionType(): StaticTypeValue {
		return self::$instances['SystemException']
			??= new StaticTypeValue(
				'SystemException',
				self::getBaseExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getExceptionType(): StaticTypeValue {
		return self::$instances['Exception']
			??= new StaticTypeValue(
				'Exception',
				self::getBaseExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getSyntaxErrorType(): StaticTypeValue {
		return self::$instances['SyntaxError']
			??= new StaticTypeValue(
				'SyntaxError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getRuntimeErrorType(): StaticTypeValue {
		return self::$instances['RuntimeError']
			??= new StaticTypeValue(
				'RuntimeError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getImportErrorType(): StaticTypeValue {
		return self::$instances['ImportError']
			??= new StaticTypeValue(
				'ImportError',
				self::getBaseExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getNameErrorType(): StaticTypeValue {
		return self::$instances['NameError']
			??= new StaticTypeValue(
				'NameError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getLookupErrorType(): StaticTypeValue {
		return self::$instances['LookupError']
			??= new StaticTypeValue(
				'LookupError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getIndexErrorType(): StaticTypeValue {
		return self::$instances['IndexError']
			??= new StaticTypeValue(
				'IndexError',
				self::getLookupErrorType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getKeyErrorType(): StaticTypeValue {
		return self::$instances['KeyError']
			??= new StaticTypeValue(
				'KeyError',
				self::getLookupErrorType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getAttributeErrorType(): StaticTypeValue {
		return self::$instances['AttributeError']
			??= new StaticTypeValue(
				'AttributeError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getTypeErrorType(): StaticTypeValue {
		return self::$instances['TypeError']
			??= new StaticTypeValue(
				'TypeError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getAssertionErrorType(): StaticTypeValue {
		return self::$instances['AssertionError']
			??= new StaticTypeValue(
				'AssertionError',
				self::getExceptionType(),
				isFinal: false, // Can be extended.
			);
	}

}
