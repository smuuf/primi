<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Parser\ParserHandler;
use \Smuuf\Primi\Modules\NativeModule;

/**
 * Native '__engine__.ast' module.
 */
return new class extends NativeModule {

	/**
	 * Return parsed Primi code provided as string represented as (possibly
	 * nested) dicts/lists.
	 *
	 * ```js
	 * tree = ast.parse('tree = ast.parse()')
	 * ```
	 */
	#[PrimiFunc]
	public static function parse(StringValue $source): AbstractValue {
		$ast = (new ParserHandler($source->value))->run();
		return AbstractValue::buildAuto($ast);
	}

};
