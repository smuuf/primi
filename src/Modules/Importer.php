<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Config;
use \Smuuf\Primi\Ex\ModuleNotFoundError;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\StrictObject;
use \Smuuf\Primi\Helpers\Wrappers\ImportStackWrapper;
use \Smuuf\Primi\Modules\DotPath;

class Importer {

	use StrictObject;

	/** @var array<string, AbstractValue> Storage for loaded modules. */
	private array $loaded = [];

	/** @var array<string> Import stack for circular import detection. */
	private array $importStack = [];

	private NativeModuleLoader $nativeModuleLoader;
	private PrimiModuleLoader $primiModuleLoader;

	public function __construct(Context $ctx) {

		// Reverse the list of module dirs, so that the latest ones have
		// priority and are tried first.
		$this->nativeModuleLoader = new NativeModuleLoader(
			$ctx,
			array_reverse(Config::getNativeModuleDirs())
		);

		$this->primiModuleLoader = new PrimiModuleLoader(
			$ctx,
			array_reverse(Config::getPrimiModuleDirs())
		);

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
	public function fetchModule(string $dotPathString): AbstractValue {

		$dp = new DotPath($dotPathString);

		// Try native module first.
		if ($path = $this->nativeModuleLoader->resolveModulePath($dp)) {
			return $this->cachedOrNew($path, $this->nativeModuleLoader, $dp);
		}

		// Try Primi module if native module is not found under this dotpath.
		if ($path = $this->primiModuleLoader->resolveModulePath($dp)) {
			return $this->cachedOrNew($path, $this->primiModuleLoader, $dp);
		}

		throw new ModuleNotFoundError($dotPathString);

	}

	private function cachedOrNew(
		string $path,
		AbstractModuleLoader $loader,
		DotPath $dotPath
	): ModuleValue {

		if (isset($this->loaded[$path])) {
			return $this->loaded[$path];
		}

		$wrapper = new ImportStackWrapper($this, $path, $dotPath);
		return $wrapper->wrap(function() use ($loader, $path, $dotPath) {

			$module = $loader->loadModule($path, $dotPath->getOriginal());
			return $this->loaded[$path] = $module;;

		});

	}

}
