<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Tools\BuildDocs\Entities\Module;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\IteratorFactoryValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\TypeValue;

return new
/**
 * Module housing Primi's basic data types.
 */
class extends NativeModule {

	public function execute(Context $ctx): array {

		return [

			// Super-basic types.
			MagicStrings::TYPE_OBJECT => StaticTypes::getObjectType(),
			TypeValue::TYPE => StaticTypes::getTypeType(),
			NullValue::TYPE => StaticTypes::getNullType(),
			BoolValue::TYPE => StaticTypes::getBoolType(),
			NumberValue::TYPE => StaticTypes::getNumberType(),
			StringValue::TYPE => StaticTypes::getStringType(),
			RegexValue::TYPE => StaticTypes::getRegexType(),
			DictValue::TYPE => StaticTypes::getDictType(),
			ListValue::TYPE => StaticTypes::getListType(),
			TupleValue::TYPE => StaticTypes::getTupleType(),

			// Other basic types (basic == they're implemented in PHP).
			FuncValue::TYPE => StaticTypes::getFuncType(),
			IteratorFactoryValue::TYPE => StaticTypes::getIteratorFactoryType(),
			ModuleValue::TYPE => StaticTypes::getModuleType(),

		];

	}

};
