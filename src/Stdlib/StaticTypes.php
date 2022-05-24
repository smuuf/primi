<?php

namespace Smuuf\Primi\Stdlib;

use \Smuuf\StrictObject;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Stdlib\TypeExtensions\BoolTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\DictTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\ListTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\NullTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\TypeTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\RegexTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\TupleTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\NumberTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\ObjectTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\StringTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\ForbiddenTypeExtension;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NotImplementedValue;
use \Smuuf\Primi\Values\IteratorFactoryValue;

/**
 * Global provider for static (immutable) native types.
 */
abstract class StaticTypes {

	use StrictObject;

	private static TypeValue $objectType;
	private static TypeValue $typeType;
	private static TypeValue $nullType;
	private static TypeValue $boolType;
	private static TypeValue $numberType;
	private static TypeValue $stringType;
	private static TypeValue $regexType;
	private static TypeValue $listType;
	private static TypeValue $dictType;
	private static TypeValue $tupleType;
	private static TypeValue $funcType;
	private static TypeValue $iteratorFactoryType;
	private static TypeValue $moduleType;

	// Specials.
	private static TypeValue $notImplementedType;

	public static function getObjectType(): TypeValue {
		return self::$objectType
			??= new TypeValue(MagicStrings::TYPE_OBJECT, \null, ObjectTypeExtension::execute(), \false);
	}

	public static function getTypeType(): TypeValue {
		return self::$typeType
			??= new TypeValue(TypeValue::TYPE, self::getObjectType(), TypeTypeExtension::execute());
	}

	public static function getNullType(): TypeValue {
		return self::$nullType
			??= new TypeValue(NullValue::TYPE, self::getObjectType(), NullTypeExtension::execute());
	}

	public static function getBoolType(): TypeValue {
		return self::$boolType
			??= new TypeValue(BoolValue::TYPE, self::getObjectType(), BoolTypeExtension::execute());
	}

	public static function getNumberType(): TypeValue {
		return self::$numberType
			??= new TypeValue(NumberValue::TYPE, self::getObjectType(), NumberTypeExtension::execute());
	}

	public static function getStringType(): TypeValue {
		return self::$stringType
			??= new TypeValue(StringValue::TYPE, self::getObjectType(), StringTypeExtension::execute());
	}

	public static function getRegexType(): TypeValue {
		return self::$regexType
			??= new TypeValue(RegexValue::TYPE, self::getObjectType(), RegexTypeExtension::execute());
	}

	public static function getListType(): TypeValue {
		return self::$listType
			??= new TypeValue(ListValue::TYPE, self::getObjectType(), ListTypeExtension::execute());
	}

	public static function getDictType(): TypeValue {
		return self::$dictType
			??= new TypeValue(DictValue::TYPE, self::getObjectType(), DictTypeExtension::execute());
	}

	public static function getTupleType(): TypeValue {
		return self::$tupleType
			??= new TypeValue(TupleValue::TYPE, self::getObjectType(), TupleTypeExtension::execute());
	}

	public static function getFuncType(): TypeValue {
		return self::$funcType
			??= new TypeValue(FuncValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getIteratorFactoryType(): TypeValue {
		return self::$iteratorFactoryType
			??= new TypeValue(IteratorFactoryValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getModuleType(): TypeValue {
		return self::$moduleType
			??= new TypeValue(ModuleValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getNotImplementedType(): TypeValue {
		return self::$notImplementedType
			??= new TypeValue(NotImplementedValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

}
