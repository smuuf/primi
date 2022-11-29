<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\MethodValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NotImplementedValue;
use \Smuuf\Primi\Values\IteratorFactoryValue;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Modules\AllowedInSandboxTrait;

return new
/**
 * Module housing Primi's basic data types.
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	public function execute(Context $ctx): array {

		return [

			// Super-basic types.
			MagicStrings::TYPE_OBJECT => BuiltinTypes::getObjectType(),
			TypeValue::TYPE => BuiltinTypes::getTypeType(),
			NullValue::TYPE => BuiltinTypes::getNullType(),
			BoolValue::TYPE => BuiltinTypes::getBoolType(),
			NumberValue::TYPE => BuiltinTypes::getNumberType(),
			StringValue::TYPE => BuiltinTypes::getStringType(),
			RegexValue::TYPE => BuiltinTypes::getRegexType(),
			DictValue::TYPE => BuiltinTypes::getDictType(),
			ListValue::TYPE => BuiltinTypes::getListType(),
			TupleValue::TYPE => BuiltinTypes::getTupleType(),

			// Other basic types (basic == they're implemented in PHP).
			FuncValue::TYPE => BuiltinTypes::getFuncType(),
			MethodValue::TYPE => BuiltinTypes::getMethodType(),
			IteratorFactoryValue::TYPE => BuiltinTypes::getIteratorFactoryType(),
			ModuleValue::TYPE => BuiltinTypes::getModuleType(),

			// Other types.
			NotImplementedValue::TYPE => BuiltinTypes::getNotImplementedType(),

		];

	}

};
