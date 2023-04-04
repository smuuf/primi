<?php

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Context;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Tasks\Types\CallbackTask;
use Smuuf\Primi\Values\FuncValue;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Structures\CallArgs;

return new
/**
 * Async tools.
 */
class extends NativeModule {

	/**
	 * Run function asynchronously.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function run(CallArgs $args, Context $ctx): NullValue {

		[$fn] = $args->extractPositional(1);
		Func::allow_argument_types(1, $fn, FuncValue::class);

		$ctx->getTaskQueue()->addTask(new CallbackTask($fn));

		return Interned::null();

	}

};
