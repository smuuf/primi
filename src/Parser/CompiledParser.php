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
		'function', 'break', 'continue', 'while', 'try', 'catch', 'not', 'in'
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
function match_StringInside ($stack = []) {
	$matchrule = "StringInside"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
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
function match_StringLiteral ($stack = []) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
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
			: $this->packwrite($key, $pos, $this->match_StringInside(array_merge($stack, array($result))));
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
function match_FStringExpr ($stack = []) {
	$matchrule = "FStringExpr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$key = 'match_Variable'; $pos = $this->pos;
	$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->match_Variable(array_merge($stack, array($result))));
	if ($subres !== \false) {
		$this->store($result, $subres, "core");
		return $this->finalise($result);
	}
	else { return \false; }
}


/* FStringTxt: ( / (\\.)+ / | "{{" | "}}" | / [^\{\}{$quote}] / )* */
protected $match_FStringTxt_typestack = ['FStringTxt'];
function match_FStringTxt ($stack = []) {
	$matchrule = "FStringTxt"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
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
function match_FStringInside ($stack = []) {
	$matchrule = "FStringInside"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_35 = \null;
	do {
		$key = 'match_FStringTxt'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FStringTxt(array_merge($stack, array($result))));
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
					: $this->packwrite($key, $pos, $this->match_FStringExpr(array_merge($stack, array($result))));
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
					: $this->packwrite($key, $pos, $this->match_FStringTxt(array_merge($stack, array($result))));
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
function match_FStringLiteral ($stack = []) {
	$matchrule = "FStringLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
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
			: $this->packwrite($key, $pos, $this->match_FStringInside(array_merge($stack, array($result))));
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
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ -?\d[\d_]*(\.[\d_]+)? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: ( "true" | "false" ) !VariableName */
protected $match_BoolLiteral_typestack = ['BoolLiteral'];
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_52 = \null;
	do {
		$_49 = \null;
		do {
			$_47 = \null;
			do {
				$res_44 = $result;
				$pos_44 = $this->pos;
				if (($subres = $this->literal('true')) !== \false) {
					$result["text"] .= $subres;
					$_47 = \true; break;
				}
				$result = $res_44;
				$this->pos = $pos_44;
				if (($subres = $this->literal('false')) !== \false) {
					$result["text"] .= $subres;
					$_47 = \true; break;
				}
				$result = $res_44;
				$this->pos = $pos_44;
				$_47 = \false; break;
			}
			while(\false);
			if( $_47 === \false) { $_49 = \false; break; }
			$_49 = \true; break;
		}
		while(\false);
		if( $_49 === \false) { $_52 = \false; break; }
		$res_51 = $result;
		$pos_51 = $this->pos;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$result = $res_51;
			$this->pos = $pos_51;
			$_52 = \false; break;
		}
		else {
			$result = $res_51;
			$this->pos = $pos_51;
		}
		$_52 = \true; break;
	}
	while(\false);
	if( $_52 === \true ) { return $this->finalise($result); }
	if( $_52 === \false) { return \false; }
}


/* NullLiteral: "null" !VariableName */
protected $match_NullLiteral_typestack = ['NullLiteral'];
function match_NullLiteral ($stack = []) {
	$matchrule = "NullLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_56 = \null;
	do {
		if (($subres = $this->literal('null')) !== \false) { $result["text"] .= $subres; }
		else { $_56 = \false; break; }
		$res_55 = $result;
		$pos_55 = $this->pos;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$result = $res_55;
			$this->pos = $pos_55;
			$_56 = \false; break;
		}
		else {
			$result = $res_55;
			$this->pos = $pos_55;
		}
		$_56 = \true; break;
	}
	while(\false);
	if( $_56 === \true ) { return $this->finalise($result); }
	if( $_56 === \false) { return \false; }
}


/* RegexLiteral: "rx" core:StringLiteral */
protected $match_RegexLiteral_typestack = ['RegexLiteral'];
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_60 = \null;
	do {
		if (($subres = $this->literal('rx')) !== \false) { $result["text"] .= $subres; }
		else { $_60 = \false; break; }
		$key = 'match_StringLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_StringLiteral(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_60 = \false; break; }
		$_60 = \true; break;
	}
	while(\false);
	if( $_60 === \true ) { return $this->finalise($result); }
	if( $_60 === \false) { return \false; }
}


