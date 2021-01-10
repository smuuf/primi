<?php

namespace Smuuf\Primi\Parser;

use \hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

	// Add these properties so PHPStan doesn't complain about undefined properties.

	/** @var int */
	public $pos;

	/** @var string */
	public $string;

	private const RESERVED_WORDS = [
		'false', 'true', 'null', 'if', 'else', 'return', 'for', 'and', 'or',
		'function', 'break', 'continue', 'while', 'try', 'catch', 'not', 'in',
		'import'
	];

	/**
	 * Prevent parsing variable name which has the same name as some reserved
	 * word.
	 * Setting result to false will tell the parser that is should try other
	 * parser rules.
	 */
	protected function Mutable__finalise(&$result) {
		if (\in_array($result['text'], self::RESERVED_WORDS, \true)) {
			$result = \false;
		}
	}

/* StringInside: ( /\\./ | /[^{$quote}\\]/ )* */
protected $match_StringInside_typestack = ['StringInside'];
function match_StringInside($stack = []) {
	$matchrule = 'StringInside'; $result = $this->construct($matchrule, $matchrule);
	while (\true) {
		$res_6 = $result;
		$pos_6 = $this->pos;
		$_5 = \null;
		do {
			$_3 = \null;
			do {
				$res_0 = $result;
				$pos_0 = $this->pos;
				if (($subres = $this->rx('/\\\\./')) !== \false) {
					$result["text"] .= $subres;
					$_3 = \true; break;
				}
				$result = $res_0;
				$this->pos = $pos_0;
				if (($subres = $this->rx('/[^'.$this->expression($result, $stack, 'quote').'\\\\]/')) !== \false) {
					$result["text"] .= $subres;
					$_3 = \true; break;
				}
				$result = $res_0;
				$this->pos = $pos_0;
				$_3 = \false; break;
			}
			while(\false);
			if( $_3 === \false) { $_5 = \false; break; }
			$_5 = \true; break;
		}
		while(\false);
		if( $_5 === \false) {
			$result = $res_6;
			$this->pos = $pos_6;
			unset($res_6, $pos_6);
			break;
		}
	}
	return $this->finalise($result);
}


/* StringLiteral: quote:/['"]/ core:StringInside "$quote" */
protected $match_StringLiteral_typestack = ['StringLiteral'];
function match_StringLiteral($stack = []) {
	$matchrule = 'StringLiteral'; $result = $this->construct($matchrule, $matchrule);
	$_10 = \null;
	do {
		$stack[] = $result; $result = $this->construct( $matchrule, "quote" );
		if (($subres = $this->rx('/[\'"]/')) !== \false) {
			$result["text"] .= $subres;
			$subres = $result; $result = \array_pop($stack);
			$this->store( $result, $subres, 'quote' );
		}
		else {
			$result = \array_pop($stack);
			$_10 = \false; break;
		}
		$key = 'match_StringInside'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_StringInside(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_10 = \false; break; }
		if (($subres = $this->literal(''.$this->expression($result, $stack, 'quote').'')) !== \false) { $result["text"] .= $subres; }
		else { $_10 = \false; break; }
		$_10 = \true; break;
	}
	while(\false);
	if( $_10 === \true ) { return $this->finalise($result); }
	if( $_10 === \false) { return \false; }
}


/* FStringExpr: core:Variable */
protected $match_FStringExpr_typestack = ['FStringExpr'];
function match_FStringExpr($stack = []) {
	$matchrule = 'FStringExpr'; $result = $this->construct($matchrule, $matchrule);
	$key = 'match_Variable'; $pos = $this->pos;
	$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->match_Variable(\array_merge($stack, [$result])));
	if ($subres !== \false) {
		$this->store($result, $subres, "core");
		return $this->finalise($result);
	}
	else { return \false; }
}


/* FStringTxt: ( / (\\.)+ / | "{{" | "}}" | / [^\{\}{$quote}] / )* */
protected $match_FStringTxt_typestack = ['FStringTxt'];
function match_FStringTxt($stack = []) {
	$matchrule = 'FStringTxt'; $result = $this->construct($matchrule, $matchrule);
	while (\true) {
		$res_27 = $result;
		$pos_27 = $this->pos;
		$_26 = \null;
		do {
			$_24 = \null;
			do {
				$res_13 = $result;
				$pos_13 = $this->pos;
				if (($subres = $this->rx('/ (\\\\.)+ /')) !== \false) {
					$result["text"] .= $subres;
					$_24 = \true; break;
				}
				$result = $res_13;
				$this->pos = $pos_13;
				$_22 = \null;
				do {
					$res_15 = $result;
					$pos_15 = $this->pos;
					if (($subres = $this->literal('{{')) !== \false) {
						$result["text"] .= $subres;
						$_22 = \true; break;
					}
					$result = $res_15;
					$this->pos = $pos_15;
					$_20 = \null;
					do {
						$res_17 = $result;
						$pos_17 = $this->pos;
						if (($subres = $this->literal('}}')) !== \false) {
							$result["text"] .= $subres;
							$_20 = \true; break;
						}
						$result = $res_17;
						$this->pos = $pos_17;
						if (($subres = $this->rx('/ [^\{\}'.$this->expression($result, $stack, 'quote').'] /')) !== \false) {
							$result["text"] .= $subres;
							$_20 = \true; break;
						}
						$result = $res_17;
						$this->pos = $pos_17;
						$_20 = \false; break;
					}
					while(\false);
					if( $_20 === \true ) { $_22 = \true; break; }
					$result = $res_15;
					$this->pos = $pos_15;
					$_22 = \false; break;
				}
				while(\false);
				if( $_22 === \true ) { $_24 = \true; break; }
				$result = $res_13;
				$this->pos = $pos_13;
				$_24 = \false; break;
			}
			while(\false);
			if( $_24 === \false) { $_26 = \false; break; }
			$_26 = \true; break;
		}
		while(\false);
		if( $_26 === \false) {
			$result = $res_27;
			$this->pos = $pos_27;
			unset($res_27, $pos_27);
			break;
		}
	}
	return $this->finalise($result);
}


/* FStringInside: parts:FStringTxt ( "{" parts:FStringExpr "}" parts:FStringTxt )* */
protected $match_FStringInside_typestack = ['FStringInside'];
function match_FStringInside($stack = []) {
	$matchrule = 'FStringInside'; $result = $this->construct($matchrule, $matchrule);
	$_35 = \null;
	do {
		$key = 'match_FStringTxt'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FStringTxt(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "parts");
		}
		else { $_35 = \false; break; }
		while (\true) {
			$res_34 = $result;
			$pos_34 = $this->pos;
			$_33 = \null;
			do {
				if (\substr($this->string, $this->pos, 1) === '{') {
					$this->pos += 1;
					$result["text"] .= '{';
				}
				else { $_33 = \false; break; }
				$key = 'match_FStringExpr'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_FStringExpr(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "parts");
				}
				else { $_33 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === '}') {
					$this->pos += 1;
					$result["text"] .= '}';
				}
				else { $_33 = \false; break; }
				$key = 'match_FStringTxt'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_FStringTxt(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "parts");
				}
				else { $_33 = \false; break; }
				$_33 = \true; break;
			}
			while(\false);
			if( $_33 === \false) {
				$result = $res_34;
				$this->pos = $pos_34;
				unset($res_34, $pos_34);
				break;
			}
		}
		$_35 = \true; break;
	}
	while(\false);
	if( $_35 === \true ) { return $this->finalise($result); }
	if( $_35 === \false) { return \false; }
}


/* FStringLiteral: "f" quote:/['"]/ core:FStringInside "$quote" */
protected $match_FStringLiteral_typestack = ['FStringLiteral'];
function match_FStringLiteral($stack = []) {
	$matchrule = 'FStringLiteral'; $result = $this->construct($matchrule, $matchrule);
	$_41 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === 'f') {
			$this->pos += 1;
			$result["text"] .= 'f';
		}
		else { $_41 = \false; break; }
		$stack[] = $result; $result = $this->construct( $matchrule, "quote" );
		if (($subres = $this->rx('/[\'"]/')) !== \false) {
			$result["text"] .= $subres;
			$subres = $result; $result = \array_pop($stack);
			$this->store( $result, $subres, 'quote' );
		}
		else {
			$result = \array_pop($stack);
			$_41 = \false; break;
		}
		$key = 'match_FStringInside'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FStringInside(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_41 = \false; break; }
		if (($subres = $this->literal(''.$this->expression($result, $stack, 'quote').'')) !== \false) { $result["text"] .= $subres; }
		else { $_41 = \false; break; }
		$_41 = \true; break;
	}
	while(\false);
	if( $_41 === \true ) { return $this->finalise($result); }
	if( $_41 === \false) { return \false; }
}


