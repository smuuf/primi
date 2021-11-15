<?php

namespace Smuuf\Primi\Stdlib;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Values\TypeValue;
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
	private static TypeValue $functionType;
	private static TypeValue $generatorType;
	private static TypeValue $moduleType;

	public static function getObjectType(): TypeValue {
		return self::$objectType
			??= new TypeValue('object', \null, ObjectTypeExtension::execute());
	}

	public static function getTypeType(): TypeValue {
		return self::$typeType
			??= new TypeValue('type', self::getObjectType(), TypeTypeExtension::execute());
	}

	public static function getNullType(): TypeValue {
		return self::$nullType
			??= new TypeValue('Null', self::getObjectType(), NullTypeExtension::execute());
	}

	public static function getBoolType(): TypeValue {
		return self::$boolType
			??= new TypeValue('bool', self::getObjectType(), BoolTypeExtension::execute());
	}

	public static function getNumberType(): TypeValue {
		return self::$numberType
			??= new TypeValue('number', self::getObjectType(), NumberTypeExtension::execute());
	}

	public static function getStringType(): TypeValue {
		return self::$stringType
			??= new TypeValue('string', self::getObjectType(), StringTypeExtension::execute());
	}

	public static function getRegexType(): TypeValue {
		return self::$regexType
			??= new TypeValue('regex', self::getObjectType(), RegexTypeExtension::execute());
	}

	public static function getListType(): TypeValue {
		return self::$listType
			??= new TypeValue('list', self::getObjectType(), ListTypeExtension::execute());
	}

	public static function getDictType(): TypeValue {
		return self::$dictType
			??= new TypeValue('dict', self::getObjectType(), DictTypeExtension::execute());
	}

	public static function getTupleType(): TypeValue {
		return self::$tupleType
			??= new TypeValue('tuple', self::getObjectType(), TupleTypeExtension::execute());
	}

	public static function getFunctionType(): TypeValue {
		return self::$functionType
			??= new TypeValue('Function', self::getObjectType(), []);
	}

	public static function getGeneratorType(): TypeValue {
		return self::$generatorType
			??= new TypeValue('Generator', self::getObjectType(), []);
	}

	public static function getModuleType(): TypeValue {
		return self::$moduleType
			??= new TypeValue('Module', self::getObjectType(), []);
	}

}