/* Nothing: "" */
protected $match_Nothing_typestack = ['Nothing'];
function match_Nothing ($stack = []) {
	$matchrule = "Nothing"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Literal: &/["']/ skip:StringLiteral | skip:NumberLiteral | skip:BoolLiteral | ( &"n" skip:NullLiteral ) | ( &"rx" skip:RegexLiteral ) | ( &"f" skip:FStringLiteral ) */
protected $match_Literal_typestack = ['Literal'];
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_94 = \null;
	do {
		$res_63 = $result;
		$pos_63 = $this->pos;
		$_66 = \null;
		do {
			$res_64 = $result;
			$pos_64 = $this->pos;
			if (($subres = $this->rx('/["\']/')) !== \false) {
				$result["text"] .= $subres;
				$result = $res_64;
				$this->pos = $pos_64;
			}
			else {
				$result = $res_64;
				$this->pos = $pos_64;
				$_66 = \false; break;
			}
			$key = 'match_StringLiteral'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_StringLiteral(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_66 = \false; break; }
			$_66 = \true; break;
		}
		while(\false);
		if( $_66 === \true ) { $_94 = \true; break; }
		$result = $res_63;
		$this->pos = $pos_63;
		$_92 = \null;
		do {
			$res_68 = $result;
			$pos_68 = $this->pos;
			$key = 'match_NumberLiteral'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_NumberLiteral(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_92 = \true; break;
			}
			$result = $res_68;
			$this->pos = $pos_68;
			$_90 = \null;
			do {
				$res_70 = $result;
				$pos_70 = $this->pos;
				$key = 'match_BoolLiteral'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BoolLiteral(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_90 = \true; break;
				}
				$result = $res_70;
				$this->pos = $pos_70;
				$_88 = \null;
				do {
					$res_72 = $result;
					$pos_72 = $this->pos;
					$_75 = \null;
					do {
						$res_73 = $result;
						$pos_73 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === 'n') {
							$this->pos += 1;
							$result["text"] .= 'n';
							$result = $res_73;
							$this->pos = $pos_73;
						}
						else {
							$result = $res_73;
							$this->pos = $pos_73;
							$_75 = \false; break;
						}
						$key = 'match_NullLiteral'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_NullLiteral(array_merge($stack, array($result))));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
						}
						else { $_75 = \false; break; }
						$_75 = \true; break;
					}
					while(\false);
					if( $_75 === \true ) { $_88 = \true; break; }
					$result = $res_72;
					$this->pos = $pos_72;
					$_86 = \null;
					do {
						$res_77 = $result;
						$pos_77 = $this->pos;
						$_80 = \null;
						do {
							$res_78 = $result;
							$pos_78 = $this->pos;
							if (($subres = $this->literal('rx')) !== \false) {
								$result["text"] .= $subres;
								$result = $res_78;
								$this->pos = $pos_78;
							}
							else {
								$result = $res_78;
								$this->pos = $pos_78;
								$_80 = \false; break;
							}
							$key = 'match_RegexLiteral'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_RegexLiteral(array_merge($stack, array($result))));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
							}
							else { $_80 = \false; break; }
							$_80 = \true; break;
						}
						while(\false);
						if( $_80 === \true ) { $_86 = \true; break; }
						$result = $res_77;
						$this->pos = $pos_77;
						$_84 = \null;
						do {
							$res_82 = $result;
							$pos_82 = $this->pos;
							if (\substr($this->string, $this->pos, 1) === 'f') {
								$this->pos += 1;
								$result["text"] .= 'f';
								$result = $res_82;
								$this->pos = $pos_82;
							}
							else {
								$result = $res_82;
								$this->pos = $pos_82;
								$_84 = \false; break;
							}
							$key = 'match_FStringLiteral'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_FStringLiteral(array_merge($stack, array($result))));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
							}
							else { $_84 = \false; break; }
							$_84 = \true; break;
						}
						while(\false);
						if( $_84 === \true ) { $_86 = \true; break; }
						$result = $res_77;
						$this->pos = $pos_77;
						$_86 = \false; break;
					}
					while(\false);
					if( $_86 === \true ) { $_88 = \true; break; }
					$result = $res_72;
					$this->pos = $pos_72;
					$_88 = \false; break;
				}
				while(\false);
				if( $_88 === \true ) { $_90 = \true; break; }
				$result = $res_70;
				$this->pos = $pos_70;
				$_90 = \false; break;
			}
			while(\false);
			if( $_90 === \true ) { $_92 = \true; break; }
			$result = $res_68;
			$this->pos = $pos_68;
			$_92 = \false; break;
		}
		while(\false);
		if( $_92 === \true ) { $_94 = \true; break; }
		$result = $res_63;
		$this->pos = $pos_63;
		$_94 = \false; break;
	}
	while(\false);
	if( $_94 === \true ) { return $this->finalise($result); }
	if( $_94 === \false) { return \false; }
}


/* VariableName: / (?:[a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = ['VariableName'];
function match_VariableName ($stack = []) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ (?:[a-zA-Z_][a-zA-Z0-9_]*) /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Variable: core:VariableName */
protected $match_Variable_typestack = ['Variable'];
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$key = 'match_VariableName'; $pos = $this->pos;
	$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
	if ($subres !== \false) {
		$this->store($result, $subres, "core");
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AnonymousFunction: "function" __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ params:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = ['AnonymousFunction'];
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_121 = \null;
	do {
		$res_98 = $result;
		$pos_98 = $this->pos;
		$_108 = \null;
		do {
			if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
			else { $_108 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_108 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_108 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_108 = \false; break; }
			$res_103 = $result;
			$pos_103 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList(array_merge($stack, array($result))));
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
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_108 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_108 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_108 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_108 = \false; break; }
			$_108 = \true; break;
		}
		while(\false);
		if( $_108 === \true ) { $_121 = \true; break; }
		$result = $res_98;
		$this->pos = $pos_98;
		$_119 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_119 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_119 = \false; break; }
			$res_112 = $result;
			$pos_112 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_112;
				$this->pos = $pos_112;
				unset($res_112, $pos_112);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_119 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_119 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_119 = \false; break; }
			if (($subres = $this->literal('=>')) !== \false) { $result["text"] .= $subres; }
			else { $_119 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_119 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_119 = \false; break; }
			$_119 = \true; break;
		}
		while(\false);
		if( $_119 === \true ) { $_121 = \true; break; }
		$result = $res_98;
		$this->pos = $pos_98;
		$_121 = \false; break;
	}
	while(\false);
	if( $_121 === \true ) { return $this->finalise($result); }
	if( $_121 === \false) { return \false; }
}


/* DictItem: __ key:Expression __ ":" __ value:Expression __ */
protected $match_DictItem_typestack = ['DictItem'];
function match_DictItem ($stack = []) {
	$matchrule = "DictItem"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_130 = \null;
	do {
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_130 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_130 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_130 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_130 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_130 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "value");
		}
		else { $_130 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_130 = \false; break; }
		$_130 = \true; break;
	}
	while(\false);
	if( $_130 === \true ) { return $this->finalise($result); }
	if( $_130 === \false) { return \false; }
}


/* DictDefinition: "{" __ ( items:DictItem ( __ "," __ items:DictItem )* )? __ ( "," __ )? "}" */
protected $match_DictDefinition_typestack = ['DictDefinition'];
function match_DictDefinition ($stack = []) {
	$matchrule = "DictDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_149 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_149 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_149 = \false; break; }
		$res_142 = $result;
		$pos_142 = $this->pos;
		$_141 = \null;
		do {
			$key = 'match_DictItem'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_DictItem(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_141 = \false; break; }
			while (\true) {
				$res_140 = $result;
				$pos_140 = $this->pos;
				$_139 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_139 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_139 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_139 = \false; break; }
					$key = 'match_DictItem'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_DictItem(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_139 = \false; break; }
					$_139 = \true; break;
				}
				while(\false);
				if( $_139 === \false) {
					$result = $res_140;
					$this->pos = $pos_140;
					unset($res_140, $pos_140);
					break;
				}
			}
			$_141 = \true; break;
		}
		while(\false);
		if( $_141 === \false) {
			$result = $res_142;
			$this->pos = $pos_142;
			unset($res_142, $pos_142);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_149 = \false; break; }
		$res_147 = $result;
		$pos_147 = $this->pos;
		$_146 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_146 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_146 = \false; break; }
			$_146 = \true; break;
		}
		while(\false);
		if( $_146 === \false) {
			$result = $res_147;
			$this->pos = $pos_147;
			unset($res_147, $pos_147);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_149 = \false; break; }
		$_149 = \true; break;
	}
	while(\false);
	if( $_149 === \true ) { return $this->finalise($result); }
	if( $_149 === \false) { return \false; }
}


