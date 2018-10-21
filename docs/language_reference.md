# Language reference

## Syntax
Primi is built with a familiar PHP/JS/C-like syntax. **Statements are separated using the `;` semicolon character**. If there's no need to put multiple statements on a single line, statements can be separated with newline, too. Both of these syntaxes are therefore valid:

- Using semicolons *(recommended)*:
    ```
    a = 1 + 2; b = a + 3;
    c = b - a;
    ```

- Using newlines:
    ```
    a = 1 + 2
    b = a + 3
    c = b - a
    ```

## Data types
Primi has 6 data types:
- Bool
- Number
- String
- Regex
- Array
- Function

### Bool
This basic type represents a primitive boolean "truth" value. Possible values are `true` and `false`.

Example usage:
```
a = true;
b = 1 == 2; // false
c = b == false; // true
d = c == "hello"; // ERR: Cannot compare 'bool' and 'string'
e = "hello" == r"[0-9]"; // false
f = "hello" == r"l{2}"; // true
```

### Number
The general `number` data type represents both integer and float values, whichever is needed. The maximum/minimum integer or float value/precision is determined by PHP version.

Example usage:
```
a = 4 - 3; // 1
b = a / 3; // 0.333...
```

### String
This data types represents a series of characters. Multi-byte characters (accents, diacritics) are treated properly.

Example usage:
```
a = "hello";
b = "wóóórld!";
c = a + " " + b; // "hello wóóórld!"
```

### Regex
Regex data type exists for advanced string matching. It is defined using the `r"..."` literal *(a string literal prefixed with `r`)* and treated as PCRE (Perl-compatible) regular expressions *(the same as within PHP itself)* with Unicode mode enabled.

Example usage:
```
a = "facebook";
b = r"[o]{2}.*";
c = a == b; // true
```

### Array
Arrays are untyped *(PHP-style)* containers that can accomodate multiple values of different *(or same)* types. Optionally, array index can be defined for a value. By default, integer index starting from the lowest index found (or from `0`) is used.

Example usage:
```
a = ["abc", 123, 4: true, false, r"[A-Z]+"];
// Resulting array: [0: "abc", 1: 123, 4: true, 5: false, 6: r"[A-Z]+"]
```

### Functions
Function is a value type that represents *a "unit" of some self-contained logic*. In Primi they have their own type and are treated as first-class citizens: they can be **stored inside variables** and **passed around** as such. Direct invocation of an anonymous function is supported, provided that the anonymous function's definition is enclosed in parentheses. A function *does capture* its surrounding variables.

Example usage:
```

// Traditional definition.
function sub(a, b) {
    return a - b;
}

// A variable "sub" that holds the "sub()" function is now defined in current scope.
sub(1, 2); // Returns -1

// Storing a function value into a variable.
// Note: This is equivalent to the previous definition.
sub_2 = function(a, b) {
    return a - b;
};

// A variable "sub_2" that holds the "sub_2()" function is now defined in current scope.
sub_2(1, 2); // Returns -1

// Creating a function with alternative, short syntax.
// Note: This is equivalent all of the previous definitions.
sub_3 = (a, b) => {
    return a - b;
};

// A variable "sub_3" that holds the "sub_3()" function is now defined in current scope.
sub_3(1, 2); // Returns -1

// Creating and using an anonymous function directly.
// Using an alternative, short syntax.
((a, b) => {
    return a - b;
})(1, 2); // Returns -1

```

