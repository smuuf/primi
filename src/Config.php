<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

final class Config {

	use StrictObject;

	private function __construct() {
		// Prevent instantiation.
	}

	//
	// Callstack limit.
	//

	/**
	 * If greater than one, this number sets the maximum call stack size.
	 * When this maximum is reached, RuntimeError is thrown.
	 *
	 * @var int
	 */
	private static int $callStackLimit = 1024;

	/**
	 * Set callstack limit for runtime `Context` objects that are created from
	 * now on. This is the maximum number of allowed nested function calls.
	 *
	 * This can be set to zero to disable any limiting.
	 *
	 * @see Context
	 */
	public static function setCallStackLimit(int $limit): void {

		if ($limit < 0) {
			throw new EngineError('Callstack limit must be positive or zero');
		}

		self::$callStackLimit = $limit;

	}

	/**
	 * Return current value of configured callstack limit.
	 */
	public static function getCallStackLimit(): int {
		return self::$callStackLimit;
	}

	//
	// Posix signal handling.
	//

	/**
	 * Automatically determine if POSIX signals should be handled or not -
	 * handling will be enabled only when running in CLI mode.
	 */
	public const POSIX_SIGNALS_AUTO = null;

	/**
	 * Enable handling of POSIX signals by Primi engine.
	 */
	public const POSIX_SIGNALS_ENABLED = true;

	/**
	 * Disable handling of POSIX signals by Primi engine.
	 */
	public const POSIX_SIGNALS_DISABLED = false;

	/**
	 * Set if POSIX signals should be received and rendered into the Primi
	 * runtime. For example SIGTERM and SIGINT signals will raise an appropriate
	 * error.
	 */
	private static ?bool $posixSignalHandling = self::POSIX_SIGNALS_AUTO;

	/**
	 * Should POSIX signals be handled by the engine?
	 *
	 * @see self::POSIX_SIGNALS_AUTO
	 * @see self::POSIX_SIGNALS_ENABLED
	 * @see self::POSIX_SIGNALS_DISABLED
	 */
	public static function setPosixSignalHandling(
		?bool $state = self::POSIX_SIGNALS_AUTO
	): void {
		self::$posixSignalHandling = $state;
	}

	/**
	 * Return current value of configured callstack limit.
	 */
	public static function getPosixSignalHandling(): ?bool {
		return self::$posixSignalHandling;
	}

	/**
	 * Returns `true` or `false` if POSIX signals should be handled based
	 * by the actual configuration.
	 */
	public static function getEffectivePosixSignalHandling(): bool {

		// Explicit enabled or disabled.
		if (\is_bool(self::$posixSignalHandling)) {
			return self::$posixSignalHandling;
		}

		// Automatic detection.
		return \PHP_SAPI === 'cli';

	}

	//
	// Posix signal handling - does SIGQUIT inject a debugging session?
	//

	private static bool $sigQuitDebugging = \true;

	/**
	 * If POSIX signal handling is enabled, received SIGQUIT causes a debugging
	 * session to be injected into currently executed code.
	 *
	 * SIGQUIT can usually be sent from terminal via `CTRL+\`.
	 */
	public static function setSigQuitDebugging(bool $state): void {
		self::$sigQuitDebugging = $state;
	}


	/**
	 * Return current config value of "SIGQUIT debugging".
	 */
	public static function getSigQuitDebugging(): bool {
		return self::$sigQuitDebugging;
	}

}
