<?php

use \Tester\Assert;

use \Smuuf\Primi\Handlers\KnownHandlers;

require __DIR__ . '/../bootstrap.php';

//
// Simple assignment.
//

$code = "a = 1;";
Assert::equal(get_ast_array_simple($code), get_ast($code));

//
// Simple assignment with variations of comment.
// (comment should be ignored entirely)
//

$code = "a = 1; // hi there!";
Assert::equal(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi there!";
Assert::equal(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi there//man";
Assert::equal(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi \"in quotes\"";
Assert::equal(get_ast_array_simple($code), get_ast($code));
$code = "a = 1;//hi \"in quotes\"// with another comment";
Assert::equal(get_ast_array_simple($code), get_ast($code));

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
	return (new \Smuuf\Primi\Parser\ParserHandler($source))->run();
}

function get_ast_array_simple() {

	$programHandlerId = KnownHandlers::fromName('Program');
	$assignment = KnownHandlers::fromName('Assignment');
	$variableName = KnownHandlers::fromName('VariableName');
	$numberLiteral = KnownHandlers::fromName('NumberLiteral');

	return array(
		'name' => $programHandlerId,
		'stmts' =>
		array(
			0 =>
			array(
				'name' => $assignment,
				'left' =>
				array(
					'name' => $variableName,
					'text' => 'a',
					'_l' => 1,
					'_p' => 0,
				),
				'right' =>
				array(
					'name' => $numberLiteral,
					'number' => '1',
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
