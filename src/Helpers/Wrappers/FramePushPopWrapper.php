<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use Smuuf\StrictObject;
use Smuuf\Primi\Context;
use Smuuf\Primi\VM\Frame;

/**
 * @internal
 */
class FramePushPopWrapper extends AbstractWrapper {

	use StrictObject;

	/**
	 * @param Context $ctx Context.
	 * @param Frame $frame Stack frame to push to call stack.
	 */
	public function __construct(
		private readonly Context $ctx,
		private readonly Frame $frame,
	) {}

	public function executeBefore() {
		$this->ctx->setCurrentFrame($this->frame);
	}

	public function executeAfter(): void {
		$this->ctx->setCurrentFrame($this->frame->getParent());
	}

}
