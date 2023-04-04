<?php

declare(strict_types=1);

namespace Smuuf\Primi\Tasks\Types;

use Smuuf\StrictObject;
use Smuuf\Primi\Context;
use Smuuf\Primi\Structures\CallArgs;
use Smuuf\Primi\Tasks\TaskInterface;
use Smuuf\Primi\Values\FuncValue;

class CallbackTask implements TaskInterface {

	use StrictObject;

	/** The callback function. */
	private FuncValue $fn;

	/** Arguments passed to callback. */
	private CallArgs $args;

	public function __construct(FuncValue $fn, ?CallArgs $args = null) {
		$this->fn = $fn;
		$this->args = $args ?? CallArgs::getEmpty();
	}

	public function execute(Context $ctx): void {
		$this->fn->invoke($ctx, $this->args);
	}

}
