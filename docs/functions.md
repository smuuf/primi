## Stdlib\TypeExtensions\BoolTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`and`**(_bool_ **`a`**, _bool_ **`b`**)  → _bool_

Returns result of logical `AND` between two boolean values.

```js
true.and(false) == false
true.and(true) == true
bool.and(true, false) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`not`**(_bool_ **`value`**)  → _bool_

Returns a negation (logical `NOT`) of a single boolean value.

```js
false.not() == true
true.not() == false
bool.not(false) == true
bool.not(true) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`or`**(_bool_ **`a`**, _bool_ **`b`**)  → _bool_

Returns an `OR` of two boolean values.

```js
true.or(true) == true
true.or(false) == true
false.or(true) == true
false.or(false) == false

bool.or(true, true) == true
bool.or(true, false) == true
bool.or(false, true) == true
bool.or(false, false) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`xor`**(_bool_ **`a`**, _bool_ **`b`**)  → _bool_

Returns an exclusive `OR` (`XOR`) of two boolean values.

```js
true.xor(true) == false
true.xor(false) == true
false.xor(true) == true
false.xor(false) == false

bool.xor(true, true) == false
bool.xor(true, false) == true
bool.xor(false, true) == true
bool.xor(false, false) == false
```

---
## Stdlib\TypeExtensions\DictTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`copy`**(_dict_ **`dict`**)  → _dict_

Returns a new shallow copy of this dict.

```js
a_dict = {'a': 1, 100: 'yes'}
b_dict = a_dict.copy()
b_dict[100] = 'nope'

a_dict == {'a': 1, 100: 'yes'}
b_dict == {'a': 1, 100: 'nope'}
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`get`**(_dict_ **`dict`**, ___undefined___ **`key`**, *[___undefined___ **`default`**]*)  → ___undefined___

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
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`has_key`**(_dict_ **`dict`**, ___undefined___ **`key`**)  → _bool_

Returns `true` if the key exists in dict. Return `false` otherwise.

```js
d = {'a': 1, 100: 'yes'}
d.has_key('a') == true
d.has_key(100) == true
d.has_key('100') == false
d.has_key('yes') == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`has_value`**(_dict_ **`dict`**, ___undefined___ **`needle`**)  → _bool_

Returns `true` if the value exists in dict. Return `false` otherwise.

```js
d = {'a': 1, 100: 'yes'}
d.has_value(1) == true
d.has_value('yes') == true
d.has_value(100) == false
d.has_value(false) == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`items`**(_dict_ **`dict`**)  → _list_

Returns a new `list` of `tuples` of **key and value pairs** from this
`dict`.

```js
{'a': 1, 100: 'yes'}.items() == [('a', 1), (100: 'yes')]
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`keys`**(_dict_ **`dict`**)  → _list_

Returns a new `list` containing **keys** from this `dict`.

```js
{'a': 1, 100: 'yes'}.values() == [1, 'yes']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`map`**()  → _dict_

Returns a new dict with same keys but values returned by a passed
function _(callback)_ applied to each item.

Callback arguments: `callback(value, key)`.

```js
a_dict = {'key_a': 'val_a', 'key_b': 'val_b'}
fn = (v, k) => { return k + "|" + v; }
a_dict.map(fn) == {"key_a": "key_a|val_a", "key_b": "key_b|val_b"}
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`values`**(_dict_ **`dict`**)  → _list_

Returns a new `list` containing **values** from this `dict`.

```js
{'a': 1, 100: 'yes'}.values() == [1, 'yes']
```

---
## Stdlib\TypeExtensions\ForbiddenTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
## Stdlib\TypeExtensions\ListTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`contains`**(_list_ **`list`**, ___undefined___ **`needle`**)  → _bool_

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
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`copy`**(_list_ **`list`**)  → _list_

Returns a new copy of the `list`.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`count`**(_list_ **`list`**, ___undefined___ **`needle`**)  → _number_

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
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`get`**(_list_ **`list`**, _number_ **`index`**, *[___undefined___ **`default`**]*)  → ___undefined___

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
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`map`**()  → _list_

Returns a new `list` from results of a passed function _(callback)_
applied to each item.

Callback arguments: `callback(value)`.

```js
[-1, 0, 2].map(to_bool) == [true, false, true]
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`pop`**(_list_ **`list`**, *[_number_ **`index`**]*)  → ___undefined___

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
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`prepend`**(_list_ **`list`**, ___undefined___ **`value`**)  → _null_

Prepend an item to the beginning of the `list`.

```js
a_list = ['a', 'b', 'c']
a_list.prepend({'some_key': 'some_value'})
a_list == [{'some_key': 'some_value'}, 'a', 'b', 'c']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`push`**(_list_ **`list`**, ___undefined___ **`value`**)  → _null_

Add (push) an item to the end of the `list`.

```js
a_list = ['a', 'b', 'c']
a_list.push({'some_key': 'some_value'})
a_list == ['a', 'b', 'c', {'some_key': 'some_value'}]
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`random`**(_list_ **`list`**)  → ___undefined___

Returns a random item from the `list`.

```js
[1, 2, 3].random() // Either 1, 2, or 3.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`reverse`**(_list_ **`list`**)  → _list_

Returns a new `list` with values of the original `list` reversed.

```js
[1, 2, 3].reverse() == [3, 2, 1]
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`shuffle`**(_list_ **`list`**)  → _list_

Returns a new `list` with shuffled items.

```js
[1, 2].shuffle() // Either [1, 2] or [2, 1]
```

---
## Stdlib\TypeExtensions\NullTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
## Stdlib\TypeExtensions\NumberTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`is_divisible_by`**(_number_ **`a`**, _number_ **`b`**)  → _bool_

