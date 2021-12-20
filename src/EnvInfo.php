<?php

declare(strict_types=1);

namespace Smuuf\Primi;

/**
 * Static helper for providing information about current runtime environment.
 */
abstract class EnvInfo {

	private static ?bool $runningInPhar = \null;
	private static ?string $homeDir = \null;
	private static ?string $currentUser = \null;
	private static ?string $bestTempDir = \null;

	/**
	 * Get Primi build ID (if executed within compiled Phar, else 'dev').
	 */
	public static function getPrimiBuild(): string {

		return self::isRunningInPhar()
			? \constant('BUILD_ID')
			: 'dev';

	}

	/**
	 * Is current runtime being executed within Phar?
	 */
	public static function isRunningInPhar(): bool {
		return self::$runningInPhar
			?? (self::$runningInPhar = self::determineIsRunningInPhar());
	}

	/**
	 * Return current user's HOME directory, or `null` if there's not any.
	 */
	public static function getHomeDir(): ?string {
		return self::$homeDir
			?? (self::$homeDir = (\getenv('HOME') ?: \null));
	}

	/**
	 * Return current user's username.
	 */
	public static function getCurrentUser(): string {
		return self::$currentUser
			?? (self::$currentUser = \getenv('USER'));
	}

	/**
	 * Return path to best temporary dir available for current runtime.
	 */
	public static function getBestTempDir(): ?string {

		return self::$bestTempDir
			?? (self::$bestTempDir = self::determineBestTempDir());

	}

	//
	// Helpers.
	//

	/**
	 * Determine if Phar extension is enabled and if we're being executed
	 * inside Phar.
	 */
	private static function determineIsRunningInPhar(): bool {
		return \extension_loaded('phar') && \Phar::running();
	}

	/**
	 * Determine best directory to use as temporary directory for Primi.
	 */
	private static function determineBestTempDir(): ?string {

		// If not running inside Phar, default dir will be in Primi's temp
		// directory.
		if (!EnvInfo::isRunningInPhar()) {

			$tempDir = __DIR__ . '/../temp';
			Logger::debug("Using library temp directory '$tempDir'");
			return $tempDir;

		}

		//
		// Now we handle situations where we're being executed as Phar archive.
		//

		// Determine if we can get home directory for current user.
		$homeDir = EnvInfo::getHomeDir();
		if ($homeDir === \null) {

			$currentUser = EnvInfo::getCurrentUser();
			Logger::debug("Current user '$currentUser' has no home directory. Temp directory disabled");

			// Current user has no home directory, we're disabling temp dir.
			return \null;

		}

		// Determine if current user's home directory contains ".primi" file/dir.
		$tempDir = "{$homeDir}/.primi";
		if (!\file_exists($tempDir)) {

			// Home directory does not contain ".primi" - try creating it.
			$success = @mkdir($tempDir);
			if ($success === \false) {
				Logger::debug("Failed to create temp directory '$tempDir'. Temp directory disabled");
				return \null;
			}

		}

		// Is ".primi" file/dir in home dir a file? We need it to be a dir...
		if (\is_file($tempDir)) {
			Logger::debug("Path to temp directory '$tempDir' exists, but is a file. Temp directory disabled");
			return \null;
		}

		// And this dir needs to be writable by us...
		if (!\is_writable($tempDir)) {
			Logger::debug("Temp directory '$tempDir' is not writable. Temp directory disabled");
			return \null;
		}

		// The ".primi" in home dir is a directory and we can write to it,
		// let's use it!
		Logger::debug("Using temp directory '$tempDir'");
		return $tempDir;

	}

}
