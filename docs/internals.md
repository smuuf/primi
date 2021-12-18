# Primi internals and PHP API

## Terminology
- `Couple`: A tuple with two items _(also `2-tuple`)_. Inside Primi internals this usually describes a PHP array with two items, without explicitly specified indices.
  - For example this is a `couple`:
    ```php
    ["value A", "value B"]
    ```
- `Pair`: A mapping key-value pair. Inside Primi internals this usually describes what ``
  - For example this code yields a key-value pair:
    ```php
    yield $key => $value;
    ```
	or
	```php
	[
		$keyA => $valueA, // This is pair.
		$keyB => $valueB, // This is another pair.
	];
	```
