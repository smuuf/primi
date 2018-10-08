<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

	// Add these properties so PHPStan doesn't complain about undefined properties.

	/** @var int **/
	public $pos;

	/** @var string **/
	public $string;

/* StringLiteral: / (?:".*?(?<!\\)")|(?:'.*?(?<!\\)') /s */
protected $match_StringLiteral_typestack = array('StringLiteral');
function match_StringLiteral ($stack = []) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ (?:".*?(?<!\\\\)")|(?:\'.*?(?<!\\\\)\') /s' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = array('NumberLiteral');
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/-?\d+(\.\d+)?/' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = array('BoolLiteral');
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	$_5 = \null;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== \false) {
			$result["text"] .= $subres;
			$_5 = \true; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		if (( $subres = $this->literal( 'false' ) ) !== \false) {
			$result["text"] .= $subres;
			$_5 = \true; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$_5 = \false; break;
	}
	while(0);
	if( $_5 === \true ) { return $this->finalise($result); }
	if( $_5 === \false) { return \false; }
}


/* NullLiteral: "null" */
protected $match_NullLiteral_typestack = array('NullLiteral');
function match_NullLiteral ($stack = []) {
	$matchrule = "NullLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->literal( 'null' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* RegexLiteral: "/" /(\\\/|[^\/])+/ "/" */
protected $match_RegexLiteral_typestack = array('RegexLiteral');
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	$_11 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_11 = \false; break; }
		if (( $subres = $this->rx( '/(\\\\\/|[^\/])+/' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_11 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_11 = \false; break; }
		$_11 = \true; break;
	}
	while(0);
	if( $_11 === \true ) { return $this->finalise($result); }
	if( $_11 === \false) { return \false; }
}


/* Nothing: "" */
protected $match_Nothing_typestack = array('Nothing');
function match_Nothing ($stack = []) {
	$matchrule = "Nothing"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->literal( '' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:NullLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, \null);
	$_29 = \null;
	do {
		$res_14 = $result;
		$pos_14 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_29 = \true; break;
		}
		$result = $res_14;
		$this->pos = $pos_14;
		$_27 = \null;
		do {
			$res_16 = $result;
			$pos_16 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_27 = \true; break;
			}
			$result = $res_16;
			$this->pos = $pos_16;
			$_25 = \null;
			do {
				$res_18 = $result;
				$pos_18 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_25 = \true; break;
				}
				$result = $res_18;
				$this->pos = $pos_18;
				$_23 = \null;
				do {
					$res_20 = $result;
					$pos_20 = $this->pos;
					$matcher = 'match_'.'NullLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_23 = \true; break;
					}
					$result = $res_20;
					$this->pos = $pos_20;
					$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_23 = \true; break;
					}
					$result = $res_20;
					$this->pos = $pos_20;
					$_23 = \false; break;
				}
				while(0);
				if( $_23 === \true ) { $_25 = \true; break; }
				$result = $res_18;
				$this->pos = $pos_18;
				$_25 = \false; break;
			}
			while(0);
			if( $_25 === \true ) { $_27 = \true; break; }
			$result = $res_16;
			$this->pos = $pos_16;
			$_27 = \false; break;
		}
		while(0);
		if( $_27 === \true ) { $_29 = \true; break; }
		$result = $res_14;
		$this->pos = $pos_14;
		$_29 = \false; break;
	}
	while(0);
	if( $_29 === \true ) { return $this->finalise($result); }
	if( $_29 === \false) { return \false; }
}


/* VariableName: / ([a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = array('VariableName');
function match_VariableName ($stack = []) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ ([a-zA-Z_][a-zA-Z0-9_]*) /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Variable: core:VariableName */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, \null);
	$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
	if ($subres !== \false) {
		$this->store( $result, $subres, "core" );
		return $this->finalise($result);
	}
	else { return \false; }
}


