# Language reference

## Syntax
Primi is built with a familiar PHP/JS/C-like syntax. **Statements are **separated using the `;` semicolon character**. If there's no need to put multiple statements on a single line, the newline character can be used as statements' separator, too. Both of these syntaxes are therefore valid:

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
Primi has 5 basic data types:
- Bool
- Number
- String
- Regex
- Array

### Bool
This basic type represens a primitive boolean "truth" value. Possible values are `true` and `false`.

Example usage:
```
a = true;
b = 1 == 2; // false
c = b == false; // true
```

### Number
The general `number` data type harbors both integer and float values, whichever is needed at the time. THe maximum/minimum integer or float value/precision is determined by the version of PHP under which the Primi nterpreter runs.

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
Regex data type exists for advanced string matching. It is defined between two `/` forward-slash characters and treated as PCRE (Perl-compatible) regular expressions *(the same as within PHP itself)* with Unicode mode enabled.

Example usage:
```
a = "facebook";
b = /[o]{2}.*/;
c = a == b; // true
```

### Array
Arrays are untyped *(PHP-style)* containers that can accomodate multiple values of different *(or same)* types. Array index can be optionally defined for an array value. If index is not explicitly defined, integer index starting from the lowest present index found is used by default.

Example usage:
```
a = ["abc", 123, 4: true, false, /[A-Z]+/];
// Resulting array: [0: "abc", 1: 123, 4: true, 5: false, 6: /[A-Z]+/]
```