/* NumberLiteral: / -?\d[\d_]*(\.[\d_]+)? / */
protected $match_NumberLiteral_typestack = ['NumberLiteral'];
function match_NumberLiteral($stack = []) {
	$matchrule = 'NumberLiteral'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ -?\d[\d_]*(\.[\d_]+)? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: / \b(true|false)\b / */
protected $match_BoolLiteral_typestack = ['BoolLiteral'];
function match_BoolLiteral($stack = []) {
	$matchrule = 'BoolLiteral'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ \b(true|false)\b /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NullLiteral: "null" !VariableName */
protected $match_NullLiteral_typestack = ['NullLiteral'];
function match_NullLiteral($stack = []) {
	$matchrule = 'NullLiteral'; $result = $this->construct($matchrule, $matchrule);
	$_47 = \null;
	do {
		if (($subres = $this->literal('null')) !== \false) { $result["text"] .= $subres; }
		else { $_47 = \false; break; }
		$res_46 = $result;
		$pos_46 = $this->pos;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$result = $res_46;
			$this->pos = $pos_46;
			$_47 = \false; break;
		}
		else {
			$result = $res_46;
			$this->pos = $pos_46;
		}
		$_47 = \true; break;
	}
	while(\false);
	if( $_47 === \true ) { return $this->finalise($result); }
	if( $_47 === \false) { return \false; }
}


/* RegexLiteral: "rx" core:StringLiteral */
protected $match_RegexLiteral_typestack = ['RegexLiteral'];
function match_RegexLiteral($stack = []) {
	$matchrule = 'RegexLiteral'; $result = $this->construct($matchrule, $matchrule);
	$_51 = \null;
	do {
		if (($subres = $this->literal('rx')) !== \false) { $result["text"] .= $subres; }
		else { $_51 = \false; break; }
		$key = 'match_StringLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_StringLiteral(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_51 = \false; break; }
		$_51 = \true; break;
	}
	while(\false);
	if( $_51 === \true ) { return $this->finalise($result); }
	if( $_51 === \false) { return \false; }
}


/* Nothing: "" */
protected $match_Nothing_typestack = ['Nothing'];
function match_Nothing($stack = []) {
	$matchrule = 'Nothing'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Literal: &/["']/ skip:StringLiteral | skip:NumberLiteral | skip:BoolLiteral | ( &"n" skip:NullLiteral ) | ( &"rx" skip:RegexLiteral ) | ( &"f" skip:FStringLiteral ) */
protected $match_Literal_typestack = ['Literal'];
function match_Literal($stack = []) {
	$matchrule = 'Literal'; $result = $this->construct($matchrule, $matchrule);
	$_85 = \null;
	do {
		$res_54 = $result;
		$pos_54 = $this->pos;
		$_57 = \null;
		do {
			$res_55 = $result;
			$pos_55 = $this->pos;
			if (($subres = $this->rx('/["\']/')) !== \false) {
				$result["text"] .= $subres;
				$result = $res_55;
				$this->pos = $pos_55;
			}
			else {
				$result = $res_55;
				$this->pos = $pos_55;
				$_57 = \false; break;
			}
			$key = 'match_StringLiteral'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_StringLiteral(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_57 = \false; break; }
			$_57 = \true; break;
		}
		while(\false);
		if( $_57 === \true ) { $_85 = \true; break; }
		$result = $res_54;
		$this->pos = $pos_54;
		$_83 = \null;
		do {
			$res_59 = $result;
			$pos_59 = $this->pos;
			$key = 'match_NumberLiteral'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_NumberLiteral(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_83 = \true; break;
			}
			$result = $res_59;
			$this->pos = $pos_59;
			$_81 = \null;
			do {
				$res_61 = $result;
				$pos_61 = $this->pos;
				$key = 'match_BoolLiteral'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BoolLiteral(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_81 = \true; break;
				}
				$result = $res_61;
				$this->pos = $pos_61;
				$_79 = \null;
				do {
					$res_63 = $result;
					$pos_63 = $this->pos;
					$_66 = \null;
					do {
						$res_64 = $result;
						$pos_64 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === 'n') {
							$this->pos += 1;
							$result["text"] .= 'n';
							$result = $res_64;
							$this->pos = $pos_64;
						}
						else {
							$result = $res_64;
							$this->pos = $pos_64;
							$_66 = \false; break;
						}
						$key = 'match_NullLiteral'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_NullLiteral(\array_merge($stack, [$result])));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
						}
						else { $_66 = \false; break; }
						$_66 = \true; break;
					}
					while(\false);
					if( $_66 === \true ) { $_79 = \true; break; }
					$result = $res_63;
					$this->pos = $pos_63;
					$_77 = \null;
					do {
						$res_68 = $result;
						$pos_68 = $this->pos;
						$_71 = \null;
						do {
							$res_69 = $result;
							$pos_69 = $this->pos;
							if (($subres = $this->literal('rx')) !== \false) {
								$result["text"] .= $subres;
								$result = $res_69;
								$this->pos = $pos_69;
							}
							else {
								$result = $res_69;
								$this->pos = $pos_69;
								$_71 = \false; break;
							}
							$key = 'match_RegexLiteral'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_RegexLiteral(\array_merge($stack, [$result])));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
							}
							else { $_71 = \false; break; }
							$_71 = \true; break;
						}
						while(\false);
						if( $_71 === \true ) { $_77 = \true; break; }
						$result = $res_68;
						$this->pos = $pos_68;
						$_75 = \null;
						do {
							$res_73 = $result;
							$pos_73 = $this->pos;
							if (\substr($this->string, $this->pos, 1) === 'f') {
								$this->pos += 1;
								$result["text"] .= 'f';
								$result = $res_73;
								$this->pos = $pos_73;
							}
							else {
								$result = $res_73;
								$this->pos = $pos_73;
								$_75 = \false; break;
							}
							$key = 'match_FStringLiteral'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_FStringLiteral(\array_merge($stack, [$result])));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
							}
							else { $_75 = \false; break; }
							$_75 = \true; break;
						}
						while(\false);
						if( $_75 === \true ) { $_77 = \true; break; }
						$result = $res_68;
						$this->pos = $pos_68;
						$_77 = \false; break;
					}
					while(\false);
					if( $_77 === \true ) { $_79 = \true; break; }
					$result = $res_63;
					$this->pos = $pos_63;
					$_79 = \false; break;
				}
				while(\false);
				if( $_79 === \true ) { $_81 = \true; break; }
				$result = $res_61;
				$this->pos = $pos_61;
				$_81 = \false; break;
			}
			while(\false);
			if( $_81 === \true ) { $_83 = \true; break; }
			$result = $res_59;
			$this->pos = $pos_59;
			$_83 = \false; break;
		}
		while(\false);
		if( $_83 === \true ) { $_85 = \true; break; }
		$result = $res_54;
		$this->pos = $pos_54;
		$_85 = \false; break;
	}
	while(\false);
	if( $_85 === \true ) { return $this->finalise($result); }
	if( $_85 === \false) { return \false; }
}


/* VariableName: / (?:[a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = ['VariableName'];
function match_VariableName($stack = []) {
	$matchrule = 'VariableName'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ (?:[a-zA-Z_][a-zA-Z0-9_]*) /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Variable: core:VariableName */
protected $match_Variable_typestack = ['Variable'];
function match_Variable($stack = []) {
	$matchrule = 'Variable'; $result = $this->construct($matchrule, $matchrule);
	$key = 'match_VariableName'; $pos = $this->pos;
	$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
	if ($subres !== \false) {
		$this->store($result, $subres, "core");
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AnonymousFunction: "function" __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ params:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = ['AnonymousFunction'];
function match_AnonymousFunction($stack = []) {
	$matchrule = 'AnonymousFunction'; $result = $this->construct($matchrule, $matchrule);
	$_112 = \null;
	do {
		$res_89 = $result;
		$pos_89 = $this->pos;
		$_99 = \null;
		do {
			if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
			else { $_99 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_99 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_99 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_99 = \false; break; }
			$res_94 = $result;
			$pos_94 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_94;
				$this->pos = $pos_94;
				unset($res_94, $pos_94);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_99 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_99 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_99 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_99 = \false; break; }
			$_99 = \true; break;
		}
		while(\false);
		if( $_99 === \true ) { $_112 = \true; break; }
		$result = $res_89;
		$this->pos = $pos_89;
		$_110 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_110 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_110 = \false; break; }
			$res_103 = $result;
			$pos_103 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_103;
				$this->pos = $pos_103;
				unset($res_103, $pos_103);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_110 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_110 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_110 = \false; break; }
			if (($subres = $this->literal('=>')) !== \false) { $result["text"] .= $subres; }
			else { $_110 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_110 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_110 = \false; break; }
			$_110 = \true; break;
		}
		while(\false);
		if( $_110 === \true ) { $_112 = \true; break; }
		$result = $res_89;
		$this->pos = $pos_89;
		$_112 = \false; break;
	}
	while(\false);
	if( $_112 === \true ) { return $this->finalise($result); }
	if( $_112 === \false) { return \false; }
}


