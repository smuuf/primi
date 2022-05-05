<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Structures\CallArgs;

/**
 * Native 'std.runtime' module.
 */
return new class extends NativeModule {

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Return traceback as a list.
	 *
	 * @primi.func(no-stack, call-conv: callargs)
	 */
	public static function get_stack(
		CallArgs $_,
		Context $ctx
	): AbstractValue {

		return AbstractValue::buildAuto(\array_map(
			fn($f) => $f->asString(),
			$ctx->getCallStack()
		));

	}

};
