<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Scopes\AbstractScope;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

class ContextPushPopWrapper extends AbstractWrapper {

	use StrictObject;

	/** @var Context */
	private $ctx;

	/** @var string Call ID to push to call stack. */
	private $callId;

	/** @var AbstractScope|null (optional) Scope to push to scope stack. */
	private $scope;

	public function __construct(
		Context $ctx,
		string $callId,
		?AbstractScope $scope = null
	) {
		$this->ctx = $ctx;
		$this->callId = $callId;
		$this->scope = $scope;
	}

	/**
	 * Push the call ID and scope (if present) onto call stack and scope stack.
	 */
	public function executeBefore() {

		$this->ctx->pushCall($this->callId);
		if ($this->scope) {
			$this->ctx->pushScope($this->scope);
		}

		return $this->ctx;

	}

	/**
	 * Pop the items from call stack and scope stack.
	 */
	public function executeAfter() {

		$this->ctx->popCall();
		if ($this->scope) {
			$this->ctx->popScope();
		}

	}

}
