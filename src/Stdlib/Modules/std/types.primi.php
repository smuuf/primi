<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Modules\NativeModule;

/**
 * Native 'std.types' module.
 */
return new class extends NativeModule {

	public function execute(Context $ctx): array {

		return [

			// Super-basic types.
			'object' => StaticTypes::getObjectType(),
			'type' => StaticTypes::getTypeType(),
			'null' => StaticTypes::getNullType(),
			'bool' => StaticTypes::getBoolType(),
			'number' => StaticTypes::getNumberType(),
			'string' => StaticTypes::getStringType(),
			'regex' => StaticTypes::getRegexType(),
			'dict' => StaticTypes::getDictType(),
			'list' => StaticTypes::getListType(),

			// Other native types (native == they're implemented in PHP).
			'Function' => StaticTypes::getFunctionType(),
			'Generator' => StaticTypes::getGeneratorType(),
			'Module' => StaticTypes::getModuleType(),

		];

	}

};