/* ListDefinition: "[" __ ( items:Expression ( __ "," __ items:Expression )* )? __ ( "," __ )? "]" */
protected $match_ListDefinition_typestack = ['ListDefinition'];
function match_ListDefinition ($stack = []) {
	$matchrule = "ListDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_168 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_168 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_168 = \false; break; }
		$res_161 = $result;
		$pos_161 = $this->pos;
		$_160 = \null;
		do {
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_160 = \false; break; }
			while (\true) {
				$res_159 = $result;
				$pos_159 = $this->pos;
				$_158 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_158 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_158 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_158 = \false; break; }
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_158 = \false; break; }
					$_158 = \true; break;
				}
				while(\false);
				if( $_158 === \false) {
					$result = $res_159;
					$this->pos = $pos_159;
					unset($res_159, $pos_159);
					break;
				}
			}
			$_160 = \true; break;
		}
		while(\false);
		if( $_160 === \false) {
			$result = $res_161;
			$this->pos = $pos_161;
			unset($res_161, $pos_161);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_168 = \false; break; }
		$res_166 = $result;
		$pos_166 = $this->pos;
		$_165 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_165 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_165 = \false; break; }
			$_165 = \true; break;
		}
		while(\false);
		if( $_165 === \false) {
			$result = $res_166;
			$this->pos = $pos_166;
			unset($res_166, $pos_166);
		}
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_168 = \false; break; }
		$_168 = \true; break;
	}
	while(\false);
	if( $_168 === \true ) { return $this->finalise($result); }
	if( $_168 === \false) { return \false; }
}


/* AbstractValue: skip:Literal | skip:Variable | skip:ListDefinition | skip:DictDefinition */
protected $match_AbstractValue_typestack = ['AbstractValue'];
function match_AbstractValue ($stack = []) {
	$matchrule = "AbstractValue"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_181 = \null;
	do {
		$res_170 = $result;
		$pos_170 = $this->pos;
		$key = 'match_Literal'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Literal(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_181 = \true; break;
		}
		$result = $res_170;
		$this->pos = $pos_170;
		$_179 = \null;
		do {
			$res_172 = $result;
			$pos_172 = $this->pos;
			$key = 'match_Variable'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Variable(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_179 = \true; break;
			}
			$result = $res_172;
			$this->pos = $pos_172;
			$_177 = \null;
			do {
				$res_174 = $result;
				$pos_174 = $this->pos;
				$key = 'match_ListDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ListDefinition(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_177 = \true; break;
				}
				$result = $res_174;
				$this->pos = $pos_174;
				$key = 'match_DictDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_DictDefinition(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_177 = \true; break;
				}
				$result = $res_174;
				$this->pos = $pos_174;
				$_177 = \false; break;
			}
			while(\false);
			if( $_177 === \true ) { $_179 = \true; break; }
			$result = $res_172;
			$this->pos = $pos_172;
			$_179 = \false; break;
		}
		while(\false);
		if( $_179 === \true ) { $_181 = \true; break; }
		$result = $res_170;
		$this->pos = $pos_170;
		$_181 = \false; break;
	}
	while(\false);
	if( $_181 === \true ) { return $this->finalise($result); }
	if( $_181 === \false) { return \false; }
}


/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = ['VariableVector'];
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_185 = \null;
	do {
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_185 = \false; break; }
		$key = 'match_Vector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Vector(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else { $_185 = \false; break; }
		$_185 = \true; break;
	}
	while(\false);
	if( $_185 === \true ) { return $this->finalise($result); }
	if( $_185 === \false) { return \false; }
}


/* Vector: ( "[" __ ( index:Expression | index:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = ['Vector'];
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_201 = \null;
	do {
		$_198 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_198 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_198 = \false; break; }
			$_194 = \null;
			do {
				$_192 = \null;
				do {
					$res_189 = $result;
					$pos_189 = $this->pos;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "index");
						$_192 = \true; break;
					}
					$result = $res_189;
					$this->pos = $pos_189;
					$key = 'match_Nothing'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Nothing(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "index");
						$_192 = \true; break;
					}
					$result = $res_189;
					$this->pos = $pos_189;
					$_192 = \false; break;
				}
				while(\false);
				if( $_192 === \false) { $_194 = \false; break; }
				$_194 = \true; break;
			}
			while(\false);
			if( $_194 === \false) { $_198 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_198 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_198 = \false; break; }
			$_198 = \true; break;
		}
		while(\false);
		if( $_198 === \false) { $_201 = \false; break; }
		$res_200 = $result;
		$pos_200 = $this->pos;
		$key = 'match_Vector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Vector(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else {
			$result = $res_200;
			$this->pos = $pos_200;
			unset($res_200, $pos_200);
		}
		$_201 = \true; break;
	}
	while(\false);
	if( $_201 === \true ) { return $this->finalise($result); }
	if( $_201 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = ['Mutable'];
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_206 = \null;
	do {
		$res_203 = $result;
		$pos_203 = $this->pos;
		$key = 'match_VariableVector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableVector(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_206 = \true; break;
		}
		$result = $res_203;
		$this->pos = $pos_203;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_206 = \true; break;
		}
		$result = $res_203;
		$this->pos = $pos_203;
		$_206 = \false; break;
	}
	while(\false);
	if( $_206 === \true ) { return $this->finalise($result); }
	if( $_206 === \false) { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = ['AddOperator'];
function match_AddOperator () {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule);
	$_211 = \null;
	do {
		$res_208 = $result;
		$pos_208 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_211 = \true; break;
		}
		$result = $res_208;
		$this->pos = $pos_208;
		if (\substr($this->string, $this->pos, 1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_211 = \true; break;
		}
		$result = $res_208;
		$this->pos = $pos_208;
		$_211 = \false; break;
	}
	while(\false);
	if( $_211 === \true ) { return $this->finalise($result); }
	if( $_211 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = ['MultiplyOperator'];
function match_MultiplyOperator () {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule);
	$_216 = \null;
	do {
		$res_213 = $result;
		$pos_213 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_216 = \true; break;
		}
		$result = $res_213;
		$this->pos = $pos_213;
		if (\substr($this->string, $this->pos, 1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_216 = \true; break;
		}
		$result = $res_213;
		$this->pos = $pos_213;
		$_216 = \false; break;
	}
	while(\false);
	if( $_216 === \true ) { return $this->finalise($result); }
	if( $_216 === \false) { return \false; }
}


/* PowerOperator: "**" */
protected $match_PowerOperator_typestack = ['PowerOperator'];
function match_PowerOperator ($stack = []) {
	$matchrule = "PowerOperator"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('**')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = ['AssignmentOperator'];
function match_AssignmentOperator () {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = ['ComparisonOperator'];
function match_ComparisonOperator ($stack = []) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_239 = \null;
	do {
		$res_220 = $result;
		$pos_220 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_239 = \true; break;
		}
		$result = $res_220;
		$this->pos = $pos_220;
		$_237 = \null;
		do {
			$res_222 = $result;
			$pos_222 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_237 = \true; break;
			}
			$result = $res_222;
			$this->pos = $pos_222;
			$_235 = \null;
			do {
				$res_224 = $result;
				$pos_224 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_235 = \true; break;
				}
				$result = $res_224;
				$this->pos = $pos_224;
				$_233 = \null;
				do {
					$res_226 = $result;
					$pos_226 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_233 = \true; break;
					}
					$result = $res_226;
					$this->pos = $pos_226;
					$_231 = \null;
					do {
						$res_228 = $result;
						$pos_228 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_231 = \true; break;
						}
						$result = $res_228;
						$this->pos = $pos_228;
						if (\substr($this->string, $this->pos, 1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_231 = \true; break;
						}
						$result = $res_228;
						$this->pos = $pos_228;
						$_231 = \false; break;
					}
					while(\false);
					if( $_231 === \true ) { $_233 = \true; break; }
					$result = $res_226;
					$this->pos = $pos_226;
					$_233 = \false; break;
				}
				while(\false);
				if( $_233 === \true ) { $_235 = \true; break; }
				$result = $res_224;
				$this->pos = $pos_224;
				$_235 = \false; break;
			}
			while(\false);
			if( $_235 === \true ) { $_237 = \true; break; }
			$result = $res_222;
			$this->pos = $pos_222;
			$_237 = \false; break;
		}
		while(\false);
		if( $_237 === \true ) { $_239 = \true; break; }
		$result = $res_220;
		$this->pos = $pos_220;
		$_239 = \false; break;
	}
	while(\false);
	if( $_239 === \true ) { return $this->finalise($result); }
	if( $_239 === \false) { return \false; }
}