/* UnaryOpVariable: ( pre:UnaryOperator core:Variable ) | ( core:Variable post:UnaryOperator ) */
protected $match_UnaryOpVariable_typestack = array('UnaryOpVariable');
function match_UnaryOpVariable ($stack = []) {
	$matchrule = "UnaryOpVariable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_42 = \null;
	do {
		$res_33 = $result;
		$pos_33 = $this->pos;
		$_36 = \null;
		do {
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "pre" );
			}
			else { $_36 = \false; break; }
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_36 = \false; break; }
			$_36 = \true; break;
		}
		while(0);
		if( $_36 === \true ) { $_42 = \true; break; }
		$result = $res_33;
		$this->pos = $pos_33;
		$_40 = \null;
		do {
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_40 = \false; break; }
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "post" );
			}
			else { $_40 = \false; break; }
			$_40 = \true; break;
		}
		while(0);
		if( $_40 === \true ) { $_42 = \true; break; }
		$result = $res_33;
		$this->pos = $pos_33;
		$_42 = \false; break;
	}
	while(0);
	if( $_42 === \true ) { return $this->finalise($result); }
	if( $_42 === \false) { return \false; }
}


/* AnonymousFunction: "function" __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ args:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = array('AnonymousFunction');
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_67 = \null;
	do {
		$res_44 = $result;
		$pos_44 = $this->pos;
		$_54 = \null;
		do {
			if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_54 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_54 = \false; break; }
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_54 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_54 = \false; break; }
			$res_49 = $result;
			$pos_49 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_49;
				$this->pos = $pos_49;
				unset( $res_49 );
				unset( $pos_49 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_54 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_54 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_54 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_54 = \false; break; }
			$_54 = \true; break;
		}
		while(0);
		if( $_54 === \true ) { $_67 = \true; break; }
		$result = $res_44;
		$this->pos = $pos_44;
		$_65 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_65 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_65 = \false; break; }
			$res_58 = $result;
			$pos_58 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_58;
				$this->pos = $pos_58;
				unset( $res_58 );
				unset( $pos_58 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_65 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_65 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_65 = \false; break; }
			if (( $subres = $this->literal( '=>' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_65 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_65 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_65 = \false; break; }
			$_65 = \true; break;
		}
		while(0);
		if( $_65 === \true ) { $_67 = \true; break; }
		$result = $res_44;
		$this->pos = $pos_44;
		$_67 = \false; break;
	}
	while(0);
	if( $_67 === \true ) { return $this->finalise($result); }
	if( $_67 === \false) { return \false; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = []) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, \null);
	$_76 = \null;
	do {
		$res_73 = $result;
		$pos_73 = $this->pos;
		$_72 = \null;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "key" );
			}
			else { $_72 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_72 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_72 = \false; break; }
			$_72 = \true; break;
		}
		while(0);
		if( $_72 === \false) {
			$result = $res_73;
			$this->pos = $pos_73;
			unset( $res_73 );
			unset( $pos_73 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_76 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "value" );
		}
		else { $_76 = \false; break; }
		$_76 = \true; break;
	}
	while(0);
	if( $_76 === \true ) { return $this->finalise($result); }
	if( $_76 === \false) { return \false; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ ( "," __ )? "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = []) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_95 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_95 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_95 = \false; break; }
		$res_88 = $result;
		$pos_88 = $this->pos;
		$_87 = \null;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "items" );
			}
			else { $_87 = \false; break; }
			while (\true) {
				$res_86 = $result;
				$pos_86 = $this->pos;
				$_85 = \null;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_85 = \false; break; }
					if (\substr($this->string,$this->pos,1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_85 = \false; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_85 = \false; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "items" );
					}
					else { $_85 = \false; break; }
					$_85 = \true; break;
				}
				while(0);
				if( $_85 === \false) {
					$result = $res_86;
					$this->pos = $pos_86;
					unset( $res_86 );
					unset( $pos_86 );
					break;
				}
			}
			$_87 = \true; break;
		}
		while(0);
		if( $_87 === \false) {
			$result = $res_88;
			$this->pos = $pos_88;
			unset( $res_88 );
			unset( $pos_88 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_95 = \false; break; }
		$res_93 = $result;
		$pos_93 = $this->pos;
		$_92 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_92 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_92 = \false; break; }
			$_92 = \true; break;
		}
		while(0);
		if( $_92 === \false) {
			$result = $res_93;
			$this->pos = $pos_93;
			unset( $res_93 );
			unset( $pos_93 );
		}
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_95 = \false; break; }
		$_95 = \true; break;
	}
	while(0);
	if( $_95 === \true ) { return $this->finalise($result); }
	if( $_95 === \false) { return \false; }
}


/* Value: skip:Literal | skip:UnaryOpVariable | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, \null);
	$_108 = \null;
	do {
		$res_97 = $result;
		$pos_97 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_108 = \true; break;
		}
		$result = $res_97;
		$this->pos = $pos_97;
		$_106 = \null;
		do {
			$res_99 = $result;
			$pos_99 = $this->pos;
			$matcher = 'match_'.'UnaryOpVariable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_106 = \true; break;
			}
			$result = $res_99;
			$this->pos = $pos_99;
			$_104 = \null;
			do {
				$res_101 = $result;
				$pos_101 = $this->pos;
				$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_104 = \true; break;
				}
				$result = $res_101;
				$this->pos = $pos_101;
				$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_104 = \true; break;
				}
				$result = $res_101;
				$this->pos = $pos_101;
				$_104 = \false; break;
			}
			while(0);
			if( $_104 === \true ) { $_106 = \true; break; }
			$result = $res_99;
			$this->pos = $pos_99;
			$_106 = \false; break;
		}
		while(0);
		if( $_106 === \true ) { $_108 = \true; break; }
		$result = $res_97;
		$this->pos = $pos_97;
		$_108 = \false; break;
	}
	while(0);
	if( $_108 === \true ) { return $this->finalise($result); }
	if( $_108 === \false) { return \false; }
}


/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = array('VariableVector');
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, \null);
	$_112 = \null;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_112 = \false; break; }
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "vector" );
		}
		else { $_112 = \false; break; }
		$_112 = \true; break;
	}
	while(0);
	if( $_112 === \true ) { return $this->finalise($result); }
	if( $_112 === \false) { return \false; }
}


/* Vector: ( "[" __ ( arrayKey:Expression | arrayKey:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = array('Vector');
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule, \null);
	$_128 = \null;
	do {
		$_125 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_125 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_125 = \false; break; }
			$_121 = \null;
			do {
				$_119 = \null;
				do {
					$res_116 = $result;
					$pos_116 = $this->pos;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "arrayKey" );
						$_119 = \true; break;
					}
					$result = $res_116;
					$this->pos = $pos_116;
					$matcher = 'match_'.'Nothing'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "arrayKey" );
						$_119 = \true; break;
					}
					$result = $res_116;
					$this->pos = $pos_116;
					$_119 = \false; break;
				}
				while(0);
				if( $_119 === \false) { $_121 = \false; break; }
				$_121 = \true; break;
			}
			while(0);
			if( $_121 === \false) { $_125 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_125 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_125 = \false; break; }
			$_125 = \true; break;
		}
		while(0);
		if( $_125 === \false) { $_128 = \false; break; }
		$res_127 = $result;
		$pos_127 = $this->pos;
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "vector" );
		}
		else {
			$result = $res_127;
			$this->pos = $pos_127;
			unset( $res_127 );
			unset( $pos_127 );
		}
		$_128 = \true; break;
	}
	while(0);
	if( $_128 === \true ) { return $this->finalise($result); }
	if( $_128 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = array('Mutable');
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_133 = \null;
	do {
		$res_130 = $result;
		$pos_130 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_133 = \true; break;
		}
		$result = $res_130;
		$this->pos = $pos_130;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_133 = \true; break;
		}
		$result = $res_130;
		$this->pos = $pos_130;
		$_133 = \false; break;
	}
	while(0);
	if( $_133 === \true ) { return $this->finalise($result); }
	if( $_133 === \false) { return \false; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = array('ObjectResolutionOperator');
function match_ObjectResolutionOperator ($stack = []) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NotOperator: "!" */
protected $match_NotOperator_typestack = array('NotOperator');
function match_NotOperator ($stack = []) {
	$matchrule = "NotOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '!') {
		$this->pos += 1;
		$result["text"] .= '!';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = []) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_140 = \null;
	do {
		$res_137 = $result;
		$pos_137 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_140 = \true; break;
		}
		$result = $res_137;
		$this->pos = $pos_137;
		if (\substr($this->string,$this->pos,1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_140 = \true; break;
		}
		$result = $res_137;
		$this->pos = $pos_137;
		$_140 = \false; break;
	}
	while(0);
	if( $_140 === \true ) { return $this->finalise($result); }
	if( $_140 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_145 = \null;
	do {
		$res_142 = $result;
		$pos_142 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_145 = \true; break;
		}
		$result = $res_142;
		$this->pos = $pos_142;
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_145 = \true; break;
		}
		$result = $res_142;
		$this->pos = $pos_142;
		$_145 = \false; break;
	}
	while(0);
	if( $_145 === \true ) { return $this->finalise($result); }
	if( $_145 === \false) { return \false; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = []) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = []) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_167 = \null;
	do {
		$res_148 = $result;
		$pos_148 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== \false) {
			$result["text"] .= $subres;
			$_167 = \true; break;
		}
		$result = $res_148;
		$this->pos = $pos_148;
		$_165 = \null;
		do {
			$res_150 = $result;
			$pos_150 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== \false) {
				$result["text"] .= $subres;
				$_165 = \true; break;
			}
			$result = $res_150;
			$this->pos = $pos_150;
			$_163 = \null;
			do {
				$res_152 = $result;
				$pos_152 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== \false) {
					$result["text"] .= $subres;
					$_163 = \true; break;
				}
				$result = $res_152;
				$this->pos = $pos_152;
				$_161 = \null;
				do {
					$res_154 = $result;
					$pos_154 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== \false) {
						$result["text"] .= $subres;
						$_161 = \true; break;
					}
					$result = $res_154;
					$this->pos = $pos_154;
					$_159 = \null;
					do {
						$res_156 = $result;
						$pos_156 = $this->pos;
						if (\substr($this->string,$this->pos,1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_159 = \true; break;
						}
						$result = $res_156;
						$this->pos = $pos_156;
						if (\substr($this->string,$this->pos,1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_159 = \true; break;
						}
						$result = $res_156;
						$this->pos = $pos_156;
						$_159 = \false; break;
					}
					while(0);
					if( $_159 === \true ) { $_161 = \true; break; }
					$result = $res_154;
					$this->pos = $pos_154;
					$_161 = \false; break;
				}
				while(0);
				if( $_161 === \true ) { $_163 = \true; break; }
				$result = $res_152;
				$this->pos = $pos_152;
				$_163 = \false; break;
			}
			while(0);
			if( $_163 === \true ) { $_165 = \true; break; }
			$result = $res_150;
			$this->pos = $pos_150;
			$_165 = \false; break;
		}
		while(0);
		if( $_165 === \true ) { $_167 = \true; break; }
		$result = $res_148;
		$this->pos = $pos_148;
		$_167 = \false; break;
	}
	while(0);
	if( $_167 === \true ) { return $this->finalise($result); }
	if( $_167 === \false) { return \false; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = []) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_172 = \null;
	do {
		$res_169 = $result;
		$pos_169 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== \false) {
			$result["text"] .= $subres;
			$_172 = \true; break;
		}
		$result = $res_169;
		$this->pos = $pos_169;
		if (( $subres = $this->literal( '--' ) ) !== \false) {
			$result["text"] .= $subres;
			$_172 = \true; break;
		}
		$result = $res_169;
		$this->pos = $pos_169;
		$_172 = \false; break;
	}
	while(0);
	if( $_172 === \true ) { return $this->finalise($result); }
	if( $_172 === \false) { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, \null);
	$_185 = \null;
	do {
		$res_174 = $result;
		$pos_174 = $this->pos;
		$matcher = 'match_'.'AnonymousFunction'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_185 = \true; break;
		}
		$result = $res_174;
		$this->pos = $pos_174;
		$_183 = \null;
		do {
			$res_176 = $result;
			$pos_176 = $this->pos;
			$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_183 = \true; break;
			}
			$result = $res_176;
			$this->pos = $pos_176;
			$_181 = \null;
			do {
				$res_178 = $result;
				$pos_178 = $this->pos;
				$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_181 = \true; break;
				}
				$result = $res_178;
				$this->pos = $pos_178;
				$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_181 = \true; break;
				}
				$result = $res_178;
				$this->pos = $pos_178;
				$_181 = \false; break;
			}
			while(0);
			if( $_181 === \true ) { $_183 = \true; break; }
			$result = $res_176;
			$this->pos = $pos_176;
			$_183 = \false; break;
		}
		while(0);
		if( $_183 === \true ) { $_185 = \true; break; }
		$result = $res_174;
		$this->pos = $pos_174;
		$_185 = \false; break;
	}
	while(0);
	if( $_185 === \true ) { return $this->finalise($result); }
	if( $_185 === \false) { return \false; }
}


