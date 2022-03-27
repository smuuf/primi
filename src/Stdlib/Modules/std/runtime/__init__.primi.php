<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Modules\DeniedInSandboxTrait;

/**
 * Native 'std.runtime' module.
 */
return new class extends NativeModule {

	use DeniedInSandboxTrait;

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Return traceback as a list.
	 *
	 * @primi.function(no-stack, inject-context)
	 */
	public static function get_stack(Context $ctx): AbstractValue {
		return AbstractValue::buildAuto(\array_map(
			fn($f) => (string) $f,
			$ctx->getCallStack()
		));
	}

};
