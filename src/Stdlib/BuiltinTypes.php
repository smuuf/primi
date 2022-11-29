<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

use \Smuuf\StrictObject;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Stdlib\TypeExtensions\BoolTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\BytesTypeExtension;
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
use \Smuuf\Primi\Values\BuiltinTypeValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BytesValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\MethodValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NotImplementedValue;
use \Smuuf\Primi\Values\IteratorFactoryValue;

/**
 * Global provider for builtin global and immutable native types.
 */
abstract class BuiltinTypes {

	use StrictObject;

	private static BuiltinTypeValue $objectType;
	private static BuiltinTypeValue $typeType;
	private static BuiltinTypeValue $nullType;
	private static BuiltinTypeValue $boolType;
	private static BuiltinTypeValue $numberType;
	private static BuiltinTypeValue $stringType;
	private static BuiltinTypeValue $bytesType;
	private static BuiltinTypeValue $regexType;
	private static BuiltinTypeValue $listType;
	private static BuiltinTypeValue $dictType;
	private static BuiltinTypeValue $tupleType;
	private static BuiltinTypeValue $funcType;
	private static BuiltinTypeValue $methodType;
	private static BuiltinTypeValue $iteratorFactoryType;
	private static BuiltinTypeValue $moduleType;

	// Specials.
	private static BuiltinTypeValue $notImplementedType;

	public static function getObjectType(): BuiltinTypeValue {
		return self::$objectType
			??= new BuiltinTypeValue(MagicStrings::TYPE_OBJECT, \null, ObjectTypeExtension::execute(), \false);
	}

	public static function getTypeType(): BuiltinTypeValue {
		return self::$typeType
			??= new BuiltinTypeValue(BuiltinTypeValue::TYPE, self::getObjectType(), TypeTypeExtension::execute());
	}

	public static function getNullType(): BuiltinTypeValue {
		return self::$nullType
			??= new BuiltinTypeValue(NullValue::TYPE, self::getObjectType(), NullTypeExtension::execute());
	}

	public static function getBoolType(): BuiltinTypeValue {
		return self::$boolType
			??= new BuiltinTypeValue(BoolValue::TYPE, self::getObjectType(), BoolTypeExtension::execute());
	}

	public static function getNumberType(): BuiltinTypeValue {
		return self::$numberType
			??= new BuiltinTypeValue(NumberValue::TYPE, self::getObjectType(), NumberTypeExtension::execute());
	}

	public static function getStringType(): BuiltinTypeValue {
		return self::$stringType
			??= new BuiltinTypeValue(StringValue::TYPE, self::getObjectType(), StringTypeExtension::execute());
	}

	public static function getBytesType(): BuiltinTypeValue {
		return self::$bytesType
			??= new BuiltinTypeValue(BytesValue::TYPE, self::getObjectType(), BytesTypeExtension::execute());
	}

	public static function getRegexType(): BuiltinTypeValue {
		return self::$regexType
			??= new BuiltinTypeValue(RegexValue::TYPE, self::getObjectType(), RegexTypeExtension::execute());
	}

	public static function getListType(): BuiltinTypeValue {
		return self::$listType
			??= new BuiltinTypeValue(ListValue::TYPE, self::getObjectType(), ListTypeExtension::execute());
	}

	public static function getDictType(): BuiltinTypeValue {
		return self::$dictType
			??= new BuiltinTypeValue(DictValue::TYPE, self::getObjectType(), DictTypeExtension::execute());
	}

	public static function getTupleType(): BuiltinTypeValue {
		return self::$tupleType
			??= new BuiltinTypeValue(TupleValue::TYPE, self::getObjectType(), TupleTypeExtension::execute());
	}

	public static function getFuncType(): BuiltinTypeValue {
		return self::$funcType
			??= new BuiltinTypeValue(FuncValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getMethodType(): BuiltinTypeValue {
		return self::$methodType
			??= new BuiltinTypeValue(MethodValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getIteratorFactoryType(): BuiltinTypeValue {
		return self::$iteratorFactoryType
			??= new BuiltinTypeValue(IteratorFactoryValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getModuleType(): BuiltinTypeValue {
		return self::$moduleType
			??= new BuiltinTypeValue(ModuleValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

	public static function getNotImplementedType(): BuiltinTypeValue {
		return self::$notImplementedType
			??= new BuiltinTypeValue(NotImplementedValue::TYPE, self::getObjectType(), ForbiddenTypeExtension::execute());
	}

}