/* DictItem: __ key:Expression __ ":" __ value:Expression __ */
protected $match_DictItem_typestack = ['DictItem'];
function match_DictItem($stack = []) {
	$matchrule = 'DictItem'; $result = $this->construct($matchrule, $matchrule);
	$_121 = \null;
	do {
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_121 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_121 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_121 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_121 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_121 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "value");
		}
		else { $_121 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_121 = \false; break; }
		$_121 = \true; break;
	}
	while(\false);
	if( $_121 === \true ) { return $this->finalise($result); }
	if( $_121 === \false) { return \false; }
}


/* DictDefinition: "{" __ ( items:DictItem ( __ "," __ items:DictItem )* )? __ ( "," __ )? "}" */
protected $match_DictDefinition_typestack = ['DictDefinition'];
function match_DictDefinition($stack = []) {
	$matchrule = 'DictDefinition'; $result = $this->construct($matchrule, $matchrule);
	$_140 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_140 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_140 = \false; break; }
		$res_133 = $result;
		$pos_133 = $this->pos;
		$_132 = \null;
		do {
			$key = 'match_DictItem'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_DictItem(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_132 = \false; break; }
			while (\true) {
				$res_131 = $result;
				$pos_131 = $this->pos;
				$_130 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_130 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_130 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_130 = \false; break; }
					$key = 'match_DictItem'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_DictItem(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_130 = \false; break; }
					$_130 = \true; break;
				}
				while(\false);
				if( $_130 === \false) {
					$result = $res_131;
					$this->pos = $pos_131;
					unset($res_131, $pos_131);
					break;
				}
			}
			$_132 = \true; break;
		}
		while(\false);
		if( $_132 === \false) {
			$result = $res_133;
			$this->pos = $pos_133;
			unset($res_133, $pos_133);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_140 = \false; break; }
		$res_138 = $result;
		$pos_138 = $this->pos;
		$_137 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_137 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_137 = \false; break; }
			$_137 = \true; break;
		}
		while(\false);
		if( $_137 === \false) {
			$result = $res_138;
			$this->pos = $pos_138;
			unset($res_138, $pos_138);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_140 = \false; break; }
		$_140 = \true; break;
	}
	while(\false);
	if( $_140 === \true ) { return $this->finalise($result); }
	if( $_140 === \false) { return \false; }
}


/* ListDefinition: "[" __ ( items:Expression ( __ "," __ items:Expression )* )? __ ( "," __ )? "]" */
protected $match_ListDefinition_typestack = ['ListDefinition'];
function match_ListDefinition($stack = []) {
	$matchrule = 'ListDefinition'; $result = $this->construct($matchrule, $matchrule);
	$_159 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_159 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_159 = \false; break; }
		$res_152 = $result;
		$pos_152 = $this->pos;
		$_151 = \null;
		do {
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_151 = \false; break; }
			while (\true) {
				$res_150 = $result;
				$pos_150 = $this->pos;
				$_149 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_149 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_149 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_149 = \false; break; }
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_149 = \false; break; }
					$_149 = \true; break;
				}
				while(\false);
				if( $_149 === \false) {
					$result = $res_150;
					$this->pos = $pos_150;
					unset($res_150, $pos_150);
					break;
				}
			}
			$_151 = \true; break;
		}
		while(\false);
		if( $_151 === \false) {
			$result = $res_152;
			$this->pos = $pos_152;
			unset($res_152, $pos_152);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_159 = \false; break; }
		$res_157 = $result;
		$pos_157 = $this->pos;
		$_156 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_156 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_156 = \false; break; }
			$_156 = \true; break;
		}
		while(\false);
		if( $_156 === \false) {
			$result = $res_157;
			$this->pos = $pos_157;
			unset($res_157, $pos_157);
		}
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_159 = \false; break; }
		$_159 = \true; break;
	}
	while(\false);
	if( $_159 === \true ) { return $this->finalise($result); }
	if( $_159 === \false) { return \false; }
}