/* ComparisonOperatorWithWhitespace: "in"  | "not in" */
protected $match_ComparisonOperatorWithWhitespace_typestack = ['ComparisonOperatorWithWhitespace'];
function match_ComparisonOperatorWithWhitespace ($stack = []) {
	$matchrule = "ComparisonOperatorWithWhitespace"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_244 = \null;
	do {
		$res_241 = $result;
		$pos_241 = $this->pos;
		if (($subres = $this->literal('in')) !== \false) {
			$result["text"] .= $subres;
			$_244 = \true; break;
		}
		$result = $res_241;
		$this->pos = $pos_241;
		if (($subres = $this->literal('not in')) !== \false) {
			$result["text"] .= $subres;
			$_244 = \true; break;
		}
		$result = $res_241;
		$this->pos = $pos_241;
		$_244 = \false; break;
	}
	while(\false);
	if( $_244 === \true ) { return $this->finalise($result); }
	if( $_244 === \false) { return \false; }
}


/* AndOperator: "and" */
protected $match_AndOperator_typestack = ['AndOperator'];
function match_AndOperator ($stack = []) {
	$matchrule = "AndOperator"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('and')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* OrOperator: "or" */
protected $match_OrOperator_typestack = ['OrOperator'];
function match_OrOperator ($stack = []) {
	$matchrule = "OrOperator"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('or')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NegationOperator: "!" */
protected $match_NegationOperator_typestack = ['NegationOperator'];
function match_NegationOperator () {
	$matchrule = "NegationOperator"; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '!') {
		$this->pos += 1;
		$result["text"] .= '!';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:CondExpr */
protected $match_Expression_typestack = ['Expression'];
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_256 = \null;
	do {
		$res_249 = $result;
		$pos_249 = $this->pos;
		$key = 'match_AnonymousFunction'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AnonymousFunction(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_256 = \true; break;
		}
		$result = $res_249;
		$this->pos = $pos_249;
		$_254 = \null;
		do {
			$res_251 = $result;
			$pos_251 = $this->pos;
			$key = 'match_Assignment'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Assignment(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_254 = \true; break;
			}
			$result = $res_251;
			$this->pos = $pos_251;
			$key = 'match_CondExpr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_CondExpr(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_254 = \true; break;
			}
			$result = $res_251;
			$this->pos = $pos_251;
			$_254 = \false; break;
		}
		while(\false);
		if( $_254 === \true ) { $_256 = \true; break; }
		$result = $res_249;
		$this->pos = $pos_249;
		$_256 = \false; break;
	}
	while(\false);
	if( $_256 === \true ) { return $this->finalise($result); }
	if( $_256 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_263 = \null;
	do {
		$key = 'match_Mutable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Mutable(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_263 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_263 = \false; break; }
		$key = 'match_AssignmentOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AssignmentOperator(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_263 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_263 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_263 = \false; break; }
		$_263 = \true; break;
	}
	while(\false);
	if( $_263 === \true ) { return $this->finalise($result); }
	if( $_263 === \false) { return \false; }
}


/* CondExpr: true:LogicalOr ( ] "if" __ "(" __ cond:Expression __ ")" __ "else" ] false:LogicalOr )? */
protected $match_CondExpr_typestack = ['CondExpr'];
function match_CondExpr ($stack = []) {
	$matchrule = "CondExpr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_280 = \null;
	do {
		$key = 'match_LogicalOr'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalOr(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "true");
		}
		else { $_280 = \false; break; }
		$res_279 = $result;
		$pos_279 = $this->pos;
		$_278 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_278 = \false; break; }
			if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
			else { $_278 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_278 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_278 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_278 = \false; break; }
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "cond");
			}
			else { $_278 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_278 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_278 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_278 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_278 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_278 = \false; break; }
			$key = 'match_LogicalOr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_LogicalOr(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "false");
			}
			else { $_278 = \false; break; }
			$_278 = \true; break;
		}
		while(\false);
		if( $_278 === \false) {
			$result = $res_279;
			$this->pos = $pos_279;
			unset($res_279, $pos_279);
		}
		$_280 = \true; break;
	}
	while(\false);
	if( $_280 === \true ) { return $this->finalise($result); }
	if( $_280 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_289 = \null;
	do {
		$key = 'match_LogicalAnd'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalAnd(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_289 = \false; break; }
		while (\true) {
			$res_288 = $result;
			$pos_288 = $this->pos;
			$_287 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_287 = \false; break; }
				$key = 'match_OrOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_OrOperator(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_287 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_287 = \false; break; }
				$key = 'match_LogicalAnd'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_LogicalAnd(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_287 = \false; break; }
				$_287 = \true; break;
			}
			while(\false);
			if( $_287 === \false) {
				$result = $res_288;
				$this->pos = $pos_288;
				unset($res_288, $pos_288);
				break;
			}
		}
		$_289 = \true; break;
	}
	while(\false);
	if( $_289 === \true ) { return $this->finalise($result); }
	if( $_289 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_298 = \null;
	do {
		$key = 'match_Comparison'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Comparison(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_298 = \false; break; }
		while (\true) {
			$res_297 = $result;
			$pos_297 = $this->pos;
			$_296 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_296 = \false; break; }
				$key = 'match_AndOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AndOperator(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_296 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_296 = \false; break; }
				$key = 'match_Comparison'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Comparison(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_296 = \false; break; }
				$_296 = \true; break;
			}
			while(\false);
			if( $_296 === \false) {
				$result = $res_297;
				$this->pos = $pos_297;
				unset($res_297, $pos_297);
				break;
			}
		}
		$_298 = \true; break;
	}
	while(\false);
	if( $_298 === \true ) { return $this->finalise($result); }
	if( $_298 === \false) { return \false; }
}


