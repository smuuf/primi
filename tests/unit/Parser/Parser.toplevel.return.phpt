<?php

declare(strict_types=1);

use Tester\Assert;

use Smuuf\Primi\Ex\SyntaxError;
use Smuuf\Primi\Code\Source;
use Smuuf\Primi\Code\BytecodeProvider;

require __DIR__ . '/../../bootstrap.php';

function compile(string $source) {
	return BytecodeProvider::compile(new Source($source));
}

//
// Not-OKs.
//

$src = <<<SRC
function hello() {
	print("hi there")
	return
}

function hello_2() {
	print("hi there, bruh")
	return 123
}

function hello_2() {
	print("hi there, bruh")
	function inside_hello() {
		return 456
	}
}

while (true) {
	continue
	break
}
SRC;
Assert::noError(fn() => compile($src));

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	function inside_hello() {
		return 456
	}
}
SRC;
Assert::noError(fn() => compile($src));

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	while(true) {
		break
		function lol_inner_function() {
			return 789;
		}
	}
}
SRC;
Assert::noError(fn() => compile($src));

//
// Not-OKs.
//

$src = <<<SRC
print("this is main")
return
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#return.*outside function#i');

$src = <<<SRC
print("this is main")

while (true) {
	return
}
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#return.*outside function#i');

$src = <<<SRC
print("this is main")
break
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#break.*outside loop#i');

$src = <<<SRC
print("this is main")
continue
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#continue.*outside loop#i');

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	return 456
}
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#return.*outside function#i');

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	break
}
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#break.*outside loop#i');

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	continue
}
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#continue.*outside loop#i');

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	while(true) {
		return 123
	}
}
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#return.*outside function#i');

$src = <<<SRC
class Yay {
	print("yay I'm in a class")
	while(true) {
		break
		function lol_inner_function() {
			break
		}
	}
}
SRC;
Assert::exception(fn() => compile($src), SyntaxError::class, '#break.*outside loop#i');
