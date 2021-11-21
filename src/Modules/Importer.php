<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Logger;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\ImportError;
use \Smuuf\Primi\Ex\ModuleNotFoundError;
use \Smuuf\Primi\Ex\ImportBeyondTopException;
use \Smuuf\Primi\Ex\ImportRelativeWithoutParentException;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;
use \Smuuf\Primi\Helpers\Wrappers\ImportStackWrapper;
use \Smuuf\Primi\Modules\Dotpath;
use \Smuuf\Primi\StackFrame;

/**
 * @internal
 */
class Importer {

	use StrictObject;

	private const MODULE_FILE_EXTENSIONS = [
		NativeModuleLoader::class => ".primi.php",
		PrimiModuleLoader::class => ".primi",
	];

	/** Runtime context object. */
	private Context $ctx;

	/** @var array<string, ModuleValue> Storage for loaded modules. */
	private array $loaded = [];

	/** @var array<string> Import stack for circular import detection. */
	private array $importStack = [];

	/** @var array<string> Known import paths. */
	private array $importPaths = [];

	/**
	 * Array dict cache for storing determined base paths when looking for
	 * modules in various import paths.
	 *
	 * @var array<string, ?string>
	 */
	private array $baseCache = [];

	public function __construct(
		Context $ctx,
		array $importPaths = []
	) {
		$this->ctx = $ctx;
		$this->importPaths = $importPaths;
	}

	public function getLoaded(): array {
		return $this->loaded;
	}

	public function pushImport(string $importId): bool {

		// If this import ID is already in the stack, it is a circular import.
		if (\in_array($importId, $this->importStack, \true)) {
			return \false;
		}

		$this->importStack[] = $importId;
		return \true;

	}

	public function popImport(): void {
		\array_pop($this->importStack);
	}

	/**
	 * Fetch module value object by its dot path.
	 *
	 * If the module has been already loaded, it will be just returned. If the
	 * module has not been loaded yet, it will be loaded, stored into cache, and
	 * then returned.
	 */
	public function getModule(string $dotpath): ModuleValue {

		$originPackage = '';
		if ($currentModule = $this->ctx->getCurrentModule()) {
			$originPackage = $currentModule->getPackage();
		}

		Logger::debug("Getting module '$dotpath'");

		try {
			$dp = new Dotpath($dotpath, $originPackage);
		} catch (ImportRelativeWithoutParentException $e) {
			throw new ImportError("Relative import without origin package");
		} catch (ImportBeyondTopException $e) {
			throw new ImportError("Relative import {$e->getMessage()} reached beyond top-level package");
		}

		$dotpathString = $dp->getAbsolute();
		if (isset($this->loaded[$dotpathString])) {
			Logger::debug("Returned '$dotpathString' from module cache");
			return $this->loaded[$dotpathString];
		}

		// Determine which import path matches the first part of dotpath.
		// For example if we're trying to import module 'a.b.c', this returns
		// the first import path that contains the module 'a' (a package/dir 'a'
		// or module 'a.primi' or 'a.primi.php').
		$base = $this->determineBase($dp, $this->importPaths);

		if ($base) {

			Logger::debug("Determined base '$base' for module '{$dp->getAbsolute()}'");
			if ($module = $this->tryWithBase($base, $dp)) {
				return $module;
			}

		}

		Logger::debug("Module '$dotpath' could not be found");
		throw new ModuleNotFoundError($dotpath);

	}

	/**
	 * Go through each of the possible paths from where modules can be imported
	 * and return the first path that actually contains the first part of the
	 * dotpath of the module that is requested to be imported.
	 *
	 * For example, for given import paths (order is important) and their
	 * contents:
	 *
	 * ```
	 * - /xxx/
	 *   - a/bruh.primi
	 *   - b/bruh.primi
	 * - /yyy/
	 *   - b/bruh.primi
	 *   - c/bruh.primi
	 * - /zzz/
	 *   - c/bruh.primi
	 *   - d/bruh.primi
	 * ```
	 *
	 * ... importing 'c.bruh' would look through '/xxx/' first and then '/yyy/',
	 * where 'c' directory is present - the resulting base is determined as
	 * '/yyy/', ignoring the '/zzz/', because '/yyy/' was before it and thus
	 * had a higher priority.
	 */
	private function determineBase(
		Dotpath $dp,
		array $possiblePaths
	): ?string {

		$first = $dp->getFirstPart();
		if (\array_key_exists($first, $this->baseCache)) {
			return $this->baseCache[$first];
		}

		foreach ($possiblePaths as $base) {
			$path = "$base/$first";
			if (self::isModule($path)) {
				return $this->baseCache[$first] = $base;
			}
		}

		return $this->baseCache[$first] = \null;

	}