Return `true` if first argument is divisible by the second argument.

---
## Stdlib\TypeExtensions\ObjectTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
## Stdlib\TypeExtensions\RegexTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`find`**(_regex_ **`regex`**, _string_ **`haystack`**)  → _string|bool_

Regular expression find. Returns the first occurence of matching string.
Otherwise returns `false`.

```js
rx"[xyz]+".find("abbcxxyzzdeef") == "xxyzz"
```

---
## Stdlib\TypeExtensions\StringTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`contains`**(_string_ **`haystack`**, ___undefined___ **`needle`**)  → _bool_

Returns `true` if the `string` contains `needle`. Returns `false`
otherwise.

```js
"this is a sentence".contains("sen") == true
"this is a sentence".contains("yay") == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`ends_with`**(_string_ **`haystack`**, _string_ **`needle`**)  → _bool_

Returns `true` if the string ends with specified string suffix.
Returns `false` otherwise.

```js
"this is a sentence".ends_with("tence") == true
"this is a sentence".ends_with("e") == true
"this is a sentence".ends_with("x") == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

_Missing description._

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`find_first`**(_string_ **`haystack`**, ___undefined___ **`needle`**)  → ___undefined___

Returns the position _(index)_ of **first** occurrence of `needle` in
the `string`. If the `needle` was not found, `null` is returned.

```js
"this is a sentence".find_first("s") == 3
"this is a sentence".find_first("t") == 0
"this is a sentence".find_first("x") == null
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`find_last`**(_string_ **`haystack`**, ___undefined___ **`needle`**)  → ___undefined___

Returns the position _(index)_ of **last** occurrence of `needle` in
the `string`. If the `needle` was not found, `null` is returned.

```js
"this is a sentence".find_first("s") == 3
"this is a sentence".find_first("t") == 0
"this is a sentence".find_first("x") == null
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`format`**(_string_ **`str`**, *[___undefined___ **`items`**]*)  → _string_

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
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`join`**(_string_ **`string`**, ___undefined___ **`iterable`**)  → _string_

Join items from `iterable` with this `string` and return the result as
a new string.

```js
','.join(['a', 'b', 3]) == "a,b,3"
':::'.join({'a': 1, 'b': 2, 'c': '3'}) == "1:::2:::3"
'-PADDING-'.join("abc") == "a-PADDING-b-PADDING-c" // String is also iterable.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`number_of`**(_string_ **`haystack`**, ___undefined___ **`needle`**)  → _number_

Returns `number` of occurrences of `needle` in a string.

```js
"this is a sentence".number_of("s") == 3
"this is a sentence".number_of("x") == 0
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`replace`**(_string_ **`string`**, ___undefined___ **`search`**, _string_ **`replace`**)  → _string_

Perform search and replace and return the results as new `string`.

```js
"abcdef".replace("c", "X") == "abXdef"
"přítmí ve městě za dvě stě".replace("stě", "šci") == "přítmí ve měšci za dvě šci"
"přítmí ve městě za dvě stě".replace(rx"\wt\w", "lol") == "přlolí ve mělol za dvě lol"
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`reverse`**(_string_ **`string`**)  → _string_

Return reversed string.

```js
"hello! tady čaj".reverse() == "jač ydat !olleh"
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`shuffle`**(_string_ **`str`**)  → _string_

Returns a new `string` from shuffled characters of the original `string`.

```js
"hello".shuffle() // "leohl" or something similar.
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`split`**()  → _list_

Split original `string` by some `delimiter` and return result the as a
`list`. If the `delimiter` is not specified, the `string` is splat by
whitespace characters.

```js
"a b c\nd e f".split() == ['a', 'b', 'c', 'd', 'e', 'f']
"a,b,c,d".split(',') == ['a', 'b', 'c', 'd']
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`starts_with`**(_string_ **`haystack`**, _string_ **`needle`**)  → _bool_

Returns `true` if the string starts with specified string.
Returns `false` otherwise.

```js
"this is a sentence".starts_with("this") == true
"this is a sentence".starts_with("t") == true
"this is a sentence".starts_with("x") == false
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`substring`**(_string_ **`string`**, _number_ **`offset`**, *[_number_ **`length`**]*)  → _string_

Returns a substring taken from `string` starting at `offset` with length `length`.
Access outside of bounds will not result in errors.
Length can be omitted to reach up to the `string` end.
Negative offset can be used.

```js
"this is a sentence".substring(5, 4) == "is a"
"this is a sentence".substring(5) == "is a sentence"
"this is a sentence".substring(10, 100) == "sentence"
"this is a sentence".substring(-8) == "sentence"
"this is a sentence".substring(20, 15) == ""
```

---
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`translate`**(_string_ **`string`**, _dict_ **`pairs`**)  → _string_

Search and replace strings within a string and return the new resulting
string. The from-to pairs are to be provided as a `dict`.

```js
"abcdef".replace({'c': 'X', 'e': 'Y'}) == "abXdYf"
"abcdef".replace({'b': 'X', 'ab': 'Y'}) == "Ycdef"
```

The longest keys will be tried first. Once a substring has been replaced,
its new value will not be searched again. This behavior is identical
to PHP function [`strtr()`](https://www.php.net/manual/en/function.strtr.php).

---
## Stdlib\TypeExtensions\TupleTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
## Stdlib\TypeExtensions\TypeTypeExtension
### <i style='color: DodgerBlue; font-size: 90%'>fn</i> **`execute`**() 

 array<string, AbstractValue> Dict array containing Primi
function/method object that represent type/class methods.

---
