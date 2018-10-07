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


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = []) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_139 = \null;
	do {
		$res_136 = $result;
		$pos_136 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_139 = \true; break;
		}
		$result = $res_136;
		$this->pos = $pos_136;
		if (\substr($this->string,$this->pos,1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_139 = \true; break;
		}
		$result = $res_136;
		$this->pos = $pos_136;
		$_139 = \false; break;
	}
	while(0);
	if( $_139 === \true ) { return $this->finalise($result); }
	if( $_139 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_144 = \null;
	do {
		$res_141 = $result;
		$pos_141 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_144 = \true; break;
		}
		$result = $res_141;
		$this->pos = $pos_141;
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_144 = \true; break;
		}
		$result = $res_141;
		$this->pos = $pos_141;
		$_144 = \false; break;
	}
	while(0);
	if( $_144 === \true ) { return $this->finalise($result); }
	if( $_144 === \false) { return \false; }
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
	$_166 = \null;
	do {
		$res_147 = $result;
		$pos_147 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== \false) {
			$result["text"] .= $subres;
			$_166 = \true; break;
		}
		$result = $res_147;
		$this->pos = $pos_147;
		$_164 = \null;
		do {
			$res_149 = $result;
			$pos_149 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== \false) {
				$result["text"] .= $subres;
				$_164 = \true; break;
			}
			$result = $res_149;
			$this->pos = $pos_149;
			$_162 = \null;
			do {
				$res_151 = $result;
				$pos_151 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== \false) {
					$result["text"] .= $subres;
					$_162 = \true; break;
				}
				$result = $res_151;
				$this->pos = $pos_151;
				$_160 = \null;
				do {
					$res_153 = $result;
					$pos_153 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== \false) {
						$result["text"] .= $subres;
						$_160 = \true; break;
					}
					$result = $res_153;
					$this->pos = $pos_153;
					$_158 = \null;
					do {
						$res_155 = $result;
						$pos_155 = $this->pos;
						if (\substr($this->string,$this->pos,1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_158 = \true; break;
						}
						$result = $res_155;
						$this->pos = $pos_155;
						if (\substr($this->string,$this->pos,1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_158 = \true; break;
						}
						$result = $res_155;
						$this->pos = $pos_155;
						$_158 = \false; break;
					}
					while(0);
					if( $_158 === \true ) { $_160 = \true; break; }
					$result = $res_153;
					$this->pos = $pos_153;
					$_160 = \false; break;
				}
				while(0);
				if( $_160 === \true ) { $_162 = \true; break; }
				$result = $res_151;
				$this->pos = $pos_151;
				$_162 = \false; break;
			}
			while(0);
			if( $_162 === \true ) { $_164 = \true; break; }
			$result = $res_149;
			$this->pos = $pos_149;
			$_164 = \false; break;
		}
		while(0);
		if( $_164 === \true ) { $_166 = \true; break; }
		$result = $res_147;
		$this->pos = $pos_147;
		$_166 = \false; break;
	}
	while(0);
	if( $_166 === \true ) { return $this->finalise($result); }
	if( $_166 === \false) { return \false; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = []) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_171 = \null;
	do {
		$res_168 = $result;
		$pos_168 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== \false) {
			$result["text"] .= $subres;
			$_171 = \true; break;
		}
		$result = $res_168;
		$this->pos = $pos_168;
		if (( $subres = $this->literal( '--' ) ) !== \false) {
			$result["text"] .= $subres;
			$_171 = \true; break;
		}
		$result = $res_168;
		$this->pos = $pos_168;
		$_171 = \false; break;
	}
	while(0);
	if( $_171 === \true ) { return $this->finalise($result); }
	if( $_171 === \false) { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, \null);
	$_184 = \null;
	do {
		$res_173 = $result;
		$pos_173 = $this->pos;
		$matcher = 'match_'.'AnonymousFunction'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_184 = \true; break;
		}
		$result = $res_173;
		$this->pos = $pos_173;
		$_182 = \null;
		do {
			$res_175 = $result;
			$pos_175 = $this->pos;
			$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_182 = \true; break;
			}
			$result = $res_175;
			$this->pos = $pos_175;
			$_180 = \null;
			do {
				$res_177 = $result;
				$pos_177 = $this->pos;
				$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_180 = \true; break;
				}
				$result = $res_177;
				$this->pos = $pos_177;
				$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_180 = \true; break;
				}
				$result = $res_177;
				$this->pos = $pos_177;
				$_180 = \false; break;
			}
			while(0);
			if( $_180 === \true ) { $_182 = \true; break; }
			$result = $res_175;
			$this->pos = $pos_175;
			$_182 = \false; break;
		}
		while(0);
		if( $_182 === \true ) { $_184 = \true; break; }
		$result = $res_173;
		$this->pos = $pos_173;
		$_184 = \false; break;
	}
	while(0);
	if( $_184 === \true ) { return $this->finalise($result); }
	if( $_184 === \false) { return \false; }
}


