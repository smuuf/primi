# Primi internals and PHP API

## Basic types
_Basic types_ in Primi are those types that have objects _(instances)_ implemented directly via their own PHP class (e.g. `StringValue` PHP class).

### List of basic types
- `null` represented by `\Smuuf\Primi\Values\NullValue`.
- `bool` represented by `\Smuuf\Primi\Values\BoolValue`.
- `number` represented by `\Smuuf\Primi\Values\NumberValue`.
- `string` represented by `\Smuuf\Primi\Values\StringValue`.
- `list` represented by `\Smuuf\Primi\Values\ListValue`.
- `dict` represented by `\Smuuf\Primi\Values\DictValue`.
- `tuple` represented by `\Smuuf\Primi\Values\TupleValue`.
- `type` represented by `\Smuuf\Primi\Values\TypeValue`.
- `regex` represented by `\Smuuf\Primi\Values\RegexValue`.
- `module` represented by `\Smuuf\Primi\Values\ModuleValue`.
- `func` represented by `\Smuuf\Primi\Values\FuncValue`.
- `iteratorfactory` represented by `\Smuuf\Primi\Values\IteratorFactoryValue`.

## Other terminology
- `Couple`: A tuple with two items _(also `2-tuple`)_. Inside Primi internals this usually describes a PHP array with two items, without explicitly specified indices.
  - For example this is a `couple`:
    ```php
    ["value A", "value B"]
    ```
- `Pair`: A mapping/key-value pair.
  - For example a generator would yield a key-value pair:
    ```php
    yield $key => $value;
    ```
  - Or some PHP array can represent key-value pairs:
    ```php
    [
      $keyA => $valueA, // This is pair.
      $keyB => $valueB, // This is another pair.
      ...
    ];
    ```
