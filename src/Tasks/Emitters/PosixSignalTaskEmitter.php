<?php

declare(strict_types=1);

namespace Smuuf\Primi\Tasks\Emitters;

use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Tasks\TaskQueue;
use Smuuf\Primi\Tasks\Types\PosixSignalTask;

abstract class PosixSignalTaskEmitter {

	/**
	 * A list of signals that have been registered into `pcntl_signal`
	 * and it is not necessary to do so again.
	 *
	 * @var int[]
	 */
	private static $signalsToCatch = [];

	/**
	 * List of registered task queues to which we'll push a signal
	 * task each time a signal is caught.
	 *
	 * @var TaskQueue[]
	 */
	private static $queues = [];

	public static function catch(int $signum): void {

		if (!\function_exists('pcntl_async_signals')) {
			return;
		}

		// Call this only once - before registering the first signal handler.
		if (!self::$signalsToCatch) {
			\pcntl_async_signals(\true);
		}

		// A specific signal can be registered only once.
		if (\in_array($signum, self::$signalsToCatch, \true)) {
			return;
		}

		$signalsToCatch[] = $signum;

		// Let's make sure any already registered signal handler is also called.
		$original = \pcntl_signal_get_handler($signum);
		\pcntl_signal($signum, function(...$args) use ($original) {

			// Do our signal handling.
			self::handle(...$args);

			// Call also the original signal handler (if it was a callable).
			if (\is_callable($original)) {
				$original(...$args);
			}

		});

	}

	public static function registerTaskQueue(TaskQueue $queue): void {

		// A specific receiver instance can be added only once.
		if (\in_array($queue, self::$queues, \true)) {
			throw new EngineInternalError('This receiver is already registered');
		}

		self::$queues[] = $queue;

	}

	/**
	 * Remove a registered receiver from signal receiving.
	 *
	 * You should always do this after you don't need to use the received, to
	 * avoid keeping unnecessary reference to the receiver object - to avoid
	 * leaks caused by blocking proper garbage collection.
	 */
	public static function unregisterTaskQueue(TaskQueue $queue): void {

		$index = \array_search($queue, self::$queues, \true);
		if ($index === \false) {
			throw new EngineInternalError('This receiver was not previously registered');
		}

		unset(self::$queues[$index]);

	}

	public static function handle(int $signum): void {

		foreach (self::$queues as $rec) {
			$rec->addTask(new PosixSignalTask($signum));
		}

	}

}
