<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\StackFrame;
use \Smuuf\Primi\Scope;

class ContextPushPopWrapper extends AbstractWrapper {

	use StrictObject;

	private Context $ctx;

	/** Call frame to push to call stack. */
	private ?StackFrame $call;

	/** Scope to push to scope stack. (optional) */
	private ?Scope $scope;

	public function __construct(
		Context $ctx,
		?StackFrame $call = \null,
		?Scope $scope = \null
	) {
		$this->ctx = $ctx;
		$this->call = $call;
		$this->scope = $scope;
	}

	/**
	 * Push the call ID and scope (if present) onto call stack and scope stack.
	 *
	 * @return mixed
	 */
	public function executeBefore() {

		$this->ctx->pushCallScopePair(
			$this->call,
			$this->scope,
		);

		return $this->ctx;

	}

	/**
	 * Pop the items from call stack and scope stack.
	 */
	public function executeAfter(): void {

		$this->ctx->popCallScopePair(
			(bool) $this->call,
			(bool) $this->scope,
		);

		unset($this->call, $this->scope);

	}

}