/* Comparison: left:Addition __ op:ComparisonOperator __ right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, \null);
	$_192 = \null;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_192 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_192 = \false; break; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "op" );
		}
		else { $_192 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_192 = \false; break; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_192 = \false; break; }
		$_192 = \true; break;
	}
	while(0);
	if( $_192 === \true ) { return $this->finalise($result); }
	if( $_192 === \false) { return \false; }
}


/* Assignment: left:Mutable __ op:AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, \null);
	$_199 = \null;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_199 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_199 = \false; break; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "op" );
		}
		else { $_199 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_199 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_199 = \false; break; }
		$_199 = \true; break;
	}
	while(0);
	if( $_199 === \true ) { return $this->finalise($result); }
	if( $_199 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_208 = \null;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_208 = \false; break; }
		while (\true) {
			$res_207 = $result;
			$pos_207 = $this->pos;
			$_206 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_206 = \false; break; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_206 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_206 = \false; break; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_206 = \false; break; }
				$_206 = \true; break;
			}
			while(0);
			if( $_206 === \false) {
				$result = $res_207;
				$this->pos = $pos_207;
				unset( $res_207 );
				unset( $pos_207 );
				break;
			}
		}
		$_208 = \true; break;
	}
	while(0);
	if( $_208 === \true ) { return $this->finalise($result); }
	if( $_208 === \false) { return \false; }
}


/* Multiplication: operands:Negation ( __ ops:MultiplyOperator __ operands:Negation)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null);
	$_217 = \null;
	do {
		$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_217 = \false; break; }
		while (\true) {
			$res_216 = $result;
			$pos_216 = $this->pos;
			$_215 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_215 = \false; break; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_215 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_215 = \false; break; }
				$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_215 = \false; break; }
				$_215 = \true; break;
			}
			while(0);
			if( $_215 === \false) {
				$result = $res_216;
				$this->pos = $pos_216;
				unset( $res_216 );
				unset( $pos_216 );
				break;
			}
		}
		$_217 = \true; break;
	}
	while(0);
	if( $_217 === \true ) { return $this->finalise($result); }
	if( $_217 === \false) { return \false; }
}


/* Negation: not:NotOperator? core:Operand */
protected $match_Negation_typestack = array('Negation');
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule, \null);
	$_221 = \null;
	do {
		$res_219 = $result;
		$pos_219 = $this->pos;
		$matcher = 'match_'.'NotOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "not" );
		}
		else {
			$result = $res_219;
			$this->pos = $pos_219;
			unset( $res_219 );
			unset( $pos_219 );
		}
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_221 = \false; break; }
		$_221 = \true; break;
	}
	while(0);
	if( $_221 === \true ) { return $this->finalise($result); }
	if( $_221 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null);
	$_241 = \null;
	do {
		$res_223 = $result;
		$pos_223 = $this->pos;
		$_238 = \null;
		do {
			$_235 = \null;
			do {
				$_233 = \null;
				do {
					$res_224 = $result;
					$pos_224 = $this->pos;
					$_230 = \null;
					do {
						if (\substr($this->string,$this->pos,1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_230 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_230 = \false; break; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "core" );
						}
						else { $_230 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_230 = \false; break; }
						if (\substr($this->string,$this->pos,1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_230 = \false; break; }
						$_230 = \true; break;
					}
					while(0);
					if( $_230 === \true ) { $_233 = \true; break; }
					$result = $res_224;
					$this->pos = $pos_224;
					$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_233 = \true; break;
					}
					$result = $res_224;
					$this->pos = $pos_224;
					$_233 = \false; break;
				}
				while(0);
				if( $_233 === \false) { $_235 = \false; break; }
				$_235 = \true; break;
			}
			while(0);
			if( $_235 === \false) { $_238 = \false; break; }
			$res_237 = $result;
			$pos_237 = $this->pos;
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
			}
			else {
				$result = $res_237;
				$this->pos = $pos_237;
				unset( $res_237 );
				unset( $pos_237 );
			}
			$_238 = \true; break;
		}
		while(0);
		if( $_238 === \true ) { $_241 = \true; break; }
		$result = $res_223;
		$this->pos = $pos_223;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_241 = \true; break;
		}
		$result = $res_223;
		$this->pos = $pos_223;
		$_241 = \false; break;
	}
	while(0);
	if( $_241 === \true ) { return $this->finalise($result); }
	if( $_241 === \false) { return \false; }
}