#### Chained functions
In addition to classical function invocation, Primi additionaly supports [Uniform Function Call Syntax (UFCS)](https://en.wikipedia.org/wiki/Uniform_Function_Call_Syntax) as a way to call functions "on values". Essentially, it means that calling `foo(bar);` is ***equivalent*** to calling `bar.foo()`, or *- to provide an example with additional parameters -* that calling `foo(bar, 1, true, "something");` is ***equivalent*** to calling `bar.foo(1, true, "something")`.

#### Value-type based inference of called function name
When using chained function invocation, Primi **interpreter will try to find the most fitting function to call**. "Most fitting" meaning that when the client calls `bar()` function on a value having the `string` type, Primi will try to find and use the `string_bar()` first. If such function is not defined, only then will the interpreter use the original `bar()` function.

Consider this *a syntactic sugar* to make coding in Primi a bit more user-friendly. Because of this the user is able to call `"something".length()` on a string the same way as calling `[1, 2, 3].length()` on an array, even though there are in fact two separate functions `string_length()` and `array_length()` invoked behind the scenes.

## Operators
Plethora of well known operators can be used to **define relationships** between and/or affect various values. Different operators can have various effects on various data types, some of which are covered down below.

#### Precedence
Precedence of various operators is defined as follows *(from highest to lowest)*:
- `(` ... `)` *(parentheses)*
- `!` *(logical not)*
- `*`, `/` *(multiplication/division)*
- `+`, `-` *(addition/subtraction)*
- `==`, `!=`, `>=`, `<=`, `>`, `<` *(comparison)*
- `and` *(logical and)*
- `or` *(logical not)*
- `=` *(assignment)*

##### Precedence example

| Raw source | Equivalent to |
| --- | --- |
| `1 + 2 + 3 + 4` | `((1 + 2) + 3) + 4` |
| `1 - 2 + 3 - 4` | `((1 - 2) + 3) - 4` |
| `1 + 2 * 3 + 4` | `1 + (2 * 3) + 4` |
| `1 + 2 * 3 / 4` | `1 + ((2 * 3) / 4)` |
| `1 + -2 * 3 / 4` | `1 + (((-2) * 3) / 4)` |
| `1 and 2 or 3 and 4` | `(1 and 2) or (3 and 4)` |
| `1 or 2 or 3 and 4` | `(1 or 2) or (3 and 4)` |
| `1 or !2 or !3 and 4` | `(1 or (!2)) or ((!3) and 4))` |
| `x = true or false and true` | `x = (true or (false and true))` |
| `x = a == b` | `x = (a == b)` |
| `x = a > 5 and b < 6` | `x = ((a > 5) and (b < 6))` |


### Logical operators
- `and`
    - Returns `true` if **both** operands are *truthy*.
    - Otherwise returns `false`.
- `or`
    - Returns `true` if **either one (or both)** operand is *truthy*.
    - Otherwise returns `false`.

### Negation
- `!`
    - Negate the value located after this operator.
    - Examples:
        ```
        a = true;
        b = !a; // false
        c = !b; // true
        d = !!b; // false
        ```
### Assignment
- `=`
    - Assigns some value to a variable.
    - Can also be used to insert values to values that support it (eg. arrays).
    - Examples:
        ```
        a = 1;
        b = "a word";
        c = false;
        d = r"regul[ar]+";
        e = ["x", "b": "z"];
        e["c"] = "x"; // e == ["x", "b": "z", "c": "x"]
        ```

### Addition and multiplication
- `+`, `-`
    - Performs addition (subtraction) of two values.
        - Numbers:
            - `+` Add two numbers.
            - `-` Subtract two numbers.
        - Strings:
            - `+` Concatenate two strings.
            - `-` Removes all occurences of the right side from the left side.
            - `-` **(if the right side is a *Regex* value)** Removes all matches of the regex from the left side string.
    - Examples:
        ```
        a = 5 + 4 // (number) 9
        b = 5 - 4; // (number) 1
        c = "a word and number " + 5.to_string(); // (string) "a word and number 5"
        d = "a word and number {}".format(5); // (string) "a word and number 5"
        e = "a word" + " and one more"; // (string) "a word and one more"
        f = "a word" - "or"; // (string) "a wd"
        g = "regular expressions" - r"regul[ar]+\s*"; // (string) "expressions"
        _x = 5 + "4" // ERR: Cannot use operator '+' with 'number' and 'string'
        _x = "a word and number " + 5; // ERR: Cannot use operator '+' with 'number' and 'string'
        ```
- `*`, `/`
    - Performs multiplication (division) of two values.
        - Numbers:
            - `+` Multiply two numbers.
            - `-` Divide two numbers.
    - Examples:
        ```
        a = 1 * -2; // (number) -2
        b = 2 * 3; // (number) 6
        c = 2 * "3"; // (string) "33"
        d = "3" * 4; // (string) "3333"
        e = 5 / 4; // (number) 1.25
        g = 5 / 5; // (number) 1
        _x = "2" * "3"; // ERR: Cannot use operator '*' with 'string' and 'string'
        _x = 5 / "4"; // ERR: Cannot use operator '/' with 'number' and 'string'
        _x = "20" / 4; // ERR: Cannot use operator '/' with 'string' and 'number'
        ```

### Unary operations
- `++`, `--`
    - Will add/subtract `1` to a number and, **based on the operator's position**, will return the original *or* the new value.
    - Examples:
        ```
        a = 5;
        b = ++a;
        // a == 6, b == 6

        a = 5;
        b = a++;
        // a == 6, b == 5

        a = 5;
        b = --a;
        // a == 4, b == 4

        a = 5;
        b = a--;
        // a == 4, b == 5
        ```
