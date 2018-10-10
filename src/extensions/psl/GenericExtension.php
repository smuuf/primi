<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;

class GenericExtension extends Extension {

	public static function length(Value $self): NumberValue {

		switch (true) {
			case $self instanceof StringValue:
				return StringExtension::string_length($self);
			case $self instanceof ArrayValue:
				return ArrayExtension::array_length($self);
			case $self instanceof NumberValue:
				return NumberExtension::number_length($self);
		}

		throw new \TypeError;

	}

	public static function reverse(Value $self): Value {

		switch (true) {
			case $self instanceof StringValue:
				return StringExtension::string_reverse($self);
			case $self instanceof ArrayValue:
				return ArrayExtension::array_reverse($self);
		}

		throw new \TypeError;

	}

	public static function number_of(Value $self, ...$args): NumberValue {

		switch (true) {
			case $self instanceof StringValue:
				return StringExtension::string_number_of($self, ...$args);
			case $self instanceof ArrayValue:
				return ArrayExtension::array_number_of($self, ...$args);
		}

		throw new \TypeError;

	}

	public static function contains(Value $self, ...$args): BoolValue {

		switch (true) {
			case $self instanceof StringValue:
				$tmp = StringExtension::string_number_of($self, ...$args);
				return new BoolValue((bool) $tmp->value);
			case $self instanceof ArrayValue:
				$tmp = ArrayExtension::array_number_of($self, ...$args);
				return new BoolValue((bool) $tmp->value);
		}

		throw new \TypeError;

	}

}