/* AbstractValue: skip:Literal | skip:Variable | skip:ListDefinition | skip:DictDefinition */
protected $match_AbstractValue_typestack = ['AbstractValue'];
function match_AbstractValue($stack = []) {
	$matchrule = 'AbstractValue'; $result = $this->construct($matchrule, $matchrule);
	$_172 = \null;
	do {
		$res_161 = $result;
		$pos_161 = $this->pos;
		$key = 'match_Literal'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Literal(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_172 = \true; break;
		}
		$result = $res_161;
		$this->pos = $pos_161;
		$_170 = \null;
		do {
			$res_163 = $result;
			$pos_163 = $this->pos;
			$key = 'match_Variable'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Variable(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_170 = \true; break;
			}
			$result = $res_163;
			$this->pos = $pos_163;
			$_168 = \null;
			do {
				$res_165 = $result;
				$pos_165 = $this->pos;
				$key = 'match_ListDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ListDefinition(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_168 = \true; break;
				}
				$result = $res_165;
				$this->pos = $pos_165;
				$key = 'match_DictDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_DictDefinition(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_168 = \true; break;
				}
				$result = $res_165;
				$this->pos = $pos_165;
				$_168 = \false; break;
			}
			while(\false);
			if( $_168 === \true ) { $_170 = \true; break; }
			$result = $res_163;
			$this->pos = $pos_163;
			$_170 = \false; break;
		}
		while(\false);
		if( $_170 === \true ) { $_172 = \true; break; }
		$result = $res_161;
		$this->pos = $pos_161;
		$_172 = \false; break;
	}
	while(\false);
	if( $_172 === \true ) { return $this->finalise($result); }
	if( $_172 === \false) { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = ['AddOperator'];
function match_AddOperator($stack = []) {
	$matchrule = 'AddOperator'; $result = $this->construct($matchrule, $matchrule);
	$_177 = \null;
	do {
		$res_174 = $result;
		$pos_174 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_177 = \true; break;
		}
		$result = $res_174;
		$this->pos = $pos_174;
		if (\substr($this->string, $this->pos, 1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_177 = \true; break;
		}
		$result = $res_174;
		$this->pos = $pos_174;
		$_177 = \false; break;
	}
	while(\false);
	if( $_177 === \true ) { return $this->finalise($result); }
	if( $_177 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = ['MultiplyOperator'];
function match_MultiplyOperator($stack = []) {
	$matchrule = 'MultiplyOperator'; $result = $this->construct($matchrule, $matchrule);
	$_182 = \null;
	do {
		$res_179 = $result;
		$pos_179 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_182 = \true; break;
		}
		$result = $res_179;
		$this->pos = $pos_179;
		if (\substr($this->string, $this->pos, 1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_182 = \true; break;
		}
		$result = $res_179;
		$this->pos = $pos_179;
		$_182 = \false; break;
	}
	while(\false);
	if( $_182 === \true ) { return $this->finalise($result); }
	if( $_182 === \false) { return \false; }
}


/* PowerOperator: "**" */
protected $match_PowerOperator_typestack = ['PowerOperator'];
function match_PowerOperator($stack = []) {
	$matchrule = 'PowerOperator'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('**')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = ['AssignmentOperator'];
function match_AssignmentOperator($stack = []) {
	$matchrule = 'AssignmentOperator'; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = ['ComparisonOperator'];
function match_ComparisonOperator($stack = []) {
	$matchrule = 'ComparisonOperator'; $result = $this->construct($matchrule, $matchrule);
	$_205 = \null;
	do {
		$res_186 = $result;
		$pos_186 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_205 = \true; break;
		}
		$result = $res_186;
		$this->pos = $pos_186;
		$_203 = \null;
		do {
			$res_188 = $result;
			$pos_188 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_203 = \true; break;
			}
			$result = $res_188;
			$this->pos = $pos_188;
			$_201 = \null;
			do {
				$res_190 = $result;
				$pos_190 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_201 = \true; break;
				}
				$result = $res_190;
				$this->pos = $pos_190;
				$_199 = \null;
				do {
					$res_192 = $result;
					$pos_192 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_199 = \true; break;
					}
					$result = $res_192;
					$this->pos = $pos_192;
					$_197 = \null;
					do {
						$res_194 = $result;
						$pos_194 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_197 = \true; break;
						}
						$result = $res_194;
						$this->pos = $pos_194;
						if (\substr($this->string, $this->pos, 1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_197 = \true; break;
						}
						$result = $res_194;
						$this->pos = $pos_194;
						$_197 = \false; break;
					}
					while(\false);
					if( $_197 === \true ) { $_199 = \true; break; }
					$result = $res_192;
					$this->pos = $pos_192;
					$_199 = \false; break;
				}
				while(\false);
				if( $_199 === \true ) { $_201 = \true; break; }
				$result = $res_190;
				$this->pos = $pos_190;
				$_201 = \false; break;
			}
			while(\false);
			if( $_201 === \true ) { $_203 = \true; break; }
			$result = $res_188;
			$this->pos = $pos_188;
			$_203 = \false; break;
		}
		while(\false);
		if( $_203 === \true ) { $_205 = \true; break; }
		$result = $res_186;
		$this->pos = $pos_186;
		$_205 = \false; break;
	}
	while(\false);
	if( $_205 === \true ) { return $this->finalise($result); }
	if( $_205 === \false) { return \false; }
}


/* ComparisonOperatorWithWhitespace: "in"  | "not in" */
protected $match_ComparisonOperatorWithWhitespace_typestack = ['ComparisonOperatorWithWhitespace'];
function match_ComparisonOperatorWithWhitespace($stack = []) {
	$matchrule = 'ComparisonOperatorWithWhitespace'; $result = $this->construct($matchrule, $matchrule);
	$_210 = \null;
	do {
		$res_207 = $result;
		$pos_207 = $this->pos;
		if (($subres = $this->literal('in')) !== \false) {
			$result["text"] .= $subres;
			$_210 = \true; break;
		}
		$result = $res_207;
		$this->pos = $pos_207;
		if (($subres = $this->literal('not in')) !== \false) {
			$result["text"] .= $subres;
			$_210 = \true; break;
		}
		$result = $res_207;
		$this->pos = $pos_207;
		$_210 = \false; break;
	}
	while(\false);
	if( $_210 === \true ) { return $this->finalise($result); }
	if( $_210 === \false) { return \false; }
}


/* AndOperator: "and" */
protected $match_AndOperator_typestack = ['AndOperator'];
function match_AndOperator($stack = []) {
	$matchrule = 'AndOperator'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('and')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* OrOperator: "or" */
protected $match_OrOperator_typestack = ['OrOperator'];
function match_OrOperator($stack = []) {
	$matchrule = 'OrOperator'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('or')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NegationOperator: "!" */
protected $match_NegationOperator_typestack = ['NegationOperator'];
function match_NegationOperator($stack = []) {
	$matchrule = 'NegationOperator'; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '!') {
		$this->pos += 1;
		$result["text"] .= '!';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:CondExpr */
protected $match_Expression_typestack = ['Expression'];
function match_Expression($stack = []) {
	$matchrule = 'Expression'; $result = $this->construct($matchrule, $matchrule);
	$_222 = \null;
	do {
		$res_215 = $result;
		$pos_215 = $this->pos;
		$key = 'match_AnonymousFunction'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AnonymousFunction(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_222 = \true; break;
		}
		$result = $res_215;
		$this->pos = $pos_215;
		$_220 = \null;
		do {
			$res_217 = $result;
			$pos_217 = $this->pos;
			$key = 'match_Assignment'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Assignment(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_220 = \true; break;
			}
			$result = $res_217;
			$this->pos = $pos_217;
			$key = 'match_CondExpr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_CondExpr(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_220 = \true; break;
			}
			$result = $res_217;
			$this->pos = $pos_217;
			$_220 = \false; break;
		}
		while(\false);
		if( $_220 === \true ) { $_222 = \true; break; }
		$result = $res_215;
		$this->pos = $pos_215;
		$_222 = \false; break;
	}
	while(\false);
	if( $_222 === \true ) { return $this->finalise($result); }
	if( $_222 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment($stack = []) {
	$matchrule = 'Assignment'; $result = $this->construct($matchrule, $matchrule);
	$_229 = \null;
	do {
		$key = 'match_Mutable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Mutable(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_229 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_229 = \false; break; }
		$key = 'match_AssignmentOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AssignmentOperator(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_229 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_229 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_229 = \false; break; }
		$_229 = \true; break;
	}
	while(\false);
	if( $_229 === \true ) { return $this->finalise($result); }
	if( $_229 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = ['Mutable'];
function match_Mutable($stack = []) {
	$matchrule = 'Mutable'; $result = $this->construct($matchrule, $matchrule);
	$_234 = \null;
	do {
		$res_231 = $result;
		$pos_231 = $this->pos;
		$key = 'match_VariableVector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableVector(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_234 = \true; break;
		}
		$result = $res_231;
		$this->pos = $pos_231;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_234 = \true; break;
		}
		$result = $res_231;
		$this->pos = $pos_231;
		$_234 = \false; break;
	}
	while(\false);
	if( $_234 === \true ) { return $this->finalise($result); }
	if( $_234 === \false) { return \false; }
}


/* VariableVector: core:Variable ( ( vector:VectorAttr | vector:VectorItem )+ vector:VectorItemNoIndex? | vector:VectorItemNoIndex ) */
protected $match_VariableVector_typestack = ['VariableVector'];
function match_VariableVector($stack = []) {
	$matchrule = 'VariableVector'; $result = $this->construct($matchrule, $matchrule);
	$_253 = \null;
	do {
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_253 = \false; break; }
		$_251 = \null;
		do {
			$_249 = \null;
			do {
				$res_237 = $result;
				$pos_237 = $this->pos;
				$_246 = \null;
				do {
					$count_244 = 0;
					while (\true) {
						$res_244 = $result;
						$pos_244 = $this->pos;
						$_243 = \null;
						do {
							$_241 = \null;
							do {
								$res_238 = $result;
								$pos_238 = $this->pos;
								$key = 'match_VectorAttr'; $pos = $this->pos;
								$subres = $this->packhas($key, $pos)
									? $this->packread($key, $pos)
									: $this->packwrite($key, $pos, $this->match_VectorAttr(\array_merge($stack, [$result])));
								if ($subres !== \false) {
									$this->store($result, $subres, "vector");
									$_241 = \true; break;
								}
								$result = $res_238;
								$this->pos = $pos_238;
								$key = 'match_VectorItem'; $pos = $this->pos;
								$subres = $this->packhas($key, $pos)
									? $this->packread($key, $pos)
									: $this->packwrite($key, $pos, $this->match_VectorItem(\array_merge($stack, [$result])));
								if ($subres !== \false) {
									$this->store($result, $subres, "vector");
									$_241 = \true; break;
								}
								$result = $res_238;
								$this->pos = $pos_238;
								$_241 = \false; break;
							}
							while(\false);
							if( $_241 === \false) { $_243 = \false; break; }
							$_243 = \true; break;
						}
						while(\false);
						if( $_243 === \false) {
							$result = $res_244;
							$this->pos = $pos_244;
							unset($res_244, $pos_244);
							break;
						}
						$count_244++;
					}
					if ($count_244 >= 1) {  }
					else { $_246 = \false; break; }
					$res_245 = $result;
					$pos_245 = $this->pos;
					$key = 'match_VectorItemNoIndex'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_VectorItemNoIndex(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "vector");
					}
					else {
						$result = $res_245;
						$this->pos = $pos_245;
						unset($res_245, $pos_245);
					}
					$_246 = \true; break;
				}
				while(\false);
				if( $_246 === \true ) { $_249 = \true; break; }
				$result = $res_237;
				$this->pos = $pos_237;
				$key = 'match_VectorItemNoIndex'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VectorItemNoIndex(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "vector");
					$_249 = \true; break;
				}
				$result = $res_237;
				$this->pos = $pos_237;
				$_249 = \false; break;
			}
			while(\false);
			if( $_249 === \false) { $_251 = \false; break; }
			$_251 = \true; break;
		}
		while(\false);
		if( $_251 === \false) { $_253 = \false; break; }
		$_253 = \true; break;
	}
	while(\false);
	if( $_253 === \true ) { return $this->finalise($result); }
	if( $_253 === \false) { return \false; }
}


/* VectorItem: "[" __ index:Expression __ "]" */
protected $match_VectorItem_typestack = ['VectorItem'];
function match_VectorItem($stack = []) {
	$matchrule = 'VectorItem'; $result = $this->construct($matchrule, $matchrule);
	$_260 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_260 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_260 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "index");
		}
		else { $_260 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_260 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_260 = \false; break; }
		$_260 = \true; break;
	}
	while(\false);
	if( $_260 === \true ) { return $this->finalise($result); }
	if( $_260 === \false) { return \false; }
}


/* VectorItemNoIndex: "[" __ "]" */
protected $match_VectorItemNoIndex_typestack = ['VectorItemNoIndex'];
function match_VectorItemNoIndex($stack = []) {
	$matchrule = 'VectorItemNoIndex'; $result = $this->construct($matchrule, $matchrule);
	$_265 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_265 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_265 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_265 = \false; break; }
		$_265 = \true; break;
	}
	while(\false);
	if( $_265 === \true ) { return $this->finalise($result); }
	if( $_265 === \false) { return \false; }
}


/* VectorAttr: "." attr:VariableName */
protected $match_VectorAttr_typestack = ['VectorAttr'];
function match_VectorAttr($stack = []) {
	$matchrule = 'VectorAttr'; $result = $this->construct($matchrule, $matchrule);
	$_269 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_269 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "attr");
		}
		else { $_269 = \false; break; }
		$_269 = \true; break;
	}
	while(\false);
	if( $_269 === \true ) { return $this->finalise($result); }
	if( $_269 === \false) { return \false; }
}