	private function tryWithBase(string $basepath, Dotpath $dp): ?ModuleValue {

		$module = \null;

		// Go through each of the paths (originating from this basepath) leading
		// to the target module - and try to import each one of them.
		foreach ($dp->iterPaths($basepath) as [$dotpath, $package, $filepath]) {

			if (!$module = $this->importModule($filepath, $dotpath, $package)) {

				// Stop importing if any of the paths doesn't represent a
				// module. Modules imported so far are still loaded and cached.
				return \null;

			}

		}

		// If the whole tree of module was successfully imported/fetched, return
		// the last one (which is what the user wants - when doing
		// 'import a.b.c' the 'c' module is returned from this function).
		return $module;

	}

	private function importModule(
		string $filepath,
		string $dotpath,
		string $packageDotpath
	): ?ModuleValue {

		if (isset($this->loaded[$dotpath])) {
			return $this->loaded[$dotpath];
		}

		// If the module is - in fact - a package (dir), import it as such.
		if (\is_dir($filepath)) {
			$module = $this->importPackage($filepath, $dotpath);
			return $module;
		}

		// Build dict of possible filenames with supported extensions
		// (differentiating whether the module is a native/PHP module or a
		// Primi module). The keys of the dict are the class names of loaders
		// that know how to load that particular extension.
		// Native modules ('.../module_name.primi.php') have priority over
		// Primi modules ('.../module_name.primi').
		$candidates = self::withSupportedExtensions($filepath);
		foreach ($candidates as $loader => $candidate) {

			// If this file with this extension does in fact exist, load it
			// using the correct loader.
			if (!\is_file($candidate)) {
				continue;
			}

			return $this->loadModule(
				$loader,
				$candidate,
				$dotpath,
				$packageDotpath
			);

		}

		// No filepath that corresponds to this dotpath exists, return null,
		// telling the caller that this import was a failure.
		return \null;

	}

	private function importPackage(
		string $filepath,
		string $dotpath
	): ModuleValue {

		// Try importing the package's init file.
		$init = "$filepath/__init__";
		if ($module = $this->importModule($init, $dotpath, $dotpath)) {
			return $module;
		}

		Logger::debug("Creating implicit package '$dotpath' from '$filepath'");

		// If the package has no init file, just create an empty module object.
		$module = new ModuleValue($dotpath, $dotpath, new Scope);
		$this->loaded[$dotpath] = $module;

		return $module;

	}

	private function loadModule(
		string $loader,
		string $filepath,
		string $dotpath,
		string $packageDotpath
	): ModuleValue {

		Logger::debug("Loading module '$dotpath' from file '$filepath'");

		$wrapper = new ImportStackWrapper($this, $filepath, $dotpath);
		return $wrapper->wrap(function() use (
				$loader,
				$filepath,
				$dotpath,
				$packageDotpath
			) {

				// Imported module will have its own global scope.
				$scope = new Scope;
				$module = new ModuleValue(
					$dotpath,
					$packageDotpath,
					$scope
				);

				// Put the newly created module immediately in the "loaded
				// module" cache. Relative imports made from this new module
				// might need to load this module again (because if module 'a'
				// wants to import '.b' relatively, the resulting absolute
				// module path is 'a.b' - and import mechanism wants to import
				// 'a' first and then 'b'). And we don't want the new module to
				// be imported again (that would result in circular import).
				$this->loaded[$dotpath] = $module;

				$frame = new StackFrame("<module>", $module);
				$wrapper = new ContextPushPopWrapper($this->ctx, $frame, $scope);

				$wrapper->wrap(function() use (
					$loader,
					$filepath,
					$dotpath,
					$packageDotpath
				) {

					return $loader::loadModule(
						$this->ctx,
						$filepath,
						$dotpath,
						$packageDotpath
					);

				});

				return $module;

			});

	}

	// Static Helpers.

	/**
	 * Returns true if a filepath (without extension) represents a module
	 * or a package (which is a kind of module). Otherwise returns false.
	 */
	private static function isModule(string $filepath): bool {

		Logger::debug("Is '$filepath' a module?");

		// Just a simple directory is a package (a package is a module).
		if (\is_dir($filepath)) {
			Logger::debug("  YES (package)");
			return \true;
		}

		// Try adding supported module (file) extensions and go see if it
		// matches the filesystem contents.
		$candidates = self::withSupportedExtensions($filepath);
		foreach ($candidates as $candidate) {
			if (\is_file($candidate)) {
				Logger::debug("  YES (module)");
				return \true;
			}
		}

		Logger::debug("  NO");
		return \false;

	}

	private static function withSupportedExtensions(string $filepath): array {

		$result = [];
		foreach (self::MODULE_FILE_EXTENSIONS as $loaderClass => $ext) {
			$result[$loaderClass] = "{$filepath}{$ext}";
		}

		return $result;

	}

}