/* Chain: ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = array('Chain');
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null);
	$_255 = \null;
	do {
		$_252 = \null;
		do {
			$_250 = \null;
			do {
				$res_243 = $result;
				$pos_243 = $this->pos;
				$matcher = 'match_'.'Dereference'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_250 = \true; break;
				}
				$result = $res_243;
				$this->pos = $pos_243;
				$_248 = \null;
				do {
					$res_245 = $result;
					$pos_245 = $this->pos;
					$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_248 = \true; break;
					}
					$result = $res_245;
					$this->pos = $pos_245;
					$matcher = 'match_'.'ChainedFunction'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_248 = \true; break;
					}
					$result = $res_245;
					$this->pos = $pos_245;
					$_248 = \false; break;
				}
				while(0);
				if( $_248 === \true ) { $_250 = \true; break; }
				$result = $res_243;
				$this->pos = $pos_243;
				$_250 = \false; break;
			}
			while(0);
			if( $_250 === \false) { $_252 = \false; break; }
			$_252 = \true; break;
		}
		while(0);
		if( $_252 === \false) { $_255 = \false; break; }
		$res_254 = $result;
		$pos_254 = $this->pos;
		$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "chain" );
		}
		else {
			$result = $res_254;
			$this->pos = $pos_254;
			unset( $res_254 );
			unset( $pos_254 );
		}
		$_255 = \true; break;
	}
	while(0);
	if( $_255 === \true ) { return $this->finalise($result); }
	if( $_255 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = array('Dereference');
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule, \null);
	$_262 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_262 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_262 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "key" );
		}
		else { $_262 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_262 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_262 = \false; break; }
		$_262 = \true; break;
	}
	while(0);
	if( $_262 === \true ) { return $this->finalise($result); }
	if( $_262 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = array('Invocation');
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule, \null);
	$_269 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_269 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_269 = \false; break; }
		$res_266 = $result;
		$pos_266 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_266;
			$this->pos = $pos_266;
			unset( $res_266 );
			unset( $pos_266 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_269 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_269 = \false; break; }
		$_269 = \true; break;
	}
	while(0);
	if( $_269 === \true ) { return $this->finalise($result); }
	if( $_269 === \false) { return \false; }
}


/* ChainedFunction: ObjectResolutionOperator fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = array('ChainedFunction');
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_274 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_274 = \false; break; }
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "fn" );
		}
		else { $_274 = \false; break; }
		$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "invo" );
		}
		else { $_274 = \false; break; }
		$_274 = \true; break;
	}
	while(0);
	if( $_274 === \true ) { return $this->finalise($result); }
	if( $_274 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = array('ArgumentList');
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_283 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else { $_283 = \false; break; }
		while (\true) {
			$res_282 = $result;
			$pos_282 = $this->pos;
			$_281 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_281 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_281 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_281 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "args" );
				}
				else { $_281 = \false; break; }
				$_281 = \true; break;
			}
			while(0);
			if( $_281 === \false) {
				$result = $res_282;
				$this->pos = $pos_282;
				unset( $res_282 );
				unset( $pos_282 );
				break;
			}
		}
		$_283 = \true; break;
	}
	while(0);
	if( $_283 === \true ) { return $this->finalise($result); }
	if( $_283 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_292 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_292 = \false; break; }
		while (\true) {
			$res_291 = $result;
			$pos_291 = $this->pos;
			$_290 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_290 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_290 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_290 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_290 = \false; break; }
				$_290 = \true; break;
			}
			while(0);
			if( $_290 === \false) {
				$result = $res_291;
				$this->pos = $pos_291;
				unset( $res_291 );
				unset( $pos_291 );
				break;
			}
		}
		$_292 = \true; break;
	}
	while(0);
	if( $_292 === \true ) { return $this->finalise($result); }
	if( $_292 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_305 = \null;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_305 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_305 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "function" );
		}
		else { $_305 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_305 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_305 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_305 = \false; break; }
		$res_300 = $result;
		$pos_300 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_300;
			$this->pos = $pos_300;
			unset( $res_300 );
			unset( $pos_300 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_305 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_305 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_305 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "body" );
		}
		else { $_305 = \false; break; }
		$_305 = \true; break;
	}
	while(0);
	if( $_305 === \true ) { return $this->finalise($result); }
	if( $_305 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_318 = \null;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_318 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_318 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_318 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_318 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_318 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_318 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_318 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_318 = \false; break; }
		$_316 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_316 = \false; break; }
			$_316 = \true; break;
		}
		while(0);
		if( $_316 === \false) { $_318 = \false; break; }
		$_318 = \true; break;
	}
	while(0);
	if( $_318 === \true ) { return $this->finalise($result); }
	if( $_318 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_331 = \null;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_331 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_331 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_331 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_331 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_331 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_331 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_331 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_331 = \false; break; }
		$_329 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_329 = \false; break; }
			$_329 = \true; break;
		}
		while(0);
		if( $_329 === \false) { $_331 = \false; break; }
		$_331 = \true; break;
	}
	while(0);
	if( $_331 === \true ) { return $this->finalise($result); }
	if( $_331 === \false) { return \false; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = []) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_348 = \null;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_348 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_348 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_348 = \false; break; }
		if (( $subres = $this->literal( 'as' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_348 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "item" );
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_348 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_348 = \false; break; }
		$_346 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_346 = \false; break; }
			$_346 = \true; break;
		}
		while(0);
		if( $_346 === \false) { $_348 = \false; break; }
		$_348 = \true; break;
	}
	while(0);
	if( $_348 === \true ) { return $this->finalise($result); }
	if( $_348 === \false) { return \false; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_353 = \null;
	do {
		$res_350 = $result;
		$pos_350 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_353 = \true; break;
		}
		$result = $res_350;
		$this->pos = $pos_350;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_353 = \true; break;
		}
		$result = $res_350;
		$this->pos = $pos_350;
		$_353 = \false; break;
	}
	while(0);
	if( $_353 === \true ) { return $this->finalise($result); }
	if( $_353 === \false) { return \false; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = []) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_358 = \null;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_358 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_358 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_358 = \false; break; }
		$_358 = \true; break;
	}
	while(0);
	if( $_358 === \true ) { return $this->finalise($result); }
	if( $_358 === \false) { return \false; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_365 = \null;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_365 = \false; break; }
		$res_364 = $result;
		$pos_364 = $this->pos;
		$_363 = \null;
		do {
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			else { $_363 = \false; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_363 = \false; break; }
			$_363 = \true; break;
		}
		while(0);
		if( $_363 === \false) {
			$result = $res_364;
			$this->pos = $pos_364;
			unset( $res_364 );
			unset( $pos_364 );
		}
		$_365 = \true; break;
	}
	while(0);
	if( $_365 === \true ) { return $this->finalise($result); }
	if( $_365 === \false) { return \false; }
}


/* BlockStatements: &/[A-Za-z]/ ( skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition ) */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_383 = \null;
	do {
		$res_367 = $result;
		$pos_367 = $this->pos;
		if (( $subres = $this->rx( '/[A-Za-z]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_367;
			$this->pos = $pos_367;
		}
		else {
			$result = $res_367;
			$this->pos = $pos_367;
			$_383 = \false; break;
		}
		$_381 = \null;
		do {
			$_379 = \null;
			do {
				$res_368 = $result;
				$pos_368 = $this->pos;
				$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_379 = \true; break;
				}
				$result = $res_368;
				$this->pos = $pos_368;
				$_377 = \null;
				do {
					$res_370 = $result;
					$pos_370 = $this->pos;
					$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_377 = \true; break;
					}
					$result = $res_370;
					$this->pos = $pos_370;
					$_375 = \null;
					do {
						$res_372 = $result;
						$pos_372 = $this->pos;
						$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_375 = \true; break;
						}
						$result = $res_372;
						$this->pos = $pos_372;
						$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_375 = \true; break;
						}
						$result = $res_372;
						$this->pos = $pos_372;
						$_375 = \false; break;
					}
					while(0);
					if( $_375 === \true ) { $_377 = \true; break; }
					$result = $res_370;
					$this->pos = $pos_370;
					$_377 = \false; break;
				}
				while(0);
				if( $_377 === \true ) { $_379 = \true; break; }
				$result = $res_368;
				$this->pos = $pos_368;
				$_379 = \false; break;
			}
			while(0);
			if( $_379 === \false) { $_381 = \false; break; }
			$_381 = \true; break;
		}
		while(0);
		if( $_381 === \false) { $_383 = \false; break; }
		$_383 = \true; break;
	}
	while(0);
	if( $_383 === \true ) { return $this->finalise($result); }
	if( $_383 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_397 = \null;
	do {
		$res_385 = $result;
		$pos_385 = $this->pos;
		if (( $subres = $this->rx( '/[\s\{\};]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_385;
			$this->pos = $pos_385;
			$_397 = \false; break;
		}
		else {
			$result = $res_385;
			$this->pos = $pos_385;
		}
		$_395 = \null;
		do {
			$_393 = \null;
			do {
				$res_386 = $result;
				$pos_386 = $this->pos;
				$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_393 = \true; break;
				}
				$result = $res_386;
				$this->pos = $pos_386;
				$_391 = \null;
				do {
					$res_388 = $result;
					$pos_388 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_391 = \true; break;
					}
					$result = $res_388;
					$this->pos = $pos_388;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_391 = \true; break;
					}
					$result = $res_388;
					$this->pos = $pos_388;
					$_391 = \false; break;
				}
				while(0);
				if( $_391 === \true ) { $_393 = \true; break; }
				$result = $res_386;
				$this->pos = $pos_386;
				$_393 = \false; break;
			}
			while(0);
			if( $_393 === \false) { $_395 = \false; break; }
			$_395 = \true; break;
		}
		while(0);
		if( $_395 === \false) { $_397 = \false; break; }
		$_397 = \true; break;
	}
	while(0);
	if( $_397 === \true ) { return $this->finalise($result); }
	if( $_397 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null);
	$_405 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_405 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_405 = \false; break; }
		$res_403 = $result;
		$pos_403 = $this->pos;
		$_402 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_402 = \false; break; }
			$_402 = \true; break;
		}
		while(0);
		if( $_402 === \false) {
			$result = $res_403;
			$this->pos = $pos_403;
			unset( $res_403 );
			unset( $pos_403 );
		}
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_405 = \false; break; }
		$_405 = \true; break;
	}
	while(0);
	if( $_405 === \true ) { return $this->finalise($result); }
	if( $_405 === \false) { return \false; }
}


