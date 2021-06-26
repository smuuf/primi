<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\StrictObject;

abstract class AbstractModuleLoader {

	use StrictObject;

	/** @var array<string> List of directories to find stuff in. */
	protected array $paths = [];

	/** Context instance */
	protected Context $ctx;

	public function __construct(Context $ctx, array $paths) {

		$this->ctx = $ctx;
		$this->paths = Func::validate_dirs($paths);

	}

	public function resolveModulePath(DotPath $dp): ?string {

		foreach ($this->paths as $base) {

			$candidatePath = \realpath(static::buildModulePath($base, $dp->getParts()));
			if ($candidatePath !== \false) {

				// Prevent access outside of the import path.
				if (!\str_starts_with($candidatePath, $base)) {
					throw new EngineInternalError("Blocked access outside of import path");
				}

				return $candidatePath;

			}

		}

		return \null;

	}

	abstract protected static function buildModulePath(string $base, array $pathParts): string;
	abstract public function loadModule(string $path, string $name): ModuleValue;

}