/* Chain: &/[\[\(\.]/ ( core: AttrAccess | core:Dereference | core:Invocation ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain($stack = []) {
	$matchrule = 'Chain'; $result = $this->construct($matchrule, $matchrule);
	$_284 = \null;
	do {
		$res_271 = $result;
		$pos_271 = $this->pos;
		if (($subres = $this->rx('/[\[\(\.]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_271;
			$this->pos = $pos_271;
		}
		else {
			$result = $res_271;
			$this->pos = $pos_271;
			$_284 = \false; break;
		}
		$_281 = \null;
		do {
			$_279 = \null;
			do {
				$res_272 = $result;
				$pos_272 = $this->pos;
				$key = 'match_AttrAccess'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AttrAccess(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_279 = \true; break;
				}
				$result = $res_272;
				$this->pos = $pos_272;
				$_277 = \null;
				do {
					$res_274 = $result;
					$pos_274 = $this->pos;
					$key = 'match_Dereference'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Dereference(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_277 = \true; break;
					}
					$result = $res_274;
					$this->pos = $pos_274;
					$key = 'match_Invocation'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Invocation(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_277 = \true; break;
					}
					$result = $res_274;
					$this->pos = $pos_274;
					$_277 = \false; break;
				}
				while(\false);
				if( $_277 === \true ) { $_279 = \true; break; }
				$result = $res_272;
				$this->pos = $pos_272;
				$_279 = \false; break;
			}
			while(\false);
			if( $_279 === \false) { $_281 = \false; break; }
			$_281 = \true; break;
		}
		while(\false);
		if( $_281 === \false) { $_284 = \false; break; }
		$res_283 = $result;
		$pos_283 = $this->pos;
		$key = 'match_Chain'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Chain(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_283;
			$this->pos = $pos_283;
			unset($res_283, $pos_283);
		}
		$_284 = \true; break;
	}
	while(\false);
	if( $_284 === \true ) { return $this->finalise($result); }
	if( $_284 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference($stack = []) {
	$matchrule = 'Dereference'; $result = $this->construct($matchrule, $matchrule);
	$_291 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_291 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_291 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_291 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_291 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_291 = \false; break; }
		$_291 = \true; break;
	}
	while(\false);
	if( $_291 === \true ) { return $this->finalise($result); }
	if( $_291 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation($stack = []) {
	$matchrule = 'Invocation'; $result = $this->construct($matchrule, $matchrule);
	$_298 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_298 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_298 = \false; break; }
		$res_295 = $result;
		$pos_295 = $this->pos;
		$key = 'match_ArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ArgumentList(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_295;
			$this->pos = $pos_295;
			unset($res_295, $pos_295);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_298 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_298 = \false; break; }
		$_298 = \true; break;
	}
	while(\false);
	if( $_298 === \true ) { return $this->finalise($result); }
	if( $_298 === \false) { return \false; }
}


/* AttrAccess: "." attr:VariableName */
protected $match_AttrAccess_typestack = ['AttrAccess'];
function match_AttrAccess($stack = []) {
	$matchrule = 'AttrAccess'; $result = $this->construct($matchrule, $matchrule);
	$_302 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_302 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "attr");
		}
		else { $_302 = \false; break; }
		$_302 = \true; break;
	}
	while(\false);
	if( $_302 === \true ) { return $this->finalise($result); }
	if( $_302 === \false) { return \false; }
}


/* CondExpr: true:LogicalOr ( ] "if" __ "(" __ cond:Expression __ ")" __ "else" ] false:LogicalOr )? */
protected $match_CondExpr_typestack = ['CondExpr'];
function match_CondExpr($stack = []) {
	$matchrule = 'CondExpr'; $result = $this->construct($matchrule, $matchrule);
	$_319 = \null;
	do {
		$key = 'match_LogicalOr'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalOr(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "true");
		}
		else { $_319 = \false; break; }
		$res_318 = $result;
		$pos_318 = $this->pos;
		$_317 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_317 = \false; break; }
			if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
			else { $_317 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_317 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_317 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_317 = \false; break; }
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "cond");
			}
			else { $_317 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_317 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_317 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_317 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_317 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_317 = \false; break; }
			$key = 'match_LogicalOr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_LogicalOr(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "false");
			}
			else { $_317 = \false; break; }
			$_317 = \true; break;
		}
		while(\false);
		if( $_317 === \false) {
			$result = $res_318;
			$this->pos = $pos_318;
			unset($res_318, $pos_318);
		}
		$_319 = \true; break;
	}
	while(\false);
	if( $_319 === \true ) { return $this->finalise($result); }
	if( $_319 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr($stack = []) {
	$matchrule = 'LogicalOr'; $result = $this->construct($matchrule, $matchrule);
	$_328 = \null;
	do {
		$key = 'match_LogicalAnd'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalAnd(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_328 = \false; break; }
		while (\true) {
			$res_327 = $result;
			$pos_327 = $this->pos;
			$_326 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_326 = \false; break; }
				$key = 'match_OrOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_OrOperator(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_326 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_326 = \false; break; }
				$key = 'match_LogicalAnd'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_LogicalAnd(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_326 = \false; break; }
				$_326 = \true; break;
			}
			while(\false);
			if( $_326 === \false) {
				$result = $res_327;
				$this->pos = $pos_327;
				unset($res_327, $pos_327);
				break;
			}
		}
		$_328 = \true; break;
	}
	while(\false);
	if( $_328 === \true ) { return $this->finalise($result); }
	if( $_328 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd($stack = []) {
	$matchrule = 'LogicalAnd'; $result = $this->construct($matchrule, $matchrule);
	$_337 = \null;
	do {
		$key = 'match_Comparison'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Comparison(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_337 = \false; break; }
		while (\true) {
			$res_336 = $result;
			$pos_336 = $this->pos;
			$_335 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_335 = \false; break; }
				$key = 'match_AndOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AndOperator(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_335 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_335 = \false; break; }
				$key = 'match_Comparison'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Comparison(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_335 = \false; break; }
				$_335 = \true; break;
			}
			while(\false);
			if( $_335 === \false) {
				$result = $res_336;
				$this->pos = $pos_336;
				unset($res_336, $pos_336);
				break;
			}
		}
		$_337 = \true; break;
	}
	while(\false);
	if( $_337 === \true ) { return $this->finalise($result); }
	if( $_337 === \false) { return \false; }
}


