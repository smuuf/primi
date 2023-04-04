<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

use Smuuf\StrictObject;
use Smuuf\Primi\MagicStrings;
use Smuuf\Primi\Stdlib\TypeExtensions\BoolTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\DictTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\ListTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\NullTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\TypeTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\RegexTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\TupleTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\NumberTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\ObjectTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\StringTypeExtension;
use Smuuf\Primi\Stdlib\TypeExtensions\ForbiddenTypeExtension;
use Smuuf\Primi\Values\BoolValue;
use Smuuf\Primi\Values\DictValue;
use Smuuf\Primi\Values\FuncValue;
use Smuuf\Primi\Values\ListValue;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Values\TupleValue;
use Smuuf\Primi\Values\RegexValue;
use Smuuf\Primi\Values\MethodValue;
use Smuuf\Primi\Values\ModuleValue;
use Smuuf\Primi\Values\NumberValue;
use Smuuf\Primi\Values\StringValue;
use Smuuf\Primi\Values\StaticTypeValue;
use Smuuf\Primi\Values\NotImplementedValue;
use Smuuf\Primi\Values\IteratorFactoryValue;

/**
 * Global provider for static/immutable native object types.
 */
abstract class StaticTypes {

	use StrictObject;
	use StaticTypesContainer;

	/** @var array<string, StaticTypeValue> */
	private static $instances = [];

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getObjectType(): StaticTypeValue {
		return self::$instances['objectType']
			??= new StaticTypeValue(MagicStrings::TYPE_OBJECT, \null, ObjectTypeExtension::execute(), \false);
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getTypeType(): StaticTypeValue {
		return self::$instances['typeType']
			??= new StaticTypeValue(StaticTypeValue::TYPE, self::getObjectType(), TypeTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getNullType(): StaticTypeValue {
		return self::$instances['nullType']
			??= new StaticTypeValue(NullValue::TYPE, self::getObjectType(), NullTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getBoolType(): StaticTypeValue {
		return self::$instances['boolType']
			??= new StaticTypeValue(BoolValue::TYPE, self::getObjectType(), BoolTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getNumberType(): StaticTypeValue {
		return self::$instances['numberType']
			??= new StaticTypeValue(NumberValue::TYPE, self::getObjectType(), NumberTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getStringType(): StaticTypeValue {
		return self::$instances['stringType']
			??= new StaticTypeValue(StringValue::TYPE, self::getObjectType(), StringTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getRegexType(): StaticTypeValue {
		return self::$instances['regexType']
			??= new StaticTypeValue(RegexValue::TYPE, self::getObjectType(), RegexTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getListType(): StaticTypeValue {
		return self::$instances['listType']
			??= new StaticTypeValue(ListValue::TYPE, self::getObjectType(), ListTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getDictType(): StaticTypeValue {
		return self::$instances['dictType']
			??= new StaticTypeValue(DictValue::TYPE, self::getObjectType(), DictTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getTupleType(): StaticTypeValue {
		return self::$instances['tupleType']
			??= new StaticTypeValue(TupleValue::TYPE, self::getObjectType(), TupleTypeExtension::execute());
	}

	#[StaticTypeGetter()]
	public static function getFuncType(): StaticTypeValue {
		return self::$instances['funcType']
			??= new StaticTypeValue(FuncValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	#[StaticTypeGetter()]
	public static function getMethodType(): StaticTypeValue {
		return self::$instances['methodType']
			??= new StaticTypeValue(MethodValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	#[StaticTypeGetter()]
	public static function getIteratorFactoryType(): StaticTypeValue {
		return self::$instances['iteratorFactoryType']
			??= new StaticTypeValue(IteratorFactoryValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	#[StaticTypeGetter()]
	public static function getModuleType(): StaticTypeValue {
		return self::$instances['moduleType']
			??= new StaticTypeValue(ModuleValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	#[StaticTypeGetter(StaticTypeGetter::INJECT_AS_BUILTIN)]
	public static function getNotImplementedType(): StaticTypeValue {
		return self::$instances['notImplementedType']
			??= new StaticTypeValue(NotImplementedValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

}
