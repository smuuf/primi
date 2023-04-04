<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\StringValue;
use Smuuf\Primi\Parser\GrammarHelpers;
use Smuuf\Primi\Structures\MapContainer;

abstract class Iteration {

	public const FLAG_ITERATOR_END = null;

	public static function getIteratorOfObject(
		AbstractValue $value,
	): \Iterator {

		$iter = $value->getIterator();
		if ($iter === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Object of type '{$value->getTypeName()}' is not iterable",
			);
		}

		return $iter;

	}

	public static function getNextItem(\Iterator $iter): ?AbstractValue {

		$hasAnotherValue = $iter->valid();

		if ($hasAnotherValue) {
			$value = $iter->current();
			$iter->next();
			return $value;
		} else {
			return self::FLAG_ITERATOR_END;
		}

	}

	/**
	 * @param \Iterator<AbstractValue> $iter
	 */
	public static function unpack(\Iterator $iter, int $targetCount): array {

		$buffer = [];
		$counter = 0;
		foreach ($iter as $value) {

			$counter++;
			if ($counter > $targetCount) {
				Exceptions::piggyback(
					StaticExceptionTypes::getRuntimeErrorType(),
					"Too many values to unpack (expected $targetCount)",
				);
			}

			$buffer[] = $value;

		}

		if ($counter < $targetCount) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Not enough values to unpack (expected $targetCount but got $counter)",
			);
		}

		return array_reverse($buffer);

	}

	public static function fromMapToArray(MapContainer $c): array {

		$result = [];
		foreach ($c->getItemsIterator() as [$k, $v]) {
			$result[$k->getStringValue()] = $v;
		}

		return $result;

	}

	/**
	 * @return array<string, AbstractValue>
	 */
	public static function fromMapToVariables(MapContainer $c): array {

		$result = [];
		foreach ($c->getItemsIterator() as [$k, $v]) {

			if (!$k instanceof StringValue) {
				Exceptions::piggyback(
					StaticExceptionTypes::getTypeErrorType(),
					"Cannot use '{$k->getTypeName()}' as variable name",
				);
			}

			$name = $k->getStringValue();

			if (!GrammarHelpers::isValidName($name)) {
				Exceptions::piggyback(
					StaticExceptionTypes::getRuntimeErrorType(),
					"'$name' is not a valid variable name",
				);
			}

			$result[$name] = $v;

		}

		return $result;

	}

	/**
	 * Returns PHP iterable returning couples (2-tuples) of `[key, value]` from
	 * a iterable Primi object that can be interpreted as a mapping.
	 * Best-effort-style.
	 *
	 * @return TypeDef_PrimiObjectCouples
	 */
	public static function fromMappingToCouples(AbstractValue $value) {

		$internalValue = $value->getCoreValue();
		if ($internalValue instanceof MapContainer) {

			// If the internal value already is a mapping represented by
			// MapContainer, just return its items-iterator.
			return $internalValue->getItemsIterator();

		} else {

			// We can also try to extract mapping from Primi iterable objects.
			// If the Primi object provides an iterator, we're going to iterate
			// over its items AND if each of these items is an iterable with
			// two items in it, we can extract mapping from it - and convert
			// it into Primi object couples.

			// First, we try if the passed Primi object supports iteration.
			$items = $value->getIterator();
			if ($items === \null) {
				Exceptions::piggyback(
					StaticExceptionTypes::getTypeErrorType(),
					"Unable to create mapping from non-iterable",
				);
			}

			// We prepare the result container for couples, which will be
			// discarded if we encounter any errors when putting results in it.
			$couples = [];
			$i = -1;

			foreach ($items as $item) {

				$couple = [];
				$i++;
				$j = 0;

				// Second, for each of the item of the top-iterator we check
				// if the item also supports iteration.
				$subitems = $item->getIterator();
				if ($subitems === \null) {
					Exceptions::piggyback(
						StaticExceptionTypes::getTypeErrorType(),
						"Unable to create mapping from iterable: item #$i is not iterable",
					);
				}

				foreach ($subitems as $subitem) {

					$j++;

					// Third, since we want to build and return iterable
					// containing couples, the item needs to contain
					// exactly two sub-items.
					if ($j === 3) {
						Exceptions::piggyback(
							StaticExceptionTypes::getTypeErrorType(),
							"Unable to create mapping from iterable: item #$i contains more than two items ($j)",
						);
					}

					$couple[] = $subitem;

				}

				if ($j < 2) {
					Exceptions::piggyback(
						StaticExceptionTypes::getTypeErrorType(),
						"Unable to create mapping from iterable: item #$i contains less than two items ($j)",
					);
				}

				$couples[] = $couple;

			}

			// All went well, return iterable (list array) with all gathered
			// couples.
			return $couples;

		}

	}


	/**
	 * Convert iterable of couples _(PHP 2-tuple arrays with two items
	 * containing Primi objects, where first item must a string object
	 * representing a valid Primi variable name)_ to PHP dict array mapping
	 * pairs of `['variable_name' => Some Primi object]`.
	 *
	 * @param TypeDef_PrimiObjectCouples $couples
	 * @return array<string, AbstractValue> PHP dict array mapping of variables.
	 */
	public static function fromCouplesToVariables(
		iterable $couples,
		string $intendedTarget,
	): array {

		$attrs = [];
		foreach ($couples as [$k, $v]) {

			if (!$k instanceof StringValue) {
				Exceptions::piggyback(
					StaticExceptionTypes::getTypeErrorType(),
					"$intendedTarget is not a string but '{$k->getTypeName()}'",
				);
			}

			$varName = $k->getStringValue();
			if (!GrammarHelpers::isValidName($varName)) {
				Exceptions::piggyback(
					StaticExceptionTypes::getTypeErrorType(),
					"$intendedTarget '$varName' is not a valid name",
				);
			}

			$attrs[$varName] = $v;

		}

		return $attrs;

	}

}
