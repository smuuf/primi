<?php

declare(strict_types=1);

namespace Smuuf\Primi\Tasks\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Tasks\TaskInterface;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

class CallbackTask implements TaskInterface {

	use StrictObject;

	/** @var FnContainer Callback function. */
	private $fn = null;

	/** @var array Arguments passed to callback. */
	private $args = [];

	public function __construct(FuncValue $fn, array $args = []) {
		$this->fn = $fn;
	}

	public function execute(Context $ctx): void {
		$this->fn->invoke($ctx, $this->args);
	}

}
