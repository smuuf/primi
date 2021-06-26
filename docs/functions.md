## BoolExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`bool_and`**(_bool_ **`a`**, _bool_ **`b`**)  → _bool_

Returns result of logical `AND` between two boolean values.

```js
true.and(false) == false
true.and(true) == true
bool_and(true, false) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`bool_not`**(_bool_ **`value`**)  → _bool_

Returns a negation (logical `NOT`) of a single boolean value.

```js
false.not() == true
bool_not(true) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`bool_or`**(_bool_ **`a`**, _bool_ **`b`**)  → _bool_

Returns an `OR` of two boolean values.

```js
true.or(true) == true
true.or(false) == true
false.or(true) == true
false.or(false) == false

bool_or(true, true) == true
bool_or(true, false) == true
bool_or(false, true) == true
bool_or(false, false) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`bool_xor`**(_bool_ **`a`**, _bool_ **`b`**)  → _bool_

Returns an exclusive `OR` (`XOR`) of two boolean values.

```js
true.xor(true) == false
true.xor(false) == true
false.xor(true) == true
false.xor(false) == false

bool_xor(true, true) == false
bool_xor(true, false) == true
bool_xor(false, true) == true
bool_xor(false, false) == false
```

---
## CastingExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`to_bool`**(_any_ **`value`**)  → _bool_

Returns a new `bool` value based on argument's truthness.

```js
to_bool(true) == true
to_bool(false) == false
to_bool(-1) == true
to_bool(0) == false
to_bool([]) == false
to_bool(['']) == true
to_bool('') == false
to_bool(' ') == true
to_bool('0') == true
to_bool('1') == true
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`to_dict`**(_any_ **`value`**)  → _dict_

Returns a new dict containing items of some iterable value.

```js
a_list = 'máj'.to_dict()
a_list == {0: 'm', 1: 'á', 2: 'j'}

b_list = {'a': 1, 'b': 2, 'c': []}.to_list()
b_list = [1, 2, []]

c_list = {'a': 1, 'b': 2, 'c': []}.keys().to_list()
c_list = ['a', 'b', 'c']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`to_list`**(_any_ **`value`**)  → _list_

Returns a new `list` containing items of some iterable value.

```js
a_list = 'první máj'.to_list()
a_list == ["p", "r", "v", "n", "í", " ", "m", "á", "j"]

b_list = {'a': 1, 'b': 2, 'c': []}.to_list()
b_list = [1, 2, []]

c_list = {'a': 1, 'b': 2, 'c': []}.keys().to_list()
c_list = ['a', 'b', 'c']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`to_number`**(_any_ **`value`**)  → _number_

Cast a `number|string|bool` value to `number`.

```js
to_number(1) == 1
to_number('123') == 123
to_number('+123') == 123
to_number('-123') == -123
to_number(' +123.001   ') == 123.001
to_number(' -123.00   ') == -123.0

to_number(true) == 1
to_number(false) == 0
to_number(fal) == 0
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`to_regex`**(_any_ **`value`**, *[_bool_ **`escape`**]*)  → _regex_

Convert `string` to a `regex` value. If the optional `escape` argument
is `true`, the any characters with special regex meaning will be
escaped so that they are meant literally.

```js
"hello".to_regex() == rx"hello"
to_regex("Why so serious...?", true) == rx"Why so serious\.\.\.\?"
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`to_string`**(_any_ **`value`**)  → _string_

Return a `string` representation of value.

```js
to_string(true) == 'true'
to_string([]) == '[]'
to_string(3.14) == '3.14'
{'a': 1, 'b': 'c'}.to_string() == '{"a": 1, "b": "c"}'
"hello there!".to_string() == "hello there!"
to_string(to_string) == "<function: native>"
```

---
## CliExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`debugger`**()  → _any_