/* Comparison: operands:Addition ( ( __ ops:ComparisonOperator __ | ops: ] ComparisonOperatorWithWhitespace ] ) operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison($stack = []) {
	$matchrule = 'Comparison'; $result = $this->construct($matchrule, $matchrule);
	$_358 = \null;
	do {
		$key = 'match_Addition'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Addition(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_358 = \false; break; }
		while (\true) {
			$res_357 = $result;
			$pos_357 = $this->pos;
			$_356 = \null;
			do {
				$_353 = \null;
				do {
					$_351 = \null;
					do {
						$res_340 = $result;
						$pos_340 = $this->pos;
						$_344 = \null;
						do {
							$key = 'match___'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_344 = \false; break; }
							$key = 'match_ComparisonOperator'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ComparisonOperator(\array_merge($stack, [$result])));
							if ($subres !== \false) {
								$this->store($result, $subres, "ops");
							}
							else { $_344 = \false; break; }
							$key = 'match___'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_344 = \false; break; }
							$_344 = \true; break;
						}
						while(\false);
						if( $_344 === \true ) { $_351 = \true; break; }
						$result = $res_340;
						$this->pos = $pos_340;
						$_349 = \null;
						do {
							if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
							else { $_349 = \false; break; }
							$key = 'match_ComparisonOperatorWithWhitespace'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ComparisonOperatorWithWhitespace(\array_merge($stack, [$result])));
							if ($subres !== \false) {
								$this->store($result, $subres, "ops");
							}
							else { $_349 = \false; break; }
							if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
							else { $_349 = \false; break; }
							$_349 = \true; break;
						}
						while(\false);
						if( $_349 === \true ) { $_351 = \true; break; }
						$result = $res_340;
						$this->pos = $pos_340;
						$_351 = \false; break;
					}
					while(\false);
					if( $_351 === \false) { $_353 = \false; break; }
					$_353 = \true; break;
				}
				while(\false);
				if( $_353 === \false) { $_356 = \false; break; }
				$key = 'match_Addition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Addition(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_356 = \false; break; }
				$_356 = \true; break;
			}
			while(\false);
			if( $_356 === \false) {
				$result = $res_357;
				$this->pos = $pos_357;
				unset($res_357, $pos_357);
				break;
			}
		}
		$_358 = \true; break;
	}
	while(\false);
	if( $_358 === \true ) { return $this->finalise($result); }
	if( $_358 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition($stack = []) {
	$matchrule = 'Addition'; $result = $this->construct($matchrule, $matchrule);
	$_367 = \null;
	do {
		$key = 'match_Multiplication'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Multiplication(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_367 = \false; break; }
		while (\true) {
			$res_366 = $result;
			$pos_366 = $this->pos;
			$_365 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_365 = \false; break; }
				$key = 'match_AddOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AddOperator(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_365 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_365 = \false; break; }
				$key = 'match_Multiplication'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Multiplication(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_365 = \false; break; }
				$_365 = \true; break;
			}
			while(\false);
			if( $_365 === \false) {
				$result = $res_366;
				$this->pos = $pos_366;
				unset($res_366, $pos_366);
				break;
			}
		}
		$_367 = \true; break;
	}
	while(\false);
	if( $_367 === \true ) { return $this->finalise($result); }
	if( $_367 === \false) { return \false; }
}


/* Multiplication: operands:Exponentiation ( __ ops:MultiplyOperator __ operands:Exponentiation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication($stack = []) {
	$matchrule = 'Multiplication'; $result = $this->construct($matchrule, $matchrule);
	$_376 = \null;
	do {
		$key = 'match_Exponentiation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Exponentiation(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_376 = \false; break; }
		while (\true) {
			$res_375 = $result;
			$pos_375 = $this->pos;
			$_374 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_374 = \false; break; }
				$key = 'match_MultiplyOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_MultiplyOperator(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_374 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_374 = \false; break; }
				$key = 'match_Exponentiation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Exponentiation(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_374 = \false; break; }
				$_374 = \true; break;
			}
			while(\false);
			if( $_374 === \false) {
				$result = $res_375;
				$this->pos = $pos_375;
				unset($res_375, $pos_375);
				break;
			}
		}
		$_376 = \true; break;
	}
	while(\false);
	if( $_376 === \true ) { return $this->finalise($result); }
	if( $_376 === \false) { return \false; }
}


/* Exponentiation: operands:Negation ( __ ops:PowerOperator __ operands:Negation )* */
protected $match_Exponentiation_typestack = ['Exponentiation'];
function match_Exponentiation($stack = []) {
	$matchrule = 'Exponentiation'; $result = $this->construct($matchrule, $matchrule);
	$_385 = \null;
	do {
		$key = 'match_Negation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Negation(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_385 = \false; break; }
		while (\true) {
			$res_384 = $result;
			$pos_384 = $this->pos;
			$_383 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_383 = \false; break; }
				$key = 'match_PowerOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_PowerOperator(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_383 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_383 = \false; break; }
				$key = 'match_Negation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Negation(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_383 = \false; break; }
				$_383 = \true; break;
			}
			while(\false);
			if( $_383 === \false) {
				$result = $res_384;
				$this->pos = $pos_384;
				unset($res_384, $pos_384);
				break;
			}
		}
		$_385 = \true; break;
	}
	while(\false);
	if( $_385 === \true ) { return $this->finalise($result); }
	if( $_385 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation($stack = []) {
	$matchrule = 'Negation'; $result = $this->construct($matchrule, $matchrule);
	$_391 = \null;
	do {
		while (\true) {
			$res_389 = $result;
			$pos_389 = $this->pos;
			$_388 = \null;
			do {
				$key = 'match_NegationOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_NegationOperator(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_388 = \false; break; }
				$_388 = \true; break;
			}
			while(\false);
			if( $_388 === \false) {
				$result = $res_389;
				$this->pos = $pos_389;
				unset($res_389, $pos_389);
				break;
			}
		}
		$key = 'match_Operand'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Operand(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_391 = \false; break; }
		$_391 = \true; break;
	}
	while(\false);
	if( $_391 === \true ) { return $this->finalise($result); }
	if( $_391 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:AbstractValue ) chain:Chain? ) | skip:AbstractValue */
protected $match_Operand_typestack = ['Operand'];
function match_Operand($stack = []) {
	$matchrule = 'Operand'; $result = $this->construct($matchrule, $matchrule);
	$_411 = \null;
	do {
		$res_393 = $result;
		$pos_393 = $this->pos;
		$_408 = \null;
		do {
			$_405 = \null;
			do {
				$_403 = \null;
				do {
					$res_394 = $result;
					$pos_394 = $this->pos;
					$_400 = \null;
					do {
						if (\substr($this->string, $this->pos, 1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_400 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_400 = \false; break; }
						$key = 'match_Expression'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_400 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_400 = \false; break; }
						if (\substr($this->string, $this->pos, 1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_400 = \false; break; }
						$_400 = \true; break;
					}
					while(\false);
					if( $_400 === \true ) { $_403 = \true; break; }
					$result = $res_394;
					$this->pos = $pos_394;
					$key = 'match_AbstractValue'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_AbstractValue(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_403 = \true; break;
					}
					$result = $res_394;
					$this->pos = $pos_394;
					$_403 = \false; break;
				}
				while(\false);
				if( $_403 === \false) { $_405 = \false; break; }
				$_405 = \true; break;
			}
			while(\false);
			if( $_405 === \false) { $_408 = \false; break; }
			$res_407 = $result;
			$pos_407 = $this->pos;
			$key = 'match_Chain'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Chain(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_407;
				$this->pos = $pos_407;
				unset($res_407, $pos_407);
			}
			$_408 = \true; break;
		}
		while(\false);
		if( $_408 === \true ) { $_411 = \true; break; }
		$result = $res_393;
		$this->pos = $pos_393;
		$key = 'match_AbstractValue'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AbstractValue(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_411 = \true; break;
		}
		$result = $res_393;
		$this->pos = $pos_393;
		$_411 = \false; break;
	}
	while(\false);
	if( $_411 === \true ) { return $this->finalise($result); }
	if( $_411 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList($stack = []) {
	$matchrule = 'ArgumentList'; $result = $this->construct($matchrule, $matchrule);
	$_420 = \null;
	do {
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_420 = \false; break; }
		while (\true) {
			$res_419 = $result;
			$pos_419 = $this->pos;
			$_418 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_418 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_418 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_418 = \false; break; }
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_418 = \false; break; }
				$_418 = \true; break;
			}
			while(\false);
			if( $_418 === \false) {
				$result = $res_419;
				$this->pos = $pos_419;
				unset($res_419, $pos_419);
				break;
			}
		}
		$_420 = \true; break;
	}
	while(\false);
	if( $_420 === \true ) { return $this->finalise($result); }
	if( $_420 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList($stack = []) {
	$matchrule = 'FunctionDefinitionArgumentList'; $result = $this->construct($matchrule, $matchrule);
	$_429 = \null;
	do {
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_429 = \false; break; }
		while (\true) {
			$res_428 = $result;
			$pos_428 = $this->pos;
			$_427 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_427 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_427 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_427 = \false; break; }
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_427 = \false; break; }
				$_427 = \true; break;
			}
			while(\false);
			if( $_427 === \false) {
				$result = $res_428;
				$this->pos = $pos_428;
				unset($res_428, $pos_428);
				break;
			}
		}
		$_429 = \true; break;
	}
	while(\false);
	if( $_429 === \true ) { return $this->finalise($result); }
	if( $_429 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition($stack = []) {
	$matchrule = 'FunctionDefinition'; $result = $this->construct($matchrule, $matchrule);
	$_442 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_442 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_442 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_442 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_442 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_442 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_442 = \false; break; }
		$res_437 = $result;
		$pos_437 = $this->pos;
		$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_437;
			$this->pos = $pos_437;
			unset($res_437, $pos_437);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_442 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_442 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_442 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_442 = \false; break; }
		$_442 = \true; break;
	}
	while(\false);
	if( $_442 === \true ) { return $this->finalise($result); }
	if( $_442 === \false) { return \false; }
}


/* ImportStatement:
	( "from" ] ( module: ( VariableName ("." VariableName)* ) ) ] "import" ] ( symbol: VariableName ) )
	| ( "import" ] ( module: ( VariableName ("." VariableName)* ) ) ) */
