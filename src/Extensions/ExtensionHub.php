<?php

declare(strict_types=1);

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Scopes\AbstractScope;
use \Smuuf\Primi\Scopes\ExtensionScope;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;
use \Smuuf\Primi\Structures\FnContainer;

class ExtensionHub {

	use StrictObject;

	/**
	 * Essential extensions required for Primi runtime.
	 * @const string[]
	 */
	const ESSENTIAL_EXTENSIONS = [
		\Smuuf\Primi\Stdlib\StandardExtension::class,
		\Smuuf\Primi\Stdlib\StringExtension::class,
		\Smuuf\Primi\Stdlib\NumberExtension::class,
		\Smuuf\Primi\Stdlib\DictExtension::class,
		\Smuuf\Primi\Stdlib\ListExtension::class,
		\Smuuf\Primi\Stdlib\RegexExtension::class,
		\Smuuf\Primi\Stdlib\CliExtension::class,
		\Smuuf\Primi\Stdlib\CastingExtension::class,
	];

	/**
	 * Non-essential extensions loaded by default for Primi runtime.
	 * @const string[]
	 */
	const DEFAULT_EXTENSIONS = [
		\Smuuf\Primi\Stdlib\HashExtension::class,
		\Smuuf\Primi\Stdlib\DatetimeExtension::class,
		\PHP_SAPI === 'cli' // CliExtension only in CLI mode.
			? \Smuuf\Primi\Stdlib\CliExtension::class
			: \false,
	];

	/** @var (string|Extension)[] Extensions that are to be applied to runtime scope. */
	private $extensions = [];

	/**
	 * Is `true` if extension hub was already applied to a scope and is now
	 * locked. If so, new extensions cannot be added.
	 *
	 * @var bool
	 */
	private $isLocked = \false;

	public function __construct(
		array $extensions = [],
		bool $skipDefault = \false
	) {

		// Load essential extensions.
		$this->add(self::ESSENTIAL_EXTENSIONS);

		// Load default extensions.
		if (!$skipDefault) {
			$this->add(self::DEFAULT_EXTENSIONS);
		}

		// Load additional extensions.
		$this->add($extensions);

	}

	/**
	 * Register a PHP class as an extension to a target Primi  <...>AbstractValue class.
	 * Optionally pass an array of <PHP class> => <AbstractValue class> pairs to
	 * register multiple extensions at once.
	 *
	 * @param string|array<string>|Extension|array<Extension> $extension A list
	 * of extensions or a single extension - as a class name or as an instance
	 * of `Extension` class.
	 */
	public function add($extension): void {

		if ($this->isLocked) {
			throw new EngineError(
				"This ExtensionHub instance was already applied and is now locked"
			);
		}

		// We allow registering extensions in bulk.
		if (\is_array($extension)) {

			// Skip falsey values (friendlier for adding conditional
			// extensions).
			foreach (\array_filter($extension) as $ext) {
				$this->add($ext);
			}

			return;

		}

		// Can be either class name or instance, so we can't use `instanceof`.
		if (!\is_subclass_of($extension, Extension::class)) {
			throw new EngineError("'$extension' is not a valid Primi extension");
		}

		$this->extensions[$extension] = $extension;

	}

	/**
	 * Create new `ExtensionScope` filled with items provided by the extension
	 * hub and add it as the top parent for the scope passed as argument.
	 *
	 * If the top parent already is some `ExtensionScope`, do not do anything.
	 */
	public function apply(AbstractScope $scope): void {

		// Detect if the scope already has ExtensionScope as one of its parents.
		// If so, do not apply anything.
		$topScope = self::getTopScope($scope);
		if ($topScope instanceof ExtensionScope) {
			return;
		}

		// Lock this extension hub to avoid adding new extensions - this
		// ensures consistency (extensions added to the hub later wouldn't be
		// available).
		$this->isLocked = \true;
		$extScope = new ExtensionScope;

		foreach ($this->extensions as $ext) {

			// Extensions can be class names or instances.
			$instance = \is_string($ext)
				? new $ext
				: $ext;

			$extScope->setVariables($this->processExtension($instance));

		}

		$topScope->setParent($extScope);

	}

	/**
	 * Process an extension class - iterate over all public methods and
	 * make FuncValues from them.
	 */
	private function processExtension(Extension $ext): array {

		$extRef = new \ReflectionClass($ext);
		$methods = $extRef->getMethods(\ReflectionMethod::IS_PUBLIC);

		$result = [];
		foreach ($methods as $methodRef) {

			$methodName = $methodRef->getName();

			// Skip magic methods.
			if (\strpos($methodName, '__') === 0) {
				continue;
			}

			$callable = [$ext, $methodName];
			$value = new FuncValue(FnContainer::buildFromClosure($callable));
			$result[$methodName] = $value;

		}

		return $result;

	}

	private static function getTopScope(AbstractScope $scope): AbstractScope {

		$current = $scope;
		while ($next = $current->getParent()) {
			$current = $next;
		}

		return $current;

	}

}