/* Comparison: operands:Addition ( ( __ ops:ComparisonOperator __ | ops: ] ComparisonOperatorWithWhitespace ] ) operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_319 = \null;
	do {
		$key = 'match_Addition'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Addition(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_319 = \false; break; }
		while (\true) {
			$res_318 = $result;
			$pos_318 = $this->pos;
			$_317 = \null;
			do {
				$_314 = \null;
				do {
					$_312 = \null;
					do {
						$res_301 = $result;
						$pos_301 = $this->pos;
						$_305 = \null;
						do {
							$key = 'match___'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_305 = \false; break; }
							$key = 'match_ComparisonOperator'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ComparisonOperator(array_merge($stack, array($result))));
							if ($subres !== \false) {
								$this->store($result, $subres, "ops");
							}
							else { $_305 = \false; break; }
							$key = 'match___'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_305 = \false; break; }
							$_305 = \true; break;
						}
						while(\false);
						if( $_305 === \true ) { $_312 = \true; break; }
						$result = $res_301;
						$this->pos = $pos_301;
						$_310 = \null;
						do {
							if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
							else { $_310 = \false; break; }
							$key = 'match_ComparisonOperatorWithWhitespace'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ComparisonOperatorWithWhitespace(array_merge($stack, array($result))));
							if ($subres !== \false) {
								$this->store($result, $subres, "ops");
							}
							else { $_310 = \false; break; }
							if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
							else { $_310 = \false; break; }
							$_310 = \true; break;
						}
						while(\false);
						if( $_310 === \true ) { $_312 = \true; break; }
						$result = $res_301;
						$this->pos = $pos_301;
						$_312 = \false; break;
					}
					while(\false);
					if( $_312 === \false) { $_314 = \false; break; }
					$_314 = \true; break;
				}
				while(\false);
				if( $_314 === \false) { $_317 = \false; break; }
				$key = 'match_Addition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Addition(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_317 = \false; break; }
				$_317 = \true; break;
			}
			while(\false);
			if( $_317 === \false) {
				$result = $res_318;
				$this->pos = $pos_318;
				unset($res_318, $pos_318);
				break;
			}
		}
		$_319 = \true; break;
	}
	while(\false);
	if( $_319 === \true ) { return $this->finalise($result); }
	if( $_319 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_328 = \null;
	do {
		$key = 'match_Multiplication'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Multiplication(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_328 = \false; break; }
		while (\true) {
			$res_327 = $result;
			$pos_327 = $this->pos;
			$_326 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_326 = \false; break; }
				$key = 'match_AddOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AddOperator(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_326 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_326 = \false; break; }
				$key = 'match_Multiplication'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Multiplication(array_merge($stack, array($result))));
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


/* Multiplication: operands:Exponentiation ( __ ops:MultiplyOperator __ operands:Exponentiation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_337 = \null;
	do {
		$key = 'match_Exponentiation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Exponentiation(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_337 = \false; break; }
		while (\true) {
			$res_336 = $result;
			$pos_336 = $this->pos;
			$_335 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_335 = \false; break; }
				$key = 'match_MultiplyOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_MultiplyOperator(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_335 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_335 = \false; break; }
				$key = 'match_Exponentiation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Exponentiation(array_merge($stack, array($result))));
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


/* Exponentiation: operands:Negation ( __ ops:PowerOperator __ operands:Negation )* */
protected $match_Exponentiation_typestack = ['Exponentiation'];
function match_Exponentiation ($stack = []) {
	$matchrule = "Exponentiation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_346 = \null;
	do {
		$key = 'match_Negation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Negation(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_346 = \false; break; }
		while (\true) {
			$res_345 = $result;
			$pos_345 = $this->pos;
			$_344 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_344 = \false; break; }
				$key = 'match_PowerOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_PowerOperator(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_344 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_344 = \false; break; }
				$key = 'match_Negation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Negation(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_344 = \false; break; }
				$_344 = \true; break;
			}
			while(\false);
			if( $_344 === \false) {
				$result = $res_345;
				$this->pos = $pos_345;
				unset($res_345, $pos_345);
				break;
			}
		}
		$_346 = \true; break;
	}
	while(\false);
	if( $_346 === \true ) { return $this->finalise($result); }
	if( $_346 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_352 = \null;
	do {
		while (\true) {
			$res_350 = $result;
			$pos_350 = $this->pos;
			$_349 = \null;
			do {
				$key = 'match_NegationOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_NegationOperator(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_349 = \false; break; }
				$_349 = \true; break;
			}
			while(\false);
			if( $_349 === \false) {
				$result = $res_350;
				$this->pos = $pos_350;
				unset($res_350, $pos_350);
				break;
			}
		}
		$key = 'match_Operand'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Operand(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_352 = \false; break; }
		$_352 = \true; break;
	}
	while(\false);
	if( $_352 === \true ) { return $this->finalise($result); }
	if( $_352 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:AbstractValue ) chain:Chain? ) | skip:AbstractValue */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_372 = \null;
	do {
		$res_354 = $result;
		$pos_354 = $this->pos;
		$_369 = \null;
		do {
			$_366 = \null;
			do {
				$_364 = \null;
				do {
					$res_355 = $result;
					$pos_355 = $this->pos;
					$_361 = \null;
					do {
						if (\substr($this->string, $this->pos, 1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_361 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_361 = \false; break; }
						$key = 'match_Expression'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_361 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_361 = \false; break; }
						if (\substr($this->string, $this->pos, 1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_361 = \false; break; }
						$_361 = \true; break;
					}
					while(\false);
					if( $_361 === \true ) { $_364 = \true; break; }
					$result = $res_355;
					$this->pos = $pos_355;
					$key = 'match_AbstractValue'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_AbstractValue(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_364 = \true; break;
					}
					$result = $res_355;
					$this->pos = $pos_355;
					$_364 = \false; break;
				}
				while(\false);
				if( $_364 === \false) { $_366 = \false; break; }
				$_366 = \true; break;
			}
			while(\false);
			if( $_366 === \false) { $_369 = \false; break; }
			$res_368 = $result;
			$pos_368 = $this->pos;
			$key = 'match_Chain'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Chain(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_368;
				$this->pos = $pos_368;
				unset($res_368, $pos_368);
			}
			$_369 = \true; break;
		}
		while(\false);
		if( $_369 === \true ) { $_372 = \true; break; }
		$result = $res_354;
		$this->pos = $pos_354;
		$key = 'match_AbstractValue'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AbstractValue(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_372 = \true; break;
		}
		$result = $res_354;
		$this->pos = $pos_354;
		$_372 = \false; break;
	}
	while(\false);
	if( $_372 === \true ) { return $this->finalise($result); }
	if( $_372 === \false) { return \false; }
}