/* Comparison: left:Addition __ op:ComparisonOperator __ right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, \null);
	$_191 = \null;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_191 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_191 = \false; break; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "op" );
		}
		else { $_191 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_191 = \false; break; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_191 = \false; break; }
		$_191 = \true; break;
	}
	while(0);
	if( $_191 === \true ) { return $this->finalise($result); }
	if( $_191 === \false) { return \false; }
}


/* Assignment: left:Mutable __ op:AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, \null);
	$_198 = \null;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_198 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_198 = \false; break; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "op" );
		}
		else { $_198 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_198 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_198 = \false; break; }
		$_198 = \true; break;
	}
	while(0);
	if( $_198 === \true ) { return $this->finalise($result); }
	if( $_198 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_207 = \null;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_207 = \false; break; }
		while (\true) {
			$res_206 = $result;
			$pos_206 = $this->pos;
			$_205 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_205 = \false; break; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_205 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_205 = \false; break; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_205 = \false; break; }
				$_205 = \true; break;
			}
			while(0);
			if( $_205 === \false) {
				$result = $res_206;
				$this->pos = $pos_206;
				unset( $res_206 );
				unset( $pos_206 );
				break;
			}
		}
		$_207 = \true; break;
	}
	while(0);
	if( $_207 === \true ) { return $this->finalise($result); }
	if( $_207 === \false) { return \false; }
}


/* Multiplication: operands:Operand ( __ ops:MultiplyOperator __ operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null);
	$_216 = \null;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_216 = \false; break; }
		while (\true) {
			$res_215 = $result;
			$pos_215 = $this->pos;
			$_214 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_214 = \false; break; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_214 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_214 = \false; break; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_214 = \false; break; }
				$_214 = \true; break;
			}
			while(0);
			if( $_214 === \false) {
				$result = $res_215;
				$this->pos = $pos_215;
				unset( $res_215 );
				unset( $pos_215 );
				break;
			}
		}
		$_216 = \true; break;
	}
	while(0);
	if( $_216 === \true ) { return $this->finalise($result); }
	if( $_216 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null);
	$_236 = \null;
	do {
		$res_218 = $result;
		$pos_218 = $this->pos;
		$_233 = \null;
		do {
			$_230 = \null;
			do {
				$_228 = \null;
				do {
					$res_219 = $result;
					$pos_219 = $this->pos;
					$_225 = \null;
					do {
						if (\substr($this->string,$this->pos,1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_225 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_225 = \false; break; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "core" );
						}
						else { $_225 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_225 = \false; break; }
						if (\substr($this->string,$this->pos,1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_225 = \false; break; }
						$_225 = \true; break;
					}
					while(0);
					if( $_225 === \true ) { $_228 = \true; break; }
					$result = $res_219;
					$this->pos = $pos_219;
					$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_228 = \true; break;
					}
					$result = $res_219;
					$this->pos = $pos_219;
					$_228 = \false; break;
				}
				while(0);
				if( $_228 === \false) { $_230 = \false; break; }
				$_230 = \true; break;
			}
			while(0);
			if( $_230 === \false) { $_233 = \false; break; }
			$res_232 = $result;
			$pos_232 = $this->pos;
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
			}
			else {
				$result = $res_232;
				$this->pos = $pos_232;
				unset( $res_232 );
				unset( $pos_232 );
			}
			$_233 = \true; break;
		}
		while(0);
		if( $_233 === \true ) { $_236 = \true; break; }
		$result = $res_218;
		$this->pos = $pos_218;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_236 = \true; break;
		}
		$result = $res_218;
		$this->pos = $pos_218;
		$_236 = \false; break;
	}
	while(0);
	if( $_236 === \true ) { return $this->finalise($result); }
	if( $_236 === \false) { return \false; }
}


/* Chain: ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = array('Chain');
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null);
	$_250 = \null;
	do {
		$_247 = \null;
		do {
			$_245 = \null;
			do {
				$res_238 = $result;
				$pos_238 = $this->pos;
				$matcher = 'match_'.'Dereference'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_245 = \true; break;
				}
				$result = $res_238;
				$this->pos = $pos_238;
				$_243 = \null;
				do {
					$res_240 = $result;
					$pos_240 = $this->pos;
					$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_243 = \true; break;
					}
					$result = $res_240;
					$this->pos = $pos_240;
					$matcher = 'match_'.'ChainedFunction'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_243 = \true; break;
					}
					$result = $res_240;
					$this->pos = $pos_240;
					$_243 = \false; break;
				}
				while(0);
				if( $_243 === \true ) { $_245 = \true; break; }
				$result = $res_238;
				$this->pos = $pos_238;
				$_245 = \false; break;
			}
			while(0);
			if( $_245 === \false) { $_247 = \false; break; }
			$_247 = \true; break;
		}
		while(0);
		if( $_247 === \false) { $_250 = \false; break; }
		$res_249 = $result;
		$pos_249 = $this->pos;
		$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "chain" );
		}
		else {
			$result = $res_249;
			$this->pos = $pos_249;
			unset( $res_249 );
			unset( $pos_249 );
		}
		$_250 = \true; break;
	}
	while(0);
	if( $_250 === \true ) { return $this->finalise($result); }
	if( $_250 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = array('Dereference');
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule, \null);
	$_257 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_257 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_257 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "key" );
		}
		else { $_257 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_257 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_257 = \false; break; }
		$_257 = \true; break;
	}
	while(0);
	if( $_257 === \true ) { return $this->finalise($result); }
	if( $_257 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = array('Invocation');
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule, \null);
	$_264 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_264 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_264 = \false; break; }
		$res_261 = $result;
		$pos_261 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_261;
			$this->pos = $pos_261;
			unset( $res_261 );
			unset( $pos_261 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_264 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_264 = \false; break; }
		$_264 = \true; break;
	}
	while(0);
	if( $_264 === \true ) { return $this->finalise($result); }
	if( $_264 === \false) { return \false; }
}


/* ChainedFunction: ObjectResolutionOperator fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = array('ChainedFunction');
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_269 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_269 = \false; break; }
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "fn" );
		}
		else { $_269 = \false; break; }
		$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "invo" );
		}
		else { $_269 = \false; break; }
		$_269 = \true; break;
	}
	while(0);
	if( $_269 === \true ) { return $this->finalise($result); }
	if( $_269 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = array('ArgumentList');
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_278 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else { $_278 = \false; break; }
		while (\true) {
			$res_277 = $result;
			$pos_277 = $this->pos;
			$_276 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_276 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_276 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_276 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "args" );
				}
				else { $_276 = \false; break; }
				$_276 = \true; break;
			}
			while(0);
			if( $_276 === \false) {
				$result = $res_277;
				$this->pos = $pos_277;
				unset( $res_277 );
				unset( $pos_277 );
				break;
			}
		}
		$_278 = \true; break;
	}
	while(0);
	if( $_278 === \true ) { return $this->finalise($result); }
	if( $_278 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_287 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_287 = \false; break; }
		while (\true) {
			$res_286 = $result;
			$pos_286 = $this->pos;
			$_285 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_285 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_285 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_285 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_285 = \false; break; }
				$_285 = \true; break;
			}
			while(0);
			if( $_285 === \false) {
				$result = $res_286;
				$this->pos = $pos_286;
				unset( $res_286 );
				unset( $pos_286 );
				break;
			}
		}
		$_287 = \true; break;
	}
	while(0);
	if( $_287 === \true ) { return $this->finalise($result); }
	if( $_287 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_300 = \null;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_300 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_300 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "function" );
		}
		else { $_300 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_300 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_300 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_300 = \false; break; }
		$res_295 = $result;
		$pos_295 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_295;
			$this->pos = $pos_295;
			unset( $res_295 );
			unset( $pos_295 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_300 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_300 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_300 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "body" );
		}
		else { $_300 = \false; break; }
		$_300 = \true; break;
	}
	while(0);
	if( $_300 === \true ) { return $this->finalise($result); }
	if( $_300 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_313 = \null;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_313 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_313 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_313 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_313 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_313 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_313 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_313 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_313 = \false; break; }
		$_311 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_311 = \false; break; }
			$_311 = \true; break;
		}
		while(0);
		if( $_311 === \false) { $_313 = \false; break; }
		$_313 = \true; break;
	}
	while(0);
	if( $_313 === \true ) { return $this->finalise($result); }
	if( $_313 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_326 = \null;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_326 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_326 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_326 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_326 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_326 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_326 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_326 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_326 = \false; break; }
		$_324 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_324 = \false; break; }
			$_324 = \true; break;
		}
		while(0);
		if( $_324 === \false) { $_326 = \false; break; }
		$_326 = \true; break;
	}
	while(0);
	if( $_326 === \true ) { return $this->finalise($result); }
	if( $_326 === \false) { return \false; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = []) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_343 = \null;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_343 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_343 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_343 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_343 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_343 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_343 = \false; break; }
		if (( $subres = $this->literal( 'as' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_343 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_343 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "item" );
		}
		else { $_343 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_343 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_343 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_343 = \false; break; }
		$_341 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_341 = \false; break; }
			$_341 = \true; break;
		}
		while(0);
		if( $_341 === \false) { $_343 = \false; break; }
		$_343 = \true; break;
	}
	while(0);
	if( $_343 === \true ) { return $this->finalise($result); }
	if( $_343 === \false) { return \false; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_348 = \null;
	do {
		$res_345 = $result;
		$pos_345 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_348 = \true; break;
		}
		$result = $res_345;
		$this->pos = $pos_345;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_348 = \true; break;
		}
		$result = $res_345;
		$this->pos = $pos_345;
		$_348 = \false; break;
	}
	while(0);
	if( $_348 === \true ) { return $this->finalise($result); }
	if( $_348 === \false) { return \false; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = []) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_353 = \null;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_353 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_353 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_353 = \false; break; }
		$_353 = \true; break;
	}
	while(0);
	if( $_353 === \true ) { return $this->finalise($result); }
	if( $_353 === \false) { return \false; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_360 = \null;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_360 = \false; break; }
		$res_359 = $result;
		$pos_359 = $this->pos;
		$_358 = \null;
		do {
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
		if( $_358 === \false) {
			$result = $res_359;
			$this->pos = $pos_359;
			unset( $res_359 );
			unset( $pos_359 );
		}
		$_360 = \true; break;
	}
	while(0);
	if( $_360 === \true ) { return $this->finalise($result); }
	if( $_360 === \false) { return \false; }
}


/* BlockStatements: &/[A-Za-z]/ ( skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition ) */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_378 = \null;
	do {
		$res_362 = $result;
		$pos_362 = $this->pos;
		if (( $subres = $this->rx( '/[A-Za-z]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_362;
			$this->pos = $pos_362;
		}
		else {
			$result = $res_362;
			$this->pos = $pos_362;
			$_378 = \false; break;
		}
		$_376 = \null;
		do {
			$_374 = \null;
			do {
				$res_363 = $result;
				$pos_363 = $this->pos;
				$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_374 = \true; break;
				}
				$result = $res_363;
				$this->pos = $pos_363;
				$_372 = \null;
				do {
					$res_365 = $result;
					$pos_365 = $this->pos;
					$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_372 = \true; break;
					}
					$result = $res_365;
					$this->pos = $pos_365;
					$_370 = \null;
					do {
						$res_367 = $result;
						$pos_367 = $this->pos;
						$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_370 = \true; break;
						}
						$result = $res_367;
						$this->pos = $pos_367;
						$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_370 = \true; break;
						}
						$result = $res_367;
						$this->pos = $pos_367;
						$_370 = \false; break;
					}
					while(0);
					if( $_370 === \true ) { $_372 = \true; break; }
					$result = $res_365;
					$this->pos = $pos_365;
					$_372 = \false; break;
				}
				while(0);
				if( $_372 === \true ) { $_374 = \true; break; }
				$result = $res_363;
				$this->pos = $pos_363;
				$_374 = \false; break;
			}
			while(0);
			if( $_374 === \false) { $_376 = \false; break; }
			$_376 = \true; break;
		}
		while(0);
		if( $_376 === \false) { $_378 = \false; break; }
		$_378 = \true; break;
	}
	while(0);
	if( $_378 === \true ) { return $this->finalise($result); }
	if( $_378 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_392 = \null;
	do {
		$res_380 = $result;
		$pos_380 = $this->pos;
		if (( $subres = $this->rx( '/[\s\{\};]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_380;
			$this->pos = $pos_380;
			$_392 = \false; break;
		}
		else {
			$result = $res_380;
			$this->pos = $pos_380;
		}
		$_390 = \null;
		do {
			$_388 = \null;
			do {
				$res_381 = $result;
				$pos_381 = $this->pos;
				$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_388 = \true; break;
				}
				$result = $res_381;
				$this->pos = $pos_381;
				$_386 = \null;
				do {
					$res_383 = $result;
					$pos_383 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_386 = \true; break;
					}
					$result = $res_383;
					$this->pos = $pos_383;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_386 = \true; break;
					}
					$result = $res_383;
					$this->pos = $pos_383;
					$_386 = \false; break;
				}
				while(0);
				if( $_386 === \true ) { $_388 = \true; break; }
				$result = $res_381;
				$this->pos = $pos_381;
				$_388 = \false; break;
			}
			while(0);
			if( $_388 === \false) { $_390 = \false; break; }
			$_390 = \true; break;
		}
		while(0);
		if( $_390 === \false) { $_392 = \false; break; }
		$_392 = \true; break;
	}
	while(0);
	if( $_392 === \true ) { return $this->finalise($result); }
	if( $_392 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null);
	$_400 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_400 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_400 = \false; break; }
		$res_398 = $result;
		$pos_398 = $this->pos;
		$_397 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_397 = \false; break; }
			$_397 = \true; break;
		}
		while(0);
		if( $_397 === \false) {
			$result = $res_398;
			$this->pos = $pos_398;
			unset( $res_398 );
			unset( $pos_398 );
		}
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_400 = \false; break; }
		$_400 = \true; break;
	}
	while(0);
	if( $_400 === \true ) { return $this->finalise($result); }
	if( $_400 === \false) { return \false; }
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
	$_407 = \null;
	do {
		$res_404 = $result;
		$pos_404 = $this->pos;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_407 = \true; break;
		}
		$result = $res_404;
		$this->pos = $pos_404;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres );
			$_407 = \true; break;
		}
		$result = $res_404;
		$this->pos = $pos_404;
		$_407 = \false; break;
	}
	while(0);
	if( $_407 === \true ) { return $this->finalise($result); }
	if( $_407 === \false) { return \false; }
}


/* Program: ( !/$/ __ Statement? > SEP )+ __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null);
	$_417 = \null;
	do {
		$count = 0;
		while (\true) {
			$res_415 = $result;
			$pos_415 = $this->pos;
			$_414 = \null;
			do {
				$res_409 = $result;
				$pos_409 = $this->pos;
				if (( $subres = $this->rx( '/$/' ) ) !== \false) {
					$result["text"] .= $subres;
					$result = $res_409;
					$this->pos = $pos_409;
					$_414 = \false; break;
				}
				else {
					$result = $res_409;
					$this->pos = $pos_409;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_414 = \false; break; }
				$res_411 = $result;
				$pos_411 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else {
					$result = $res_411;
					$this->pos = $pos_411;
					unset( $res_411 );
					unset( $pos_411 );
				}
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_414 = \false; break; }
				$_414 = \true; break;
			}
			while(0);
			if( $_414 === \false) {
				$result = $res_415;
				$this->pos = $pos_415;
				unset( $res_415 );
				unset( $pos_415 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_417 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_417 = \false; break; }
		$_417 = \true; break;
	}
	while(0);
	if( $_417 === \true ) { return $this->finalise($result); }
	if( $_417 === \false) { return \false; }
}




}
