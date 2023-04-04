<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use Smuuf\Primi\Tasks\TaskQueue;
use Smuuf\Primi\Tasks\Emitters\PosixSignalTaskEmitter;

class CatchPosixSignalsWrapper extends AbstractWrapper {

	/**
	 * Dictionary for tracking how many times this wrapper registered some
	 * specific task queue (identified by its ID) to the POSIX signal handler.
	 *
	 * The task queue is registered before executing the wrapped function.
	 * The queue is then unregistered after the most outer wrapper exits.
	 *
	 * This allows us to have nested 'catch signals' wrappers and at the same
	 * time we don't keep the task queue referenced in `PosixSignalTaskEmitter`
	 * if no longer necessary (so they can be garbage collected and we don't
	 * risk memory leaks).
	 *
	 * @var array<string, int>
	 */
	private static $level = [];

	/** Send Posix signal tasks to this queue. */
	private TaskQueue $tq;

	/** Unique ID for each TaskQueue instance. */
	private string $tqId;

	public function __construct(TaskQueue $tq) {
		$this->tq = $tq;
		$this->tqId = $tq->getId();
	}

	/**
	 * @return mixed
	 */
	public function executeBefore() {

		// If this is the first (outermost) context that uses this task queue,
		// register the queue from signal task emitter.
		if (empty(self::$level[$this->tqId])) {
			PosixSignalTaskEmitter::registerTaskQueue($this->tq);
			self::$level[$this->tqId] = 1;
		} else {
			self::$level[$this->tqId]++;
		}

	}

	public function executeAfter(): void {

		self::$level[$this->tqId]--;

		// If this was the last (outermost) context that uses this task queue,
		// unregister the queue from signal task emitter.
		if (self::$level[$this->tqId] === 0) {
			PosixSignalTaskEmitter::unregisterTaskQueue($this->tq);
		}

		unset($this->tq);

	}

}