/* Chain: &/[\[\(\.]/ ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_387 = \null;
	do {
		$res_374 = $result;
		$pos_374 = $this->pos;
		if (($subres = $this->rx('/[\[\(\.]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_374;
			$this->pos = $pos_374;
		}
		else {
			$result = $res_374;
			$this->pos = $pos_374;
			$_387 = \false; break;
		}
		$_384 = \null;
		do {
			$_382 = \null;
			do {
				$res_375 = $result;
				$pos_375 = $this->pos;
				$key = 'match_Dereference'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Dereference(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_382 = \true; break;
				}
				$result = $res_375;
				$this->pos = $pos_375;
				$_380 = \null;
				do {
					$res_377 = $result;
					$pos_377 = $this->pos;
					$key = 'match_Invocation'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Invocation(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_380 = \true; break;
					}
					$result = $res_377;
					$this->pos = $pos_377;
					$key = 'match_ChainedFunction'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ChainedFunction(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_380 = \true; break;
					}
					$result = $res_377;
					$this->pos = $pos_377;
					$_380 = \false; break;
				}
				while(\false);
				if( $_380 === \true ) { $_382 = \true; break; }
				$result = $res_375;
				$this->pos = $pos_375;
				$_382 = \false; break;
			}
			while(\false);
			if( $_382 === \false) { $_384 = \false; break; }
			$_384 = \true; break;
		}
		while(\false);
		if( $_384 === \false) { $_387 = \false; break; }
		$res_386 = $result;
		$pos_386 = $this->pos;
		$key = 'match_Chain'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Chain(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_386;
			$this->pos = $pos_386;
			unset($res_386, $pos_386);
		}
		$_387 = \true; break;
	}
	while(\false);
	if( $_387 === \true ) { return $this->finalise($result); }
	if( $_387 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_394 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_394 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_394 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_394 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_394 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_394 = \false; break; }
		$_394 = \true; break;
	}
	while(\false);
	if( $_394 === \true ) { return $this->finalise($result); }
	if( $_394 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_401 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_401 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_401 = \false; break; }
		$res_398 = $result;
		$pos_398 = $this->pos;
		$key = 'match_ArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ArgumentList(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_398;
			$this->pos = $pos_398;
			unset($res_398, $pos_398);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_401 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_401 = \false; break; }
		$_401 = \true; break;
	}
	while(\false);
	if( $_401 === \true ) { return $this->finalise($result); }
	if( $_401 === \false) { return \false; }
}


/* ChainedFunction: "." fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_406 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_406 = \false; break; }
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_406 = \false; break; }
		$key = 'match_Invocation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Invocation(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_406 = \false; break; }
		$_406 = \true; break;
	}
	while(\false);
	if( $_406 === \true ) { return $this->finalise($result); }
	if( $_406 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_415 = \null;
	do {
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_415 = \false; break; }
		while (\true) {
			$res_414 = $result;
			$pos_414 = $this->pos;
			$_413 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_413 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_413 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_413 = \false; break; }
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_413 = \false; break; }
				$_413 = \true; break;
			}
			while(\false);
			if( $_413 === \false) {
				$result = $res_414;
				$this->pos = $pos_414;
				unset($res_414, $pos_414);
				break;
			}
		}
		$_415 = \true; break;
	}
	while(\false);
	if( $_415 === \true ) { return $this->finalise($result); }
	if( $_415 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_424 = \null;
	do {
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_424 = \false; break; }
		while (\true) {
			$res_423 = $result;
			$pos_423 = $this->pos;
			$_422 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_422 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_422 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_422 = \false; break; }
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_422 = \false; break; }
				$_422 = \true; break;
			}
			while(\false);
			if( $_422 === \false) {
				$result = $res_423;
				$this->pos = $pos_423;
				unset($res_423, $pos_423);
				break;
			}
		}
		$_424 = \true; break;
	}
	while(\false);
	if( $_424 === \true ) { return $this->finalise($result); }
	if( $_424 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_437 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_437 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_437 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_437 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_437 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_437 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_437 = \false; break; }
		$res_432 = $result;
		$pos_432 = $this->pos;
		$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_432;
			$this->pos = $pos_432;
			unset($res_432, $pos_432);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_437 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_437 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_437 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_437 = \false; break; }
		$_437 = \true; break;
	}
	while(\false);
	if( $_437 === \true ) { return $this->finalise($result); }
	if( $_437 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_458 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_458 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_458 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_458 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_458 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_458 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_458 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_458 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_458 = \false; break; }
		$_448 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_448 = \false; break; }
			$_448 = \true; break;
		}
		while(\false);
		if( $_448 === \false) { $_458 = \false; break; }
		$res_457 = $result;
		$pos_457 = $this->pos;
		$_456 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_456 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_456 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_456 = \false; break; }
			$_454 = \null;
			do {
				$key = 'match_Block'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_454 = \false; break; }
				$_454 = \true; break;
			}
			while(\false);
			if( $_454 === \false) { $_456 = \false; break; }
			$_456 = \true; break;
		}
		while(\false);
		if( $_456 === \false) {
			$result = $res_457;
			$this->pos = $pos_457;
			unset($res_457, $pos_457);
		}
		$_458 = \true; break;
	}
	while(\false);
	if( $_458 === \true ) { return $this->finalise($result); }
	if( $_458 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ ( key:VariableName __ ":" __ )? item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_481 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_481 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_481 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_481 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_481 = \false; break; }
		$res_469 = $result;
		$pos_469 = $this->pos;
		$_468 = \null;
		do {
			$key = 'match_VariableName'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "key");
			}
			else { $_468 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_468 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_468 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_468 = \false; break; }
			$_468 = \true; break;
		}
		while(\false);
		if( $_468 === \false) {
			$result = $res_469;
			$this->pos = $pos_469;
			unset($res_469, $pos_469);
		}
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_481 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_481 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_481 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_481 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_481 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_481 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_481 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_481 = \false; break; }
		$_479 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_479 = \false; break; }
			$_479 = \true; break;
		}
		while(\false);
		if( $_479 === \false) { $_481 = \false; break; }
		$_481 = \true; break;
	}
	while(\false);
	if( $_481 === \true ) { return $this->finalise($result); }
	if( $_481 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_494 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_494 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_494 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_494 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_494 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_494 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_494 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_494 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_494 = \false; break; }
		$_492 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_492 = \false; break; }
			$_492 = \true; break;
		}
		while(\false);
		if( $_492 === \false) { $_494 = \false; break; }
		$_494 = \true; break;
	}
	while(\false);
	if( $_494 === \true ) { return $this->finalise($result); }
	if( $_494 === \false) { return \false; }
}


/* TryStatement: "try" __ main:Block __ "catch" __ onerror:Block */
protected $match_TryStatement_typestack = ['TryStatement'];
function match_TryStatement ($stack = []) {
	$matchrule = "TryStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_503 = \null;
	do {
		if (($subres = $this->literal('try')) !== \false) { $result["text"] .= $subres; }
		else { $_503 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_503 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "main");
		}
		else { $_503 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_503 = \false; break; }
		if (($subres = $this->literal('catch')) !== \false) { $result["text"] .= $subres; }
		else { $_503 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_503 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres, "onerror");
		}
		else { $_503 = \false; break; }
		$_503 = \true; break;
	}
	while(\false);
	if( $_503 === \true ) { return $this->finalise($result); }
	if( $_503 === \false) { return \false; }
}


