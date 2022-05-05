<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Drivers\VoidIoDriver;
use \Smuuf\Primi\Drivers\TerminalIoDriver;
use \Smuuf\Primi\Drivers\StdIoDriverInterface;

class Config {

	use StrictObject;

	private bool $sandboxMode = true;

	/**
	 * Return default config for current environment.
	 *
	 * If Primi is running in CLI (terminal) the sandbox mode is disabled by
	 * default.
	 *
	 * If Primi is running anywhere else, sandbox mode is enabled by default.
	 */
	final public static function buildDefault(): Config {

		// If not running in CLI, enable sandbox mode by default.
		if (!EnvInfo::isRunningInCli()) {
			return new self;
		}

		$config = new self;
		$config->setSandboxMode(false);
		$config->setStdIoDriver(new TerminalIoDriver);
		return $config;

	}

	// Sandbox mode.

	public function setSandboxMode(bool $mode): void {
		$this->sandboxMode = $mode;
	}

	public function getSandboxMode(): bool {
		return $this->sandboxMode;
	}

	//
	// Temporary directory.
	//

	/**
	 * Path to temporary directory for caching or `null` for disabled caching.
	 */
	private ?string $tempDir = '';

	/**
	 * Set path to temporary directory for caching various stuff for the Primi
	 * engine.
	 *
	 * Default value is empty string, which means default temporary
	 * directory located inside the Primi library will be used.
	 *
	 * If necessary, another _existing_ directory can be specified to be used
	 * as the temporary directory.
	 *
	 * If the temporary directory is set to `null`, caching will be disabled.
	 */
	public function setTempDir(?string $path): void {

		$this->tempDir = $path !== null
			? Func::validate_dirs([$path])[0]
			: null;

	}

	public function getTempDir(): ?string {

		return $this->tempDir === ''
			? EnvInfo::getBestTempDir()
			: null;

	}

	//
	// Paths for finding modules.
	//

	/** @var array<string> */
	private array $importPaths = [
		__DIR__ . '/Stdlib/Modules',
	];

	public function addImportPath(string $path): void {
		$this->importPaths[] = Func::validate_dirs([$path])[0];
	}

	/**
	 * @return array<string>
	 */
	public function getImportPaths(): array {
		return $this->importPaths;
	}

	//
	// Callstack limit.
	//

	/**
	 * If greater than one, this number sets the maximum call stack size.
	 * If this maximum is reached, `\Smuuf\Primi\Ex\RuntimeError` is thrown.
	 */
	private int $callStackLimit = 4096;

	/**
	 * Set maximum limit for runtime call stack object. This is the maximum
	 * number of allowed nested function calls.
	 *
	 * This can be set to zero to disable limiting.
	 *
	 * @see Context
	 */
	public function setCallStackLimit(int $limit): void {

		if ($limit < 0) {
			throw new EngineError('Callstack limit must be positive or zero');
		}

		$this->callStackLimit = $limit;

	}

	/**
	 * Return current value of configured callstack limit.
	 */
	public function getCallStackLimit(): int {
		return $this->callStackLimit;
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
	private ?bool $posixSignalHandling = self::POSIX_SIGNALS_AUTO;

	/**
	 * Should POSIX signals be handled by the engine?
	 *
	 * @see self::POSIX_SIGNALS_AUTO
	 * @see self::POSIX_SIGNALS_ENABLED
	 * @see self::POSIX_SIGNALS_DISABLED
	 */
	public function setPosixSignalHandling(
		?bool $state = self::POSIX_SIGNALS_AUTO
	): void {
		$this->posixSignalHandling = $state;
	}

	/**
	 * Return current value of configured callstack limit.
	 */
	public function getPosixSignalHandling(): ?bool {
		return $this->posixSignalHandling;
	}

	/**
	 * Returns `true` or `false` if POSIX signals should be handled based
	 * by the actual configuration.
	 */
	public function getEffectivePosixSignalHandling(): bool {

		// Explicit enabled or disabled.
		if (\is_bool($this->posixSignalHandling)) {
			return $this->posixSignalHandling;
		}

		// Automatic detection - handle POSIX signals only in CLI.
		return EnvInfo::isRunningInCli();

	}

	//
	// Posix signal handling - does SIGQUIT inject a debugging session?
	//

	private bool $sigQuitDebugging = \true;

	/**
	 * If POSIX signal handling is enabled, received SIGQUIT causes a debugging
	 * session to be injected into currently executed code.
	 *
	 * SIGQUIT can usually be sent from terminal via `CTRL+\`.
	 */
	public function setSigQuitDebugging(bool $state): void {
		$this->sigQuitDebugging = $state;
	}

	/**
	 * Return current config value of "SIGQUIT debugging".
	 */
	public function getSigQuitDebugging(): bool {
		return $this->sigQuitDebugging;
	}

	//
	// Standard IO driver.
	//

	private StdIoDriverInterface $stdIoDriver;

	public function setStdIoDriver(StdIoDriverInterface $stdIoDriver): void {
		$this->stdIoDriver = $stdIoDriver;
	}

	public function getStdIoDriver(): StdIoDriverInterface {
		return $this->stdIoDriver
			?? ($this->stdIoDriver = new VoidIoDriver);
	}

}
