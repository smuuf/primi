<?php

declare(strict_types=1);

namespace Smuuf\Primi\Tasks\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Tasks\TaskInterface;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\StrictObject;

class CallbackTask implements TaskInterface {

	use StrictObject;

	/** @var FuncValue Callback function. */
	private $fn = null;

	/** @var array Arguments passed to callback. */
	private $args = [];

	public function __construct(FuncValue $fn, array $args = []) {
		$this->fn = $fn;
		$this->args = $args;
	}

	public function execute(Context $ctx): void {
		$this->fn->invoke($ctx, $this->args);
	}

}
