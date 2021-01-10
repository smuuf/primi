<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\CallFrame;
use \Smuuf\Primi\Scopes\AbstractScope;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

class ContextPushPopWrapper extends AbstractWrapper {

	use StrictObject;

	private Context $ctx;

	/** Call frame to push to call stack. */
	private ?CallFrame $call;

	/** Scope to push to scope stack. (optional) */
	private ?AbstractScope $scope;

	public function __construct(
		Context $ctx,
		?CallFrame $call = null,
		?AbstractScope $scope = null
	) {
		$this->ctx = $ctx;
		$this->call = $call;
		$this->scope = $scope;
	}

	/**
	 * Push the call ID and scope (if present) onto call stack and scope stack.
	 */
	public function executeBefore() {

		if ($this->call) {
			$this->ctx->pushCall($this->call);
		}

		if ($this->scope) {
			$this->ctx->pushScope($this->scope);
		}

		return $this->ctx;

	}

	/**
	 * Pop the items from call stack and scope stack.
	 */
	public function executeAfter() {

		if ($this->call) {
			$this->ctx->popCall();
			unset($this->call);
		}

		if ($this->scope) {
			$this->ctx->popScope();
			unset($this->scope);
		}

	}

}