protected $match_ImportStatement_typestack = ['ImportStatement'];
function match_ImportStatement($stack = []) {
	$matchrule = 'ImportStatement'; $result = $this->construct($matchrule, $matchrule);
	$_477 = \null;
	do {
		$res_444 = $result;
		$pos_444 = $this->pos;
		$_462 = \null;
		do {
			if (($subres = $this->literal('from')) !== \false) { $result["text"] .= $subres; }
			else { $_462 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_462 = \false; break; }
			$_454 = \null;
			do {
				$stack[] = $result; $result = $this->construct( $matchrule, "module" );
				$_452 = \null;
				do {
					$key = 'match_VariableName'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_452 = \false; break; }
					while (\true) {
						$res_451 = $result;
						$pos_451 = $this->pos;
						$_450 = \null;
						do {
							if (\substr($this->string, $this->pos, 1) === '.') {
								$this->pos += 1;
								$result["text"] .= '.';
							}
							else { $_450 = \false; break; }
							$key = 'match_VariableName'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_450 = \false; break; }
							$_450 = \true; break;
						}
						while(\false);
						if( $_450 === \false) {
							$result = $res_451;
							$this->pos = $pos_451;
							unset($res_451, $pos_451);
							break;
						}
					}
					$_452 = \true; break;
				}
				while(\false);
				if( $_452 === \true ) {
					$subres = $result; $result = \array_pop($stack);
					$this->store( $result, $subres, 'module' );
				}
				if( $_452 === \false) {
					$result = \array_pop($stack);
					$_454 = \false; break;
				}
				$_454 = \true; break;
			}
			while(\false);
			if( $_454 === \false) { $_462 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_462 = \false; break; }
			if (($subres = $this->literal('import')) !== \false) { $result["text"] .= $subres; }
			else { $_462 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_462 = \false; break; }
			$_460 = \null;
			do {
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "symbol");
				}
				else { $_460 = \false; break; }
				$_460 = \true; break;
			}
			while(\false);
			if( $_460 === \false) { $_462 = \false; break; }
			$_462 = \true; break;
		}
		while(\false);
		if( $_462 === \true ) { $_477 = \true; break; }
		$result = $res_444;
		$this->pos = $pos_444;
		$_475 = \null;
		do {
			if (($subres = $this->literal('import')) !== \false) { $result["text"] .= $subres; }
			else { $_475 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_475 = \false; break; }
			$_473 = \null;
			do {
				$stack[] = $result; $result = $this->construct( $matchrule, "module" );
				$_471 = \null;
				do {
					$key = 'match_VariableName'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_471 = \false; break; }
					while (\true) {
						$res_470 = $result;
						$pos_470 = $this->pos;
						$_469 = \null;
						do {
							if (\substr($this->string, $this->pos, 1) === '.') {
								$this->pos += 1;
								$result["text"] .= '.';
							}
							else { $_469 = \false; break; }
							$key = 'match_VariableName'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_469 = \false; break; }
							$_469 = \true; break;
						}
						while(\false);
						if( $_469 === \false) {
							$result = $res_470;
							$this->pos = $pos_470;
							unset($res_470, $pos_470);
							break;
						}
					}
					$_471 = \true; break;
				}
				while(\false);
				if( $_471 === \true ) {
					$subres = $result; $result = \array_pop($stack);
					$this->store( $result, $subres, 'module' );
				}
				if( $_471 === \false) {
					$result = \array_pop($stack);
					$_473 = \false; break;
				}
				$_473 = \true; break;
			}
			while(\false);
			if( $_473 === \false) { $_475 = \false; break; }
			$_475 = \true; break;
		}
		while(\false);
		if( $_475 === \true ) { $_477 = \true; break; }
		$result = $res_444;
		$this->pos = $pos_444;
		$_477 = \false; break;
	}
	while(\false);
	if( $_477 === \true ) { return $this->finalise($result); }
	if( $_477 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement($stack = []) {
	$matchrule = 'IfStatement'; $result = $this->construct($matchrule, $matchrule);
	$_498 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_498 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_498 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_498 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_498 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_498 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_498 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_498 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_498 = \false; break; }
		$_488 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_488 = \false; break; }
			$_488 = \true; break;
		}
		while(\false);
		if( $_488 === \false) { $_498 = \false; break; }
		$res_497 = $result;
		$pos_497 = $this->pos;
		$_496 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_496 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_496 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_496 = \false; break; }
			$_494 = \null;
			do {
				$key = 'match_Block'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_494 = \false; break; }
				$_494 = \true; break;
			}
			while(\false);
			if( $_494 === \false) { $_496 = \false; break; }
			$_496 = \true; break;
		}
		while(\false);
		if( $_496 === \false) {
			$result = $res_497;
			$this->pos = $pos_497;
			unset($res_497, $pos_497);
		}
		$_498 = \true; break;
	}
	while(\false);
	if( $_498 === \true ) { return $this->finalise($result); }
	if( $_498 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ ( key:VariableName __ ":" __ )? item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement($stack = []) {
	$matchrule = 'ForStatement'; $result = $this->construct($matchrule, $matchrule);
	$_521 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_521 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_521 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_521 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_521 = \false; break; }
		$res_509 = $result;
		$pos_509 = $this->pos;
		$_508 = \null;
		do {
			$key = 'match_VariableName'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "key");
			}
			else { $_508 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_508 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_508 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_508 = \false; break; }
			$_508 = \true; break;
		}
		while(\false);
		if( $_508 === \false) {
			$result = $res_509;
			$this->pos = $pos_509;
			unset($res_509, $pos_509);
		}
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_521 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_521 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_521 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_521 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_521 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_521 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_521 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_521 = \false; break; }
		$_519 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_519 = \false; break; }
			$_519 = \true; break;
		}
		while(\false);
		if( $_519 === \false) { $_521 = \false; break; }
		$_521 = \true; break;
	}
	while(\false);
	if( $_521 === \true ) { return $this->finalise($result); }
	if( $_521 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement($stack = []) {
	$matchrule = 'WhileStatement'; $result = $this->construct($matchrule, $matchrule);
	$_534 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_534 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_534 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_534 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_534 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_534 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_534 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_534 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_534 = \false; break; }
		$_532 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_532 = \false; break; }
			$_532 = \true; break;
		}
		while(\false);
		if( $_532 === \false) { $_534 = \false; break; }
		$_534 = \true; break;
	}
	while(\false);
	if( $_534 === \true ) { return $this->finalise($result); }
	if( $_534 === \false) { return \false; }
}


/* TryStatement: "try" __ main:Block __ "catch" __ onerror:Block */
protected $match_TryStatement_typestack = ['TryStatement'];
function match_TryStatement($stack = []) {
	$matchrule = 'TryStatement'; $result = $this->construct($matchrule, $matchrule);
	$_543 = \null;
	do {
		if (($subres = $this->literal('try')) !== \false) { $result["text"] .= $subres; }
		else { $_543 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_543 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "main");
		}
		else { $_543 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_543 = \false; break; }
		if (($subres = $this->literal('catch')) !== \false) { $result["text"] .= $subres; }
		else { $_543 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_543 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres, "onerror");
		}
		else { $_543 = \false; break; }
		$_543 = \true; break;
	}
	while(\false);
	if( $_543 === \true ) { return $this->finalise($result); }
	if( $_543 === \false) { return \false; }
}


/* CommandStatements: &/[rbc]/ ( skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement ) */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements($stack = []) {
	$matchrule = 'CommandStatements'; $result = $this->construct($matchrule, $matchrule);
	$_557 = \null;
	do {
		$res_545 = $result;
		$pos_545 = $this->pos;
		if (($subres = $this->rx('/[rbc]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_545;
			$this->pos = $pos_545;
		}
		else {
			$result = $res_545;
			$this->pos = $pos_545;
			$_557 = \false; break;
		}
		$_555 = \null;
		do {
			$_553 = \null;
			do {
				$res_546 = $result;
				$pos_546 = $this->pos;
				$key = 'match_ReturnStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ReturnStatement(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_553 = \true; break;
				}
				$result = $res_546;
				$this->pos = $pos_546;
				$_551 = \null;
				do {
					$res_548 = $result;
					$pos_548 = $this->pos;
					$key = 'match_BreakStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_BreakStatement(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_551 = \true; break;
					}
					$result = $res_548;
					$this->pos = $pos_548;
					$key = 'match_ContinueStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ContinueStatement(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_551 = \true; break;
					}
					$result = $res_548;
					$this->pos = $pos_548;
					$_551 = \false; break;
				}
				while(\false);
				if( $_551 === \true ) { $_553 = \true; break; }
				$result = $res_546;
				$this->pos = $pos_546;
				$_553 = \false; break;
			}
			while(\false);
			if( $_553 === \false) { $_555 = \false; break; }
			$_555 = \true; break;
		}
		while(\false);
		if( $_555 === \false) { $_557 = \false; break; }
		$_557 = \true; break;
	}
	while(\false);
	if( $_557 === \true ) { return $this->finalise($result); }
	if( $_557 === \false) { return \false; }
}


/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement($stack = []) {
	$matchrule = 'ReturnStatement'; $result = $this->construct($matchrule, $matchrule);
	$_571 = \null;
	do {
		$res_559 = $result;
		$pos_559 = $this->pos;
		$_565 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_565 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_565 = \false; break; }
			$res_564 = $result;
			$pos_564 = $this->pos;
			$_563 = \null;
			do {
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_563 = \false; break; }
				$_563 = \true; break;
			}
			while(\false);
			if( $_563 === \false) {
				$result = $res_564;
				$this->pos = $pos_564;
				unset($res_564, $pos_564);
			}
			$_565 = \true; break;
		}
		while(\false);
		if( $_565 === \true ) { $_571 = \true; break; }
		$result = $res_559;
		$this->pos = $pos_559;
		$_569 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_569 = \false; break; }
			$res_568 = $result;
			$pos_568 = $this->pos;
			$key = 'match_SEP'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_SEP(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_568;
				$this->pos = $pos_568;
			}
			else {
				$result = $res_568;
				$this->pos = $pos_568;
				$_569 = \false; break;
			}
			$_569 = \true; break;
		}
		while(\false);
		if( $_569 === \true ) { $_571 = \true; break; }
		$result = $res_559;
		$this->pos = $pos_559;
		$_571 = \false; break;
	}
	while(\false);
	if( $_571 === \true ) { return $this->finalise($result); }
	if( $_571 === \false) { return \false; }
}