/* __: / [\s\n]* / */
protected $match____typestack = array('__');
function match___ ($stack = []) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ [\s\n]* /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = array('NL');
function match_NL ($stack = []) {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ (?:\/\/[^\n]*)?\n /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* SEP: ";" | NL */
protected $match_SEP_typestack = array('SEP');
function match_SEP ($stack = []) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule, \null);
	$_412 = \null;
	do {
		$res_409 = $result;
		$pos_409 = $this->pos;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_412 = \true; break;
		}
		$result = $res_409;
		$this->pos = $pos_409;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres );
			$_412 = \true; break;
		}
		$result = $res_409;
		$this->pos = $pos_409;
		$_412 = \false; break;
	}
	while(0);
	if( $_412 === \true ) { return $this->finalise($result); }
	if( $_412 === \false) { return \false; }
}


/* Program: ( !/$/ __ Statement? > SEP )+ __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null);
	$_422 = \null;
	do {
		$count = 0;
		while (\true) {
			$res_420 = $result;
			$pos_420 = $this->pos;
			$_419 = \null;
			do {
				$res_414 = $result;
				$pos_414 = $this->pos;
				if (( $subres = $this->rx( '/$/' ) ) !== \false) {
					$result["text"] .= $subres;
					$result = $res_414;
					$this->pos = $pos_414;
					$_419 = \false; break;
				}
				else {
					$result = $res_414;
					$this->pos = $pos_414;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_419 = \false; break; }
				$res_416 = $result;
				$pos_416 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else {
					$result = $res_416;
					$this->pos = $pos_416;
					unset( $res_416 );
					unset( $pos_416 );
				}
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_419 = \false; break; }
				$_419 = \true; break;
			}
			while(0);
			if( $_419 === \false) {
				$result = $res_420;
				$this->pos = $pos_420;
				unset( $res_420 );
				unset( $pos_420 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_422 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_422 = \false; break; }
		$_422 = \true; break;
	}
	while(0);
	if( $_422 === \true ) { return $this->finalise($result); }
	if( $_422 === \false) { return \false; }
}




}
