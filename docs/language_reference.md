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
Primi has 5 basic data types:
- Bool
- Number
- String
- Regex
- Array

### Bool
This basic type represents a primitive boolean "truth" value. Possible values are `true` and `false`.

Example usage:
```
a = true;
b = 1 == 2; // false
c = b == false; // true
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
Regex data type exists for advanced string matching. It is defined between two `/` forward-slash characters and treated as PCRE (Perl-compatible) regular expressions *(the same as within PHP itself)* with Unicode mode enabled.

Example usage:
```
a = "facebook";
b = /[o]{2}.*/;
c = a == b; // true
```

### Array
Arrays are untyped *(PHP-style)* containers that can accomodate multiple values of different *(or same)* types. Optionally, array index can be defined for a value. By default, integer index starting from the lowest index found (or from `0`) is used.

Example usage:
```
a = ["abc", 123, 4: true, false, /[A-Z]+/];
// Resulting array: [0: "abc", 1: 123, 4: true, 5: false, 6: /[A-Z]+/]
```

## Operators
Plethora of well known operators can be used to **define relationships** between and/or affect various values. Different operators can have various effects on various data types, some of which are covered down below.

### Assignment
- `=`
    - Assign some value to a variable.
    - Can also be used to insert values to values that support it (eg. arrays).
    - Examples:
        ```
        a = 1;
        b = "a word";
        c = false;
        d = /regul[ar]+/;
        e = ["x", "b": "z"];
        e["c"] = "x"; // e == ["x", "b": "z", "c": "x"]
        ```

### Addition and multiplication
- `+`, `-`
    - Perform addition (subtraction) of two values.
        - Numbers:
            - `+` Add two numbers.
            - `-` Subtract two numbers.
        - Strings:
            - '+' Concatenate two strings.
            - '-' Removes all occurences of the left side from the right side.
    - Examples:
        ```
        a = 5 + "4" // (number) 9
        b = 5 - 4; // (number) 1
        c = "a word and number " + 5; // "a word and number5"
        d = "a word" + " and one more"; // "a word and one more"
        e = "a word" - "or"; // "a wd"
        f = "regular expressions" - /regul[ar]+\s*/; // "expressions"
        ```
- `*`, `/`
    - Perform multiplication (division) of two values.
        - Numbers:
            - `+` Multiply two numbers.
            - `-` Divide two numbers.
    - Examples:
        ```
        a = 1 * 2; // 2
        b = 2 * "3"; // 6
        c = "3" * 4; // 12
        d = 5 / "4"; // 1.25
        e = 5 / 5; // 1
        f = "20" / 4; // 5
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
