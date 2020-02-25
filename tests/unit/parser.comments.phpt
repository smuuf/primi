<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

//
// Simple assignment.
//

$code = "a = 1;";
Assert::same(get_ast_array_simple($code), get_ast($code));

//
// Simple assignment with variations of comment.
// (comment should be ignored entirely)
//

$code = "a = 1; // hi there!";
Assert::same(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi there!";
Assert::same(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi there//man";
Assert::same(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi \"in quotes\"";
Assert::same(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi \"in quotes\"// with another comment";
Assert::same(get_ast_array_simple($code), get_ast($code));

//
// Test that parser doesn't treat "//" inside strings as comments.
//

$code = 'a = "after // slashes"';
Assert::same('after // slashes', get_ast($code)['stmts'][0]['right']['text']);

$code = 'a = "after // slashes"//with comment';
Assert::same('after // slashes', get_ast($code)['stmts'][0]['right']['text']);

$code = 'a = "after // slashes//";//with comment';
Assert::same('after // slashes//', get_ast($code)['stmts'][0]['right']['text']);

// Helpers.

function get_ast(string $source) {
	return (new \Smuuf\Primi\ParserHandler($source))->run();
}

function get_ast_array_simple() {

	return array(
		'name' => 'Program',
		'stmts' =>
		array(
			0 =>
			array(
				'name' => 'Assignment',
				'left' =>
				array(
					'name' => 'VariableName',
					'text' => 'a',
					'_l' => 1,
					'_p' => 0,
				),
				'right' =>
				array(
					'name' => 'NumberLiteral',
					'text' => '1',
					'_l' => 1,
					'_p' => 4,
				),
				'_l' => 1,
				'_p' => 0,
			),
		),
		'_l' => 1,
		'_p' => 0,
	); // thx to var_export();

}
