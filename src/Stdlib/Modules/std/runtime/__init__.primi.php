<?php

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Context;
use Smuuf\Primi\VM\Frame;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Structures\CallArgs;

/**
 * Native 'std.runtime' module.
 */
return new class extends NativeModule {

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Return traceback as a list.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function get_stack(
		CallArgs $_,
		Context $ctx
	): AbstractValue {

		$frames = [];
		$frame = $ctx->getCurrentFrame();
		do {
			$frames[] = $frame;
		} while ($frame = $frame->getParent());

		return AbstractValue::buildAuto(\array_map(
			static fn(Frame $f) => $f->getName(),
			$frames,
		));

	}

};