_**Only in [CLI](https://w.wiki/QPE)**_.

Injects a [REPL](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop)
session for debugging at the specified line.




---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`get_traceback`**()  → _list_

_**Only in [CLI](https://w.wiki/QPE)**_.

Return traceback as a list.




---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`memory_get_peak_usage`**()  → _number_

_**Only in [CLI](https://w.wiki/QPE)**_.

Returns memory peak usage used by Primi _(engine behind the scenes)_ in
bytes.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`memory_get_usage`**()  → _number_

_**Only in [CLI](https://w.wiki/QPE)**_.

Returns current memory usage used by Primi _(engine behind the scenes)_
in bytes.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`print`**(*[_any_ **`value`**]*, *[_bool_ **`nl`**]*)  → _null_

_**Only in [CLI](https://w.wiki/QPE)**_.

Prints value to standard output.

---
## DatetimeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`time_monotonic`**()  → _number_

Returns high-resolution monotonic time. It is an arbitrary number that
keeps increasing by 1 every second.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`time_unix`**()  → _number_

Returns high-resolution UNIX time.

---
## DictExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_copy`**(_dict_ **`dict`**)  → _dict_

Returns a new shallow copy of this dict.

```js
a_dict = {'a': 1, 100: 'yes'}
b_dict = a_dict.copy()
b_dict[100] = 'nope'

a_dict == {'a': 1, 100: 'yes'}
b_dict == {'a': 1, 100: 'nope'}
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_get`**(_dict_ **`dict`**, _any_ **`key`**, *[_any_ **`default`**]*)  → _any_

Returns value stored under `key`, if it in dict, otherwise returns the
value of the `default` argument, which is `null` by default, but can
optionally be specified.

```js
d = {'a': 1, 100: 'yes'}
d.get('a') == 1
d.get(100) == 'yes'
d.get('100') == null
d.get('100', ['one', 'hundred']) == ['one', 'hundred']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_has_key`**(_dict_ **`dict`**, _any_ **`key`**)  → _bool_

Returns `true` if the key exists in dict. Return `false` otherwise.

```js
d = {'a': 1, 100: 'yes'}
d.has_key('a') == true
d.has_key(100) == true
d.has_key('100') == false
d.has_key('yes') == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_has_value`**(_dict_ **`dict`**, _any_ **`needle`**)  → _bool_

Returns `true` if the value exists in dict. Return `false` otherwise.

```js
d = {'a': 1, 100: 'yes'}
d.has_value(1) == true
d.has_value('yes') == true
d.has_value(100) == false
d.has_value(false) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_keys`**(_dict_ **`dict`**)  → _list_

Returns a new `list` containing **keys** from this `dict`.

```js
{'a': 1, 100: 'yes'}.values() == [1, 'yes']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_map`**(_dict_ **`dict`**, _function_ **`fn`**)  → _dict_

Returns a new dict with same keys but values returned by a passed
function _(callback)_ applied to each item.

Callback arguments: `callback(value, key)`.

```js
a_dict = {'key_a': 'val_a', 'key_b': 'val_b'}
fn = (v, k) => { return k + "|" + v; }
a_dict.map(fn) == {"key_a": "key_a|val_a", "key_b": "key_b|val_b"}
```



---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_reverse`**(_dict_ **`dict`**)  → _any_

Returns a new `dict` with original `dict`'s items in reversed order.

```js
{'a': 1, 100: 'yes'}.reverse() == {100: 'yes', 'a': 1}
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`dict_values`**(_dict_ **`dict`**)  → _list_

Returns a new `list` containing **values** from this `dict`.

```js
{'a': 1, 100: 'yes'}.values() == [1, 'yes']
```

---
## HashExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`hash_md5`**(_string_ **`val`**)  → _string_

Return [`md5` hash](https://en.wikipedia.org/wiki/MD5) representation
of a `string` value as `string`.

```js
hash_md5('hello') == '5d41402abc4b2a76b9719d911017c592'
hash_md5('123') == '202cb962ac59075b964b07152d234b70'
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`hash_sha256`**(_string_ **`val`**)  → _string_

Return [`sha256` hash](https://en.wikipedia.org/wiki/SHA-2) representation
of a `string` value as `string`.

```js
hash_sha256('hello') == '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824'
hash_sha256('123') == 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'
```

---
## ListExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_contains`**(_list_ **`list`**, _any_ **`needle`**)  → _bool_

Returns `true` if the `needle` is present in the `list` at least once.

```js
[1, 2, 3, 1].contains(1) == true
[1, 2, 3, 1].contains(666) == false

// NOTE: Lists with same items with different order are different.
[[1, 2], 'xxx'].contains([1, 2]) == true
[[1, 2], 'xxx'].contains([2, 1]) == false

// NOTE: Dicts with same items with different order are the same.
[{'b': 2, 'a': 1}, 'xxx'].contains({'a': 1, 'b': 2}) == true
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_copy`**(_list_ **`list`**)  → _list_

Returns a new copy of the `list`.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_count`**(_list_ **`list`**, _any_ **`needle`**)  → _number_

Returns number of occurrences of some value in the `list`.

```js
[1, 2, 3, 1].count(1) == 2
[1, 2, 3, 1].count(2) == 1
[1, 2, 3, 1].count(666) == 0

// NOTE: Lists with same items with different order are different.
[[1, 2], [2, 1]].count([1, 2]) == 1

// NOTE: Dicts with same items with different order are the same.
[{'a': 1, 'b': 2}, {'b': 2, 'a': 1}].count({'a': 1, 'b': 2}) == 2
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_get`**(_list_ **`list`**, _number_ **`index`**, *[_any_ **`default`**]*)  → _any_

Returns an item from `list` by its index _(starting at 0)_. Negative
indexes can be used to get items from the end.

A default is returned in case the index is not found. This default
value can be optionally specified via the `default` parameter _(`null`
by default)_

```js
['a', 'b', 'c'].get(0) == 'a'
['a', 'b', 'c'].get(1) == 'b'
['a', 'b', 'c'].get(2) == 'c'
['a', 'b', 'c'].get(3) == null
['a', 'b', 'c'].get(3, 'NOT FOUND') == 'NOT FOUND'

// Using negative index.
['a', 'b', 'c'].get(-1) == 'c'
['a', 'b', 'c'].get(-2) == 'b'
['a', 'b', 'c'].get(-3) == 'a'
['a', 'b', 'c'].get(-4) == null
['a', 'b', 'c'].get(-4, 'NOT FOUND') == 'NOT FOUND'
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_map`**(_list_ **`list`**, _function_ **`fn`**)  → _list_

Returns a new `list` from results of a passed function _(callback)_
applied to each item.

Callback arguments: `callback(value)`.

```js
[-1, 0, 2].map(to_bool) == [true, false, true]
```



---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_pop`**(_list_ **`list`**, *[_number_ **`index`**]*)  → _any_

Remove (pop) item at specified `index` from the `list` and return it.

If the `index` is not specified, last item in the `list` will be
removed.  Negative index can be used.

```js
a_list = [1, 2, 3, 4, 5]

a_list.pop() == 5 // a_list == [1, 2, 3, 4], 5 is returned
a_list.pop(1) == 2 // a_list == [1, 3, 4], 2 is returned.
a_list.pop(-3) == 1 // a_list == [3, 4], 1 is returned
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_prepend`**(_list_ **`list`**, _any_ **`value`**)  → _null_

Prepend an item to the beginning of the `list`.

```js
a_list = ['a', 'b', 'c']
a_list.prepend({'some_key': 'some_value'})
a_list == [{'some_key': 'some_value'}, 'a', 'b', 'c']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_push`**(_list_ **`list`**, _any_ **`value`**)  → _null_

Add (push) an item to the end of the `list`.

```js
a_list = ['a', 'b', 'c']
a_list.push({'some_key': 'some_value'})
a_list == ['a', 'b', 'c', {'some_key': 'some_value'}]
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_random`**(_list_ **`list`**)  → _any_

Returns a random item from the `list`.

```js
[1, 2, 3].random() // Either 1, 2, or 3.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_reverse`**(_list_ **`list`**)  → _list_

Returns a new `list` with values of the original `list` reversed.

```js
[1, 2, 3].reverse() == [3, 2, 1]
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`list_shuffle`**(_list_ **`list`**)  → _list_

Returns a new `list` with shuffled items.

```js
[1, 2].shuffle() // Either [1, 2] or [2, 1]
```

---
## NumberExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_abs`**(_number_ **`n`**)  → _number_

Returns the absolute value of number `n`.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_atan`**(_number_ **`n`**)  → _number_

Returns the arc tangent of number `n` specified in radians.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_ceil`**(_number_ **`n`**)  → _number_

Returns number `n` rounded up.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_cos`**(_number_ **`n`**)  → _number_

Returns the cosine of number `n` specified in radians.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_divisible_by`**(_number_ **`a`**, _number_ **`b`**)  → _bool_

Return `true` if first argument is divisible by the second argument.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_floor`**(_number_ **`n`**)  → _number_

Returns number `n` rounded down.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_mod`**(_number_ **`a`**, _number_ **`b`**)  → _number_

Returns the remainder (modulo) of the division of the arguments.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_pow`**(_number_ **`n`**, *[_number_ **`power`**]*)  → _number_

Returns number `n` squared to the power of `power`.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_round`**(_number_ **`n`**, *[_number_ **`precision`**]*)  → _number_

Returns number `n` rounded to specified `precision`. If the
precision is not specified, a default `precision` of zero is used.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_sin`**(_number_ **`n`**)  → _number_

Returns the sine of number `n` specified in radians.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_sqrt`**(_number_ **`n`**)  → _number_

Returns the square root of a number `n`.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_tan`**(_number_ **`n`**)  → _number_

Returns the tangent of number `n` specified in radians.

---
## RegexExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`regex_match`**(_regex_ **`regex`**, _string_ **`haystack`**)  → _any_

Regular expression match. Returns the first matching string. Otherwise
returns `false`.

```js
rx"[xyz]+".match("abbcxxyzzdeef") == "xxyzz"
```

---
## StandardExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`assert`**(_bool_ **`assumption`**, *[_string_ **`description`**]*)  → _bool_

This function returns `true` if a `bool` value passed into it is `true`
and throws error if it's `false`. Optional `string` description can be
provided, which will be visible in the eventual error message.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`len`**(_any_ **`value`**)  → _number_

Returns length of a value.

```js
"hello, Česká Třebová".len() == 20
len(123456) == 6
[1, 2, 3].len() == 3
len({'a': 1, 'b': 'c'}) == 2
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`range`**(_number_ **`start`**, *[_number_ **`end`**]*, *[_number_ **`step`**]*)  → _list_

Return a list containing numbers from `start` to `end` _(including)_.
If the `end` is not specified, numbers from `0` to the value of the
first argument `start` is returned. Optional third argument `step` can
be specified for steps other than `1`.

```js
range(0) == [0]
range(1) == [0, 1]
range(5) == [0, 1, 2, 3, 4, 5]
range(2, 7) == [2, 3, 4, 5, 6, 7]
range(2, -7) == [2, 1, 0, -1, -2, -3, -4, -5, -6, -7]
range(2, -7, 3) == [2, -1, -4, -7]
range(5, 10, 3) == [5, 8] // The last number that could be obtained by incrementing 3 is 8.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`type`**(_any_ **`value`**)  → _string_

Return type of value as string.

```js
type(true) == 'bool'
type("hello") == 'string'
type(type) == 'function'
```

---
## StringExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_contains`**(_string_ **`haystack`**, _any_ **`needle`**)  → _bool_

Returns `true` if the `string` contains `needle`. Returns `false`
otherwise.

```js
"this is a sentence".contains("sen") == true
"this is a sentence".contains("yay") == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_find_first`**(_string_ **`haystack`**, _any_ **`needle`**)  → _any_

Returns the position _(index)_ of **first** occurrence of `needle` in
the `string`. If the `needle` was not found, `null` is returned.

```js
"this is a sentence".find_first("s") == 3
"this is a sentence".find_first("t") == 0
"this is a sentence".find_first("x") == null
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_find_last`**(_string_ **`haystack`**, _any_ **`needle`**)  → _any_

Returns the position _(index)_ of **last** occurrence of `needle` in
the `string`. If the `needle` was not found, `null` is returned.

```js
"this is a sentence".find_first("s") == 3
"this is a sentence".find_first("t") == 0
"this is a sentence".find_first("x") == null
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_format`**(_string_ **`str`**, *[_any_ **`items`**]*)  → _string_

Returns a new `string` with placeholders from the original `string`
replaced by additional arguments.

Placeholders can be either _(but these can't be combined)_:
- Non-positional: `{}`
- Positional: `{0}`, `{1}`, `{2}`, etc.

```js
"x{}x, y{}y".format(1, 2) == "x1x, y2y"
"x{1}x, y{0}y".format(111, 222) == "x222x, y111y"
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_join`**(_string_ **`string`**, _any_ **`iterable`**)  → _string_

Join items from `iterable` with this `string` and return the result as
a new string.

```js
','.join(['a', 'b', 3]) == "a,b,3"
':::'.join({'a': 1, 'b': 2, 'c': '3'}) == "1:::2:::3"
'-PADDING-'.join("abc") == "a-PADDING-b-PADDING-c" // String is also iterable.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_number_of`**(_string_ **`haystack`**, _any_ **`needle`**)  → _number_

Returns `number` of occurrences of `needle` in a string.

```js
"this is a sentence".number_of("s") == 3
"this is a sentence".number_of("x") == 0
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_replace`**(_string_ **`string`**, _any_ **`search`**, *[_string_ **`replace`**]*)  → _string_

Perform search and replace and return the results as new `string`.

Two separate modes of operation:
1. The needle `search` is a `string` and haystack `replace` is a string.
2. The needle `search` is a `dict` defining search-and-replace pairs
_(and `replace` argument is omitted)_.

```js
"abcdef".replace("c", "X") == "abXdef"
"abcdef".replace({"c": "X", "e": "Y"}) == "abXdYf"
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_reverse`**(_string_ **`string`**)  → _string_

Return reversed string.

```js
"hello! tady čaj".reverse() == "jač ydat !olleh"
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_shuffle`**(_string_ **`str`**)  → _string_

Returns a new `string` from shuffled characters of the original `string`.

```js
"hello".shuffle() // "leohl" or something similar.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`string_split`**(_string_ **`string`**, *[_any_ **`delimiter`**]*)  → _list_

Split original `string` by some `delimiter` and return result the as a
`list`. If the `delimiter` is not specified, the `string` is splat by
whitespace characters.

```js
"a b c\nd e f".split() == ['a', 'b', 'c', 'd', 'e', 'f']
"a,b,c,d".split(',') == ['a', 'b', 'c', 'd']
```

---