/* BreakStatement: "break" */
protected $match_BreakStatement_typestack = ['BreakStatement'];
function match_BreakStatement($stack = []) {
	$matchrule = 'BreakStatement'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('break')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ContinueStatement: "continue" */
protected $match_ContinueStatement_typestack = ['ContinueStatement'];
function match_ContinueStatement($stack = []) {
	$matchrule = 'ContinueStatement'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('continue')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BlockStatements: &/[iwft]/ ( skip:IfStatement | skip:WhileStatement | skip:FunctionDefinition | skip:ForStatement | skip:TryStatement | skip:ImportStatement) */
protected $match_BlockStatements_typestack = ['BlockStatements'];
function match_BlockStatements($stack = []) {
	$matchrule = 'BlockStatements'; $result = $this->construct($matchrule, $matchrule);
	$_599 = \null;
	do {
		$res_575 = $result;
		$pos_575 = $this->pos;
		if (($subres = $this->rx('/[iwft]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_575;
			$this->pos = $pos_575;
		}
		else {
			$result = $res_575;
			$this->pos = $pos_575;
			$_599 = \false; break;
		}
		$_597 = \null;
		do {
			$_595 = \null;
			do {
				$res_576 = $result;
				$pos_576 = $this->pos;
				$key = 'match_IfStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_IfStatement(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_595 = \true; break;
				}
				$result = $res_576;
				$this->pos = $pos_576;
				$_593 = \null;
				do {
					$res_578 = $result;
					$pos_578 = $this->pos;
					$key = 'match_WhileStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_WhileStatement(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_593 = \true; break;
					}
					$result = $res_578;
					$this->pos = $pos_578;
					$_591 = \null;
					do {
						$res_580 = $result;
						$pos_580 = $this->pos;
						$key = 'match_FunctionDefinition'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_FunctionDefinition(\array_merge($stack, [$result])));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_591 = \true; break;
						}
						$result = $res_580;
						$this->pos = $pos_580;
						$_589 = \null;
						do {
							$res_582 = $result;
							$pos_582 = $this->pos;
							$key = 'match_ForStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ForStatement(\array_merge($stack, [$result])));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_589 = \true; break;
							}
							$result = $res_582;
							$this->pos = $pos_582;
							$_587 = \null;
							do {
								$res_584 = $result;
								$pos_584 = $this->pos;
								$key = 'match_TryStatement'; $pos = $this->pos;
								$subres = $this->packhas($key, $pos)
									? $this->packread($key, $pos)
									: $this->packwrite($key, $pos, $this->match_TryStatement(\array_merge($stack, [$result])));
								if ($subres !== \false) {
									$this->store($result, $subres, "skip");
									$_587 = \true; break;
								}
								$result = $res_584;
								$this->pos = $pos_584;
								$key = 'match_ImportStatement'; $pos = $this->pos;
								$subres = $this->packhas($key, $pos)
									? $this->packread($key, $pos)
									: $this->packwrite($key, $pos, $this->match_ImportStatement(\array_merge($stack, [$result])));
								if ($subres !== \false) {
									$this->store($result, $subres, "skip");
									$_587 = \true; break;
								}
								$result = $res_584;
								$this->pos = $pos_584;
								$_587 = \false; break;
							}
							while(\false);
							if( $_587 === \true ) { $_589 = \true; break; }
							$result = $res_582;
							$this->pos = $pos_582;
							$_589 = \false; break;
						}
						while(\false);
						if( $_589 === \true ) { $_591 = \true; break; }
						$result = $res_580;
						$this->pos = $pos_580;
						$_591 = \false; break;
					}
					while(\false);
					if( $_591 === \true ) { $_593 = \true; break; }
					$result = $res_578;
					$this->pos = $pos_578;
					$_593 = \false; break;
				}
				while(\false);
				if( $_593 === \true ) { $_595 = \true; break; }
				$result = $res_576;
				$this->pos = $pos_576;
				$_595 = \false; break;
			}
			while(\false);
			if( $_595 === \false) { $_597 = \false; break; }
			$_597 = \true; break;
		}
		while(\false);
		if( $_597 === \false) { $_599 = \false; break; }
		$_599 = \true; break;
	}
	while(\false);
	if( $_599 === \true ) { return $this->finalise($result); }
	if( $_599 === \false) { return \false; }
}


/* Statement: !/[\s\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement($stack = []) {
	$matchrule = 'Statement'; $result = $this->construct($matchrule, $matchrule);
	$_613 = \null;
	do {
		$res_601 = $result;
		$pos_601 = $this->pos;
		if (($subres = $this->rx('/[\s\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_601;
			$this->pos = $pos_601;
			$_613 = \false; break;
		}
		else {
			$result = $res_601;
			$this->pos = $pos_601;
		}
		$_611 = \null;
		do {
			$_609 = \null;
			do {
				$res_602 = $result;
				$pos_602 = $this->pos;
				$key = 'match_BlockStatements'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BlockStatements(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_609 = \true; break;
				}
				$result = $res_602;
				$this->pos = $pos_602;
				$_607 = \null;
				do {
					$res_604 = $result;
					$pos_604 = $this->pos;
					$key = 'match_CommandStatements'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_CommandStatements(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_607 = \true; break;
					}
					$result = $res_604;
					$this->pos = $pos_604;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression(\array_merge($stack, [$result])));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_607 = \true; break;
					}
					$result = $res_604;
					$this->pos = $pos_604;
					$_607 = \false; break;
				}
				while(\false);
				if( $_607 === \true ) { $_609 = \true; break; }
				$result = $res_602;
				$this->pos = $pos_602;
				$_609 = \false; break;
			}
			while(\false);
			if( $_609 === \false) { $_611 = \false; break; }
			$_611 = \true; break;
		}
		while(\false);
		if( $_611 === \false) { $_613 = \false; break; }
		$_613 = \true; break;
	}
	while(\false);
	if( $_613 === \true ) { return $this->finalise($result); }
	if( $_613 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block($stack = []) {
	$matchrule = 'Block'; $result = $this->construct($matchrule, $matchrule);
	$_621 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_621 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_621 = \false; break; }
		$res_619 = $result;
		$pos_619 = $this->pos;
		$_618 = \null;
		do {
			$key = 'match_Program'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Program(\array_merge($stack, [$result])));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_618 = \false; break; }
			$_618 = \true; break;
		}
		while(\false);
		if( $_618 === \false) {
			$result = $res_619;
			$this->pos = $pos_619;
			unset($res_619, $pos_619);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_621 = \false; break; }
		$_621 = \true; break;
	}
	while(\false);
	if( $_621 === \true ) { return $this->finalise($result); }
	if( $_621 === \false) { return \false; }
}


/* __: / [\s]*+(?:\/\/[^\n]*+(?:\s*+))? / */
protected $match____typestack = ['__'];
function match___($stack = []) {
	$matchrule = '__'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ [\s]*+(?:\/\/[^\n]*+(?:\s*+))? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = ['NL'];
function match_NL($stack = []) {
	$matchrule = 'NL'; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ (?:\/\/[^\n]*)?\n /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* SEP: ";" | NL */
protected $match_SEP_typestack = ['SEP'];
function match_SEP($stack = []) {
	$matchrule = 'SEP'; $result = $this->construct($matchrule, $matchrule);
	$_628 = \null;
	do {
		$res_625 = $result;
		$pos_625 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_628 = \true; break;
		}
		$result = $res_625;
		$this->pos = $pos_625;
		$key = 'match_NL'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NL(\array_merge($stack, [$result])));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_628 = \true; break;
		}
		$result = $res_625;
		$this->pos = $pos_625;
		$_628 = \false; break;
	}
	while(\false);
	if( $_628 === \true ) { return $this->finalise($result); }
	if( $_628 === \false) { return \false; }
}


/* Program: ( __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program($stack = []) {
	$matchrule = 'Program'; $result = $this->construct($matchrule, $matchrule);
	$_637 = \null;
	do {
		while (\true) {
			$res_635 = $result;
			$pos_635 = $this->pos;
			$_634 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_634 = \false; break; }
				$res_631 = $result;
				$pos_631 = $this->pos;
				$key = 'match_Statement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Statement(\array_merge($stack, [$result])));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_631;
					$this->pos = $pos_631;
					unset($res_631, $pos_631);
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$key = 'match_SEP'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_SEP(\array_merge($stack, [$result])));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_634 = \false; break; }
				$_634 = \true; break;
			}
			while(\false);
			if( $_634 === \false) {
				$result = $res_635;
				$this->pos = $pos_635;
				unset($res_635, $pos_635);
				break;
			}
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(\array_merge($stack, [$result])));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_637 = \false; break; }
		$_637 = \true; break;
	}
	while(\false);
	if( $_637 === \true ) { return $this->finalise($result); }
	if( $_637 === \false) { return \false; }
}




}
