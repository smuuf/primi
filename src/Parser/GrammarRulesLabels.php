<?php

declare(strict_types=1);

namespace Smuuf\Primi\Parser;

abstract class GrammarRulesLabels {

	const LABELS = [
		'NumberLiteral' => "number literal",
		'BoolLiteral' => "bool literal",
		'StringLiteral' => "string literal",
		'StringInside' => "start of string literal",
		'FStringLiteral' => "f-string literal",
		'FStringInside' => "start of f-string literal",
		'FStringTxt' => "inside f-string literal",
		'VectorAttr' => "attribute access operator",
		'AttrAccess' => "attribute access operator",
		'DictDefinition' => "dict definition",
		'DictItem' => "dict item",
		'ListDefinition' => "list definition",
		'TupleDefinition' => "tuple definition",
		'AbstractLiteral' => "literal",
		'AbstractValue' => "literal or variable name",
		'AddOperator' => "'+' or '-' operator",
		'MultiplyOperator' => "'*' or '/' operator",
		'PowerOperator' => "'**' operator",
		'AssignmentOperator' => "assignment operator",
		'ComparisonOperator' => "comparison operator",
		'ComparisonOperatorWithWhitespace' => "membership operator",
		'AndOperator' => "'and' operator",
		'OrOperator' => "'or' operator",
		'NegationOperator' => "negation operator",
		'CondExpr' => "conditional expr",
		'ArgumentList' => "arguments list",
		'ParameterList' => "parameters list",
		'FunctionDefinition' => "function definition",
		'ClassDefinition' => "class definition",
		'ImportStatement' => "import ...",
		'IfStatement' => "if ...",
		'ForStatement' => "for ...",
		'WhileStatement' => "while ...",
		'TryStatement' => "try ...",
		'ReturnStatement' => "return statement",
		'BreakStatement' => "break statement",
		'ContinueStatement' => "continue statement",
		'Block' => "code block",
		'SEP' => "';' or newline",
		'VectorItem' => "square bracket access",
		'VariableName' => "variable name",
	];

	public static function getRuleLabel(?string $ruleName): ?string {
		return self::LABELS[$ruleName] ?? \null;
	}

}