/* CommandStatements: &/[rbc]/ ( skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement ) */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_517 = \null;
	do {
		$res_505 = $result;
		$pos_505 = $this->pos;
		if (($subres = $this->rx('/[rbc]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_505;
			$this->pos = $pos_505;
		}
		else {
			$result = $res_505;
			$this->pos = $pos_505;
			$_517 = \false; break;
		}
		$_515 = \null;
		do {
			$_513 = \null;
			do {
				$res_506 = $result;
				$pos_506 = $this->pos;
				$key = 'match_ReturnStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ReturnStatement(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_513 = \true; break;
				}
				$result = $res_506;
				$this->pos = $pos_506;
				$_511 = \null;
				do {
					$res_508 = $result;
					$pos_508 = $this->pos;
					$key = 'match_BreakStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_BreakStatement(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_511 = \true; break;
					}
					$result = $res_508;
					$this->pos = $pos_508;
					$key = 'match_ContinueStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ContinueStatement(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_511 = \true; break;
					}
					$result = $res_508;
					$this->pos = $pos_508;
					$_511 = \false; break;
				}
				while(\false);
				if( $_511 === \true ) { $_513 = \true; break; }
				$result = $res_506;
				$this->pos = $pos_506;
				$_513 = \false; break;
			}
			while(\false);
			if( $_513 === \false) { $_515 = \false; break; }
			$_515 = \true; break;
		}
		while(\false);
		if( $_515 === \false) { $_517 = \false; break; }
		$_517 = \true; break;
	}
	while(\false);
	if( $_517 === \true ) { return $this->finalise($result); }
	if( $_517 === \false) { return \false; }
}


/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_531 = \null;
	do {
		$res_519 = $result;
		$pos_519 = $this->pos;
		$_525 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_525 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_525 = \false; break; }
			$res_524 = $result;
			$pos_524 = $this->pos;
			$_523 = \null;
			do {
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_523 = \false; break; }
				$_523 = \true; break;
			}
			while(\false);
			if( $_523 === \false) {
				$result = $res_524;
				$this->pos = $pos_524;
				unset($res_524, $pos_524);
			}
			$_525 = \true; break;
		}
		while(\false);
		if( $_525 === \true ) { $_531 = \true; break; }
		$result = $res_519;
		$this->pos = $pos_519;
		$_529 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_529 = \false; break; }
			$res_528 = $result;
			$pos_528 = $this->pos;
			$key = 'match_SEP'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_SEP(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_528;
				$this->pos = $pos_528;
			}
			else {
				$result = $res_528;
				$this->pos = $pos_528;
				$_529 = \false; break;
			}
			$_529 = \true; break;
		}
		while(\false);
		if( $_529 === \true ) { $_531 = \true; break; }
		$result = $res_519;
		$this->pos = $pos_519;
		$_531 = \false; break;
	}
	while(\false);
	if( $_531 === \true ) { return $this->finalise($result); }
	if( $_531 === \false) { return \false; }
}


