<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Structures\FuncValue;

class ExtensionHub extends \Smuuf\Primi\StrictObject {

	/**
	 * Essential extensions required for Primi runtime.
	 * @const string[]
	 */
	const ESSENTIAL_EXTENSIONS = [
		\Smuuf\Primi\Psl\StandardExtension::class,
		\Smuuf\Primi\Psl\StringExtension::class,
		\Smuuf\Primi\Psl\NumberExtension::class,
		\Smuuf\Primi\Psl\DictExtension::class,
		\Smuuf\Primi\Psl\ListExtension::class,
		\Smuuf\Primi\Psl\RegexExtension::class,
		\Smuuf\Primi\Psl\BoolExtension::class,
		\Smuuf\Primi\Psl\CastingExtension::class,
	];

	/**
	 * Non-essential extensions loaded by default for Primi runtime.
	 * @const string[]
	 */
	const DEFAULT_EXTENSIONS = [
		\Smuuf\Primi\Psl\HashExtension::class,
		\Smuuf\Primi\Psl\DatetimeExtension::class,
		PHP_SAPI === 'cli' // CliExtension only in CLI mode.
			? \Smuuf\Primi\Psl\CliExtension::class
			: false,
	];

	/** @var (string|Extension)[] Extensions that are to be applied to runtime scope. */
	protected $extensions = [];

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
	 * Register a PHP class as an extension to a target Primi  <...>Value class.
	 * Optionally pass an array of <PHP class> => <Value class> pairs to
	 * register multiple extensions at once.
	 */
	public function add($extClass): void {

		if ($this->isLocked) {
			throw new EngineError("Extension hub was already applied and is now locked");
		}

		// We allow registering extensions in bulk.
		if (\is_array($extClass)) {
			// Skip falsey values (easier for adding conditional extensions).
			foreach (array_filter($extClass) as $ext) {
				$this->add($ext);
			}
			return;
		}

		if (!is_subclass_of($extClass, Extension::class)) {
			throw new EngineError("'$extClass' is not a valid Primi extension");
		}

		$this->extensions[$extClass] = $extClass;

	}

	/**
	 * Return array of values provided by all registered extensions.
	 */
	public function apply(AbstractScope $scope): void {

		// Lock this extension hub to avoid adding new extensions - this
		// ensures consistency (extensions added to the hub later wouldn't be
		// available in the scope).
		$this->isLocked = \true;
		$extScope = new Scope;

		foreach ($this->extensions as $ext) {

			// Extensions can be class names or instances.
			$instance = \is_string($ext)
				? new $ext
				: $ext;

			$extScope->setVariables($this->processExtension($instance));

		}

		$scope->setParent($extScope);

	}

	/**
	 * Process an extension class - iterate over all public methods and
	 * register their return value as target value object's property.
	 * We'll use the method's name as the target property name.
	 *
	 * For example, if you register "SomeExtensionClass::class" as an extension
	 * to the Primi's "StringValue::class", then if, for example, you write a
	 * "SomeExtensionClass::plsGimmeOne()" method which returns a new
	 * NumberValue(1), that Number value will be available under the
	 * <code>'hello there!'.plsGimmeOne; </code> property.
	 *
	 * Then, if you use <code>'hello there!'.plsGimmeOne</code>, in
	 * some Primi source code, it will return a number one.
	 */
	protected function processExtension(Extension $ext): array {

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

}