/* BreakStatement: "break" */
protected $match_BreakStatement_typestack = ['BreakStatement'];
function match_BreakStatement ($stack = []) {
	$matchrule = "BreakStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('break')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ContinueStatement: "continue" */
protected $match_ContinueStatement_typestack = ['ContinueStatement'];
function match_ContinueStatement ($stack = []) {
	$matchrule = "ContinueStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('continue')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BlockStatements: &/[iwft]/ ( skip:IfStatement | skip:WhileStatement | skip:FunctionDefinition | skip:ForStatement | skip:TryStatement) */
protected $match_BlockStatements_typestack = ['BlockStatements'];
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_555 = \null;
	do {
		$res_535 = $result;
		$pos_535 = $this->pos;
		if (($subres = $this->rx('/[iwft]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_535;
			$this->pos = $pos_535;
		}
		else {
			$result = $res_535;
			$this->pos = $pos_535;
			$_555 = \false; break;
		}
		$_553 = \null;
		do {
			$_551 = \null;
			do {
				$res_536 = $result;
				$pos_536 = $this->pos;
				$key = 'match_IfStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_IfStatement(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_551 = \true; break;
				}
				$result = $res_536;
				$this->pos = $pos_536;
				$_549 = \null;
				do {
					$res_538 = $result;
					$pos_538 = $this->pos;
					$key = 'match_WhileStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_WhileStatement(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_549 = \true; break;
					}
					$result = $res_538;
					$this->pos = $pos_538;
					$_547 = \null;
					do {
						$res_540 = $result;
						$pos_540 = $this->pos;
						$key = 'match_FunctionDefinition'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_FunctionDefinition(array_merge($stack, array($result))));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_547 = \true; break;
						}
						$result = $res_540;
						$this->pos = $pos_540;
						$_545 = \null;
						do {
							$res_542 = $result;
							$pos_542 = $this->pos;
							$key = 'match_ForStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ForStatement(array_merge($stack, array($result))));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_545 = \true; break;
							}
							$result = $res_542;
							$this->pos = $pos_542;
							$key = 'match_TryStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_TryStatement(array_merge($stack, array($result))));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_545 = \true; break;
							}
							$result = $res_542;
							$this->pos = $pos_542;
							$_545 = \false; break;
						}
						while(\false);
						if( $_545 === \true ) { $_547 = \true; break; }
						$result = $res_540;
						$this->pos = $pos_540;
						$_547 = \false; break;
					}
					while(\false);
					if( $_547 === \true ) { $_549 = \true; break; }
					$result = $res_538;
					$this->pos = $pos_538;
					$_549 = \false; break;
				}
				while(\false);
				if( $_549 === \true ) { $_551 = \true; break; }
				$result = $res_536;
				$this->pos = $pos_536;
				$_551 = \false; break;
			}
			while(\false);
			if( $_551 === \false) { $_553 = \false; break; }
			$_553 = \true; break;
		}
		while(\false);
		if( $_553 === \false) { $_555 = \false; break; }
		$_555 = \true; break;
	}
	while(\false);
	if( $_555 === \true ) { return $this->finalise($result); }
	if( $_555 === \false) { return \false; }
}


/* Statement: !/[\s\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_569 = \null;
	do {
		$res_557 = $result;
		$pos_557 = $this->pos;
		if (($subres = $this->rx('/[\s\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_557;
			$this->pos = $pos_557;
			$_569 = \false; break;
		}
		else {
			$result = $res_557;
			$this->pos = $pos_557;
		}
		$_567 = \null;
		do {
			$_565 = \null;
			do {
				$res_558 = $result;
				$pos_558 = $this->pos;
				$key = 'match_BlockStatements'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BlockStatements(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_565 = \true; break;
				}
				$result = $res_558;
				$this->pos = $pos_558;
				$_563 = \null;
				do {
					$res_560 = $result;
					$pos_560 = $this->pos;
					$key = 'match_CommandStatements'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_CommandStatements(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_563 = \true; break;
					}
					$result = $res_560;
					$this->pos = $pos_560;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression(array_merge($stack, array($result))));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_563 = \true; break;
					}
					$result = $res_560;
					$this->pos = $pos_560;
					$_563 = \false; break;
				}
				while(\false);
				if( $_563 === \true ) { $_565 = \true; break; }
				$result = $res_558;
				$this->pos = $pos_558;
				$_565 = \false; break;
			}
			while(\false);
			if( $_565 === \false) { $_567 = \false; break; }
			$_567 = \true; break;
		}
		while(\false);
		if( $_567 === \false) { $_569 = \false; break; }
		$_569 = \true; break;
	}
	while(\false);
	if( $_569 === \true ) { return $this->finalise($result); }
	if( $_569 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_577 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_577 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_577 = \false; break; }
		$res_575 = $result;
		$pos_575 = $this->pos;
		$_574 = \null;
		do {
			$key = 'match_Program'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Program(array_merge($stack, array($result))));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_574 = \false; break; }
			$_574 = \true; break;
		}
		while(\false);
		if( $_574 === \false) {
			$result = $res_575;
			$this->pos = $pos_575;
			unset($res_575, $pos_575);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_577 = \false; break; }
		$_577 = \true; break;
	}
	while(\false);
	if( $_577 === \true ) { return $this->finalise($result); }
	if( $_577 === \false) { return \false; }
}


/* __: / [\s]*+(?:\/\/[^\n]*+(?:\s*+))? / */
protected $match____typestack = ['__'];
function match___ ($stack = []) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ [\s]*+(?:\/\/[^\n]*+(?:\s*+))? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = ['NL'];
function match_NL ($stack = []) {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ (?:\/\/[^\n]*)?\n /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* SEP: ";" | NL */
protected $match_SEP_typestack = ['SEP'];
function match_SEP ($stack = []) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_584 = \null;
	do {
		$res_581 = $result;
		$pos_581 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_584 = \true; break;
		}
		$result = $res_581;
		$this->pos = $pos_581;
		$key = 'match_NL'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NL(array_merge($stack, array($result))));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_584 = \true; break;
		}
		$result = $res_581;
		$this->pos = $pos_581;
		$_584 = \false; break;
	}
	while(\false);
	if( $_584 === \true ) { return $this->finalise($result); }
	if( $_584 === \false) { return \false; }
}


/* Program: ( __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_593 = \null;
	do {
		while (\true) {
			$res_591 = $result;
			$pos_591 = $this->pos;
			$_590 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_590 = \false; break; }
				$res_587 = $result;
				$pos_587 = $this->pos;
				$key = 'match_Statement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Statement(array_merge($stack, array($result))));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_587;
					$this->pos = $pos_587;
					unset($res_587, $pos_587);
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$key = 'match_SEP'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_SEP(array_merge($stack, array($result))));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_590 = \false; break; }
				$_590 = \true; break;
			}
			while(\false);
			if( $_590 === \false) {
				$result = $res_591;
				$this->pos = $pos_591;
				unset($res_591, $pos_591);
				break;
			}
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___(array_merge($stack, array($result))));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_593 = \false; break; }
		$_593 = \true; break;
	}
	while(\false);
	if( $_593 === \true ) { return $this->finalise($result); }
	if( $_593 === \false) { return \false; }
}




}
