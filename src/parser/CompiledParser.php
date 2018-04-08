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


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:NullLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, \null);
	$_28 = \null;
	do {
		$res_13 = $result;
		$pos_13 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_28 = \true; break;
		}
		$result = $res_13;
		$this->pos = $pos_13;
		$_26 = \null;
		do {
			$res_15 = $result;
			$pos_15 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_26 = \true; break;
			}
			$result = $res_15;
			$this->pos = $pos_15;
			$_24 = \null;
			do {
				$res_17 = $result;
				$pos_17 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_24 = \true; break;
				}
				$result = $res_17;
				$this->pos = $pos_17;
				$_22 = \null;
				do {
					$res_19 = $result;
					$pos_19 = $this->pos;
					$matcher = 'match_'.'NullLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_22 = \true; break;
					}
					$result = $res_19;
					$this->pos = $pos_19;
					$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_22 = \true; break;
					}
					$result = $res_19;
					$this->pos = $pos_19;
					$_22 = \false; break;
				}
				while(0);
				if( $_22 === \true ) { $_24 = \true; break; }
				$result = $res_17;
				$this->pos = $pos_17;
				$_24 = \false; break;
			}
			while(0);
			if( $_24 === \true ) { $_26 = \true; break; }
			$result = $res_15;
			$this->pos = $pos_15;
			$_26 = \false; break;
		}
		while(0);
		if( $_26 === \true ) { $_28 = \true; break; }
		$result = $res_13;
		$this->pos = $pos_13;
		$_28 = \false; break;
	}
	while(0);
	if( $_28 === \true ) { return $this->finalise($result); }
	if( $_28 === \false) { return \false; }
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


/* Variable: ( core:VariableName post:UnaryOperator? ) | ( pre:UnaryOperator? core:VariableName ) */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_40 = \null;
	do {
		$res_31 = $result;
		$pos_31 = $this->pos;
		$_34 = \null;
		do {
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_34 = \false; break; }
			$res_33 = $result;
			$pos_33 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "post" );
			}
			else {
				$result = $res_33;
				$this->pos = $pos_33;
				unset( $res_33 );
				unset( $pos_33 );
			}
			$_34 = \true; break;
		}
		while(0);
		if( $_34 === \true ) { $_40 = \true; break; }
		$result = $res_31;
		$this->pos = $pos_31;
		$_38 = \null;
		do {
			$res_36 = $result;
			$pos_36 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "pre" );
			}
			else {
				$result = $res_36;
				$this->pos = $pos_36;
				unset( $res_36 );
				unset( $pos_36 );
			}
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_38 = \false; break; }
			$_38 = \true; break;
		}
		while(0);
		if( $_38 === \true ) { $_40 = \true; break; }
		$result = $res_31;
		$this->pos = $pos_31;
		$_40 = \false; break;
	}
	while(0);
	if( $_40 === \true ) { return $this->finalise($result); }
	if( $_40 === \false) { return \false; }
}


/* PropertyGetter: core:VariableName */
protected $match_PropertyGetter_typestack = array('PropertyGetter');
function match_PropertyGetter ($stack = []) {
	$matchrule = "PropertyGetter"; $result = $this->construct($matchrule, $matchrule, \null);
	$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
	if ($subres !== \false) {
		$this->store( $result, $subres, "core" );
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AnonymousFunction: "function" __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ args:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = array('AnonymousFunction');
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_66 = \null;
	do {
		$res_43 = $result;
		$pos_43 = $this->pos;
		$_53 = \null;
		do {
			if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_53 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_53 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			$res_48 = $result;
			$pos_48 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_48;
				$this->pos = $pos_48;
				unset( $res_48 );
				unset( $pos_48 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_53 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_53 = \false; break; }
			$_53 = \true; break;
		}
		while(0);
		if( $_53 === \true ) { $_66 = \true; break; }
		$result = $res_43;
		$this->pos = $pos_43;
		$_64 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_64 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			$res_57 = $result;
			$pos_57 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_57;
				$this->pos = $pos_57;
				unset( $res_57 );
				unset( $pos_57 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_64 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			if (( $subres = $this->literal( '=>' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_64 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_64 = \false; break; }
			$_64 = \true; break;
		}
		while(0);
		if( $_64 === \true ) { $_66 = \true; break; }
		$result = $res_43;
		$this->pos = $pos_43;
		$_66 = \false; break;
	}
	while(0);
	if( $_66 === \true ) { return $this->finalise($result); }
	if( $_66 === \false) { return \false; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = []) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, \null);
	$_75 = \null;
	do {
		$res_72 = $result;
		$pos_72 = $this->pos;
		$_71 = \null;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "key" );
			}
			else { $_71 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_71 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_71 = \false; break; }
			$_71 = \true; break;
		}
		while(0);
		if( $_71 === \false) {
			$result = $res_72;
			$this->pos = $pos_72;
			unset( $res_72 );
			unset( $pos_72 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_75 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "value" );
		}
		else { $_75 = \false; break; }
		$_75 = \true; break;
	}
	while(0);
	if( $_75 === \true ) { return $this->finalise($result); }
	if( $_75 === \false) { return \false; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ ( "," __ )? "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = []) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_94 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_94 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_94 = \false; break; }
		$res_87 = $result;
		$pos_87 = $this->pos;
		$_86 = \null;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "items" );
			}
			else { $_86 = \false; break; }
			while (\true) {
				$res_85 = $result;
				$pos_85 = $this->pos;
				$_84 = \null;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_84 = \false; break; }
					if (\substr($this->string,$this->pos,1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_84 = \false; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_84 = \false; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "items" );
					}
					else { $_84 = \false; break; }
					$_84 = \true; break;
				}
				while(0);
				if( $_84 === \false) {
					$result = $res_85;
					$this->pos = $pos_85;
					unset( $res_85 );
					unset( $pos_85 );
					break;
				}
			}
			$_86 = \true; break;
		}
		while(0);
		if( $_86 === \false) {
			$result = $res_87;
			$this->pos = $pos_87;
			unset( $res_87 );
			unset( $pos_87 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_94 = \false; break; }
		$res_92 = $result;
		$pos_92 = $this->pos;
		$_91 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_91 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_91 = \false; break; }
			$_91 = \true; break;
		}
		while(0);
		if( $_91 === \false) {
			$result = $res_92;
			$this->pos = $pos_92;
			unset( $res_92 );
			unset( $pos_92 );
		}
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_94 = \false; break; }
		$_94 = \true; break;
	}
	while(0);
	if( $_94 === \true ) { return $this->finalise($result); }
	if( $_94 === \false) { return \false; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, \null);
	$_103 = \null;
	do {
		$res_96 = $result;
		$pos_96 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_103 = \true; break;
		}
		$result = $res_96;
		$this->pos = $pos_96;
		$_101 = \null;
		do {
			$res_98 = $result;
			$pos_98 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_101 = \true; break;
			}
			$result = $res_98;
			$this->pos = $pos_98;
			$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_101 = \true; break;
			}
			$result = $res_98;
			$this->pos = $pos_98;
			$_101 = \false; break;
		}
		while(0);
		if( $_101 === \true ) { $_103 = \true; break; }
		$result = $res_96;
		$this->pos = $pos_96;
		$_103 = \false; break;
	}
	while(0);
	if( $_103 === \true ) { return $this->finalise($result); }
	if( $_103 === \false) { return \false; }
}


/* DereferencableValue: core:Value ( "[" __ dereference:Expression __ "]" )* */
protected $match_DereferencableValue_typestack = array('DereferencableValue');
function match_DereferencableValue ($stack = []) {
	$matchrule = "DereferencableValue"; $result = $this->construct($matchrule, $matchrule, \null);
	$_113 = \null;
	do {
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_113 = \false; break; }
		while (\true) {
			$res_112 = $result;
			$pos_112 = $this->pos;
			$_111 = \null;
			do {
				if (\substr($this->string,$this->pos,1) === '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_111 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_111 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_111 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_111 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_111 = \false; break; }
				$_111 = \true; break;
			}
			while(0);
			if( $_111 === \false) {
				$result = $res_112;
				$this->pos = $pos_112;
				unset( $res_112 );
				unset( $pos_112 );
				break;
			}
		}
		$_113 = \true; break;
	}
	while(0);
	if( $_113 === \true ) { return $this->finalise($result); }
	if( $_113 === \false) { return \false; }
}


/* VariableVector: core:Variable ( "[" __ ( vector:Expression | vector:"" ) __ "]" )+ */
protected $match_VariableVector_typestack = array('VariableVector');
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, \null);
	$_129 = \null;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_129 = \false; break; }
		$count = 0;
		while (\true) {
			$res_128 = $result;
			$pos_128 = $this->pos;
			$_127 = \null;
			do {
				if (\substr($this->string,$this->pos,1) === '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_127 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_127 = \false; break; }
				$_123 = \null;
				do {
					$_121 = \null;
					do {
						$res_118 = $result;
						$pos_118 = $this->pos;
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "vector" );
							$_121 = \true; break;
						}
						$result = $res_118;
						$this->pos = $pos_118;
						$stack[] = $result; $result = $this->construct( $matchrule, "vector" ); 
						if (( $subres = $this->literal( '' ) ) !== \false) {
							$result["text"] .= $subres;
							$subres = $result; $result = \array_pop($stack);
							$this->store( $result, $subres, 'vector' );
							$_121 = \true; break;
						}
						else { $result = \array_pop($stack); }
						$result = $res_118;
						$this->pos = $pos_118;
						$_121 = \false; break;
					}
					while(0);
					if( $_121 === \false) { $_123 = \false; break; }
					$_123 = \true; break;
				}
				while(0);
				if( $_123 === \false) { $_127 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_127 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_127 = \false; break; }
				$_127 = \true; break;
			}
			while(0);
			if( $_127 === \false) {
				$result = $res_128;
				$this->pos = $pos_128;
				unset( $res_128 );
				unset( $pos_128 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_129 = \false; break; }
		$_129 = \true; break;
	}
	while(0);
	if( $_129 === \true ) { return $this->finalise($result); }
	if( $_129 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = array('Mutable');
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_134 = \null;
	do {
		$res_131 = $result;
		$pos_131 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_134 = \true; break;
		}
		$result = $res_131;
		$this->pos = $pos_131;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_134 = \true; break;
		}
		$result = $res_131;
		$this->pos = $pos_131;
		$_134 = \false; break;
	}
	while(0);
	if( $_134 === \true ) { return $this->finalise($result); }
	if( $_134 === \false) { return \false; }
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


/* Multiplication: operands:Operand ( __ ops:MultiplyOperator __ operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null);
	$_217 = \null;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
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
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
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


/* Chain: ObjectResolutionOperator ( core:MethodCall | core:PropertyGetter ) ( chain:Chain )? */
protected $match_Chain_typestack = array('Chain');
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null);
	$_230 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_230 = \false; break; }
		$_225 = \null;
		do {
			$_223 = \null;
			do {
				$res_220 = $result;
				$pos_220 = $this->pos;
				$matcher = 'match_'.'MethodCall'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_223 = \true; break;
				}
				$result = $res_220;
				$this->pos = $pos_220;
				$matcher = 'match_'.'PropertyGetter'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_223 = \true; break;
				}
				$result = $res_220;
				$this->pos = $pos_220;
				$_223 = \false; break;
			}
			while(0);
			if( $_223 === \false) { $_225 = \false; break; }
			$_225 = \true; break;
		}
		while(0);
		if( $_225 === \false) { $_230 = \false; break; }
		$res_229 = $result;
		$pos_229 = $this->pos;
		$_228 = \null;
		do {
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
			}
			else { $_228 = \false; break; }
			$_228 = \true; break;
		}
		while(0);
		if( $_228 === \false) {
			$result = $res_229;
			$this->pos = $pos_229;
			unset( $res_229 );
			unset( $pos_229 );
		}
		$_230 = \true; break;
	}
	while(0);
	if( $_230 === \true ) { return $this->finalise($result); }
	if( $_230 === \false) { return \false; }
}


/* Operand: ( core:FunctionCall | "(" __ core:Expression __ ")" | core:DereferencableValue ) ( chain:Chain )? */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null);
	$_252 = \null;
	do {
		$_247 = \null;
		do {
			$_245 = \null;
			do {
				$res_232 = $result;
				$pos_232 = $this->pos;
				$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_245 = \true; break;
				}
				$result = $res_232;
				$this->pos = $pos_232;
				$_243 = \null;
				do {
					$res_234 = $result;
					$pos_234 = $this->pos;
					$_240 = \null;
					do {
						if (\substr($this->string,$this->pos,1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_240 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_240 = \false; break; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "core" );
						}
						else { $_240 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_240 = \false; break; }
						if (\substr($this->string,$this->pos,1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_240 = \false; break; }
						$_240 = \true; break;
					}
					while(0);
					if( $_240 === \true ) { $_243 = \true; break; }
					$result = $res_234;
					$this->pos = $pos_234;
					$matcher = 'match_'.'DereferencableValue'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_243 = \true; break;
					}
					$result = $res_234;
					$this->pos = $pos_234;
					$_243 = \false; break;
				}
				while(0);
				if( $_243 === \true ) { $_245 = \true; break; }
				$result = $res_232;
				$this->pos = $pos_232;
				$_245 = \false; break;
			}
			while(0);
			if( $_245 === \false) { $_247 = \false; break; }
			$_247 = \true; break;
		}
		while(0);
		if( $_247 === \false) { $_252 = \false; break; }
		$res_251 = $result;
		$pos_251 = $this->pos;
		$_250 = \null;
		do {
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
			}
			else { $_250 = \false; break; }
			$_250 = \true; break;
		}
		while(0);
		if( $_250 === \false) {
			$result = $res_251;
			$this->pos = $pos_251;
			unset( $res_251 );
			unset( $pos_251 );
		}
		$_252 = \true; break;
	}
	while(0);
	if( $_252 === \true ) { return $this->finalise($result); }
	if( $_252 === \false) { return \false; }
}


/* MethodCall: method:VariableName "(" __ args:ArgumentList? __ ")" */
protected $match_MethodCall_typestack = array('MethodCall');
function match_MethodCall ($stack = []) {
	$matchrule = "MethodCall"; $result = $this->construct($matchrule, $matchrule, \null);
	$_260 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "method" );
		}
		else { $_260 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_260 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_260 = \false; break; }
		$res_257 = $result;
		$pos_257 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_257;
			$this->pos = $pos_257;
			unset( $res_257 );
			unset( $pos_257 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_260 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_260 = \false; break; }
		$_260 = \true; break;
	}
	while(0);
	if( $_260 === \true ) { return $this->finalise($result); }
	if( $_260 === \false) { return \false; }
}


/* FunctionCall: ( variable:VariableName | "(" __ value:Expression __ ")" ) "(" __ args:ArgumentList? __ ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = []) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, \null);
	$_280 = \null;
	do {
		$_273 = \null;
		do {
			$_271 = \null;
			do {
				$res_262 = $result;
				$pos_262 = $this->pos;
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "variable" );
					$_271 = \true; break;
				}
				$result = $res_262;
				$this->pos = $pos_262;
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
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "value" );
					}
					else { $_269 = \false; break; }
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
				if( $_269 === \true ) { $_271 = \true; break; }
				$result = $res_262;
				$this->pos = $pos_262;
				$_271 = \false; break;
			}
			while(0);
			if( $_271 === \false) { $_273 = \false; break; }
			$_273 = \true; break;
		}
		while(0);
		if( $_273 === \false) { $_280 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_280 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_280 = \false; break; }
		$res_277 = $result;
		$pos_277 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_277;
			$this->pos = $pos_277;
			unset( $res_277 );
			unset( $pos_277 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_280 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_280 = \false; break; }
		$_280 = \true; break;
	}
	while(0);
	if( $_280 === \true ) { return $this->finalise($result); }
	if( $_280 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = array('ArgumentList');
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_289 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else { $_289 = \false; break; }
		while (\true) {
			$res_288 = $result;
			$pos_288 = $this->pos;
			$_287 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_287 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_287 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_287 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "args" );
				}
				else { $_287 = \false; break; }
				$_287 = \true; break;
			}
			while(0);
			if( $_287 === \false) {
				$result = $res_288;
				$this->pos = $pos_288;
				unset( $res_288 );
				unset( $pos_288 );
				break;
			}
		}
		$_289 = \true; break;
	}
	while(0);
	if( $_289 === \true ) { return $this->finalise($result); }
	if( $_289 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_298 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_298 = \false; break; }
		while (\true) {
			$res_297 = $result;
			$pos_297 = $this->pos;
			$_296 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_296 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_296 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_296 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_296 = \false; break; }
				$_296 = \true; break;
			}
			while(0);
			if( $_296 === \false) {
				$result = $res_297;
				$this->pos = $pos_297;
				unset( $res_297 );
				unset( $pos_297 );
				break;
			}
		}
		$_298 = \true; break;
	}
	while(0);
	if( $_298 === \true ) { return $this->finalise($result); }
	if( $_298 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_311 = \null;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_311 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_311 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "function" );
		}
		else { $_311 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_311 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_311 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_311 = \false; break; }
		$res_306 = $result;
		$pos_306 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_306;
			$this->pos = $pos_306;
			unset( $res_306 );
			unset( $pos_306 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_311 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_311 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_311 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "body" );
		}
		else { $_311 = \false; break; }
		$_311 = \true; break;
	}
	while(0);
	if( $_311 === \true ) { return $this->finalise($result); }
	if( $_311 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_324 = \null;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		$_322 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_322 = \false; break; }
			$_322 = \true; break;
		}
		while(0);
		if( $_322 === \false) { $_324 = \false; break; }
		$_324 = \true; break;
	}
	while(0);
	if( $_324 === \true ) { return $this->finalise($result); }
	if( $_324 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_337 = \null;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		$_335 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_335 = \false; break; }
			$_335 = \true; break;
		}
		while(0);
		if( $_335 === \false) { $_337 = \false; break; }
		$_337 = \true; break;
	}
	while(0);
	if( $_337 === \true ) { return $this->finalise($result); }
	if( $_337 === \false) { return \false; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = []) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_354 = \null;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_354 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_354 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_354 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_354 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_354 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_354 = \false; break; }
		if (( $subres = $this->literal( 'as' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_354 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_354 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "item" );
		}
		else { $_354 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_354 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_354 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_354 = \false; break; }
		$_352 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_352 = \false; break; }
			$_352 = \true; break;
		}
		while(0);
		if( $_352 === \false) { $_354 = \false; break; }
		$_354 = \true; break;
	}
	while(0);
	if( $_354 === \true ) { return $this->finalise($result); }
	if( $_354 === \false) { return \false; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_359 = \null;
	do {
		$res_356 = $result;
		$pos_356 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_359 = \true; break;
		}
		$result = $res_356;
		$this->pos = $pos_356;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_359 = \true; break;
		}
		$result = $res_356;
		$this->pos = $pos_356;
		$_359 = \false; break;
	}
	while(0);
	if( $_359 === \true ) { return $this->finalise($result); }
	if( $_359 === \false) { return \false; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = []) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_364 = \null;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_364 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_364 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_364 = \false; break; }
		$_364 = \true; break;
	}
	while(0);
	if( $_364 === \true ) { return $this->finalise($result); }
	if( $_364 === \false) { return \false; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_371 = \null;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_371 = \false; break; }
		$res_370 = $result;
		$pos_370 = $this->pos;
		$_369 = \null;
		do {
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			else { $_369 = \false; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_369 = \false; break; }
			$_369 = \true; break;
		}
		while(0);
		if( $_369 === \false) {
			$result = $res_370;
			$this->pos = $pos_370;
			unset( $res_370 );
			unset( $pos_370 );
		}
		$_371 = \true; break;
	}
	while(0);
	if( $_371 === \true ) { return $this->finalise($result); }
	if( $_371 === \false) { return \false; }
}


/* BlockStatements: &/[A-Za-z]/ ( skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition ) */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_389 = \null;
	do {
		$res_373 = $result;
		$pos_373 = $this->pos;
		if (( $subres = $this->rx( '/[A-Za-z]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_373;
			$this->pos = $pos_373;
		}
		else {
			$result = $res_373;
			$this->pos = $pos_373;
			$_389 = \false; break;
		}
		$_387 = \null;
		do {
			$_385 = \null;
			do {
				$res_374 = $result;
				$pos_374 = $this->pos;
				$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_385 = \true; break;
				}
				$result = $res_374;
				$this->pos = $pos_374;
				$_383 = \null;
				do {
					$res_376 = $result;
					$pos_376 = $this->pos;
					$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_383 = \true; break;
					}
					$result = $res_376;
					$this->pos = $pos_376;
					$_381 = \null;
					do {
						$res_378 = $result;
						$pos_378 = $this->pos;
						$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_381 = \true; break;
						}
						$result = $res_378;
						$this->pos = $pos_378;
						$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_381 = \true; break;
						}
						$result = $res_378;
						$this->pos = $pos_378;
						$_381 = \false; break;
					}
					while(0);
					if( $_381 === \true ) { $_383 = \true; break; }
					$result = $res_376;
					$this->pos = $pos_376;
					$_383 = \false; break;
				}
				while(0);
				if( $_383 === \true ) { $_385 = \true; break; }
				$result = $res_374;
				$this->pos = $pos_374;
				$_385 = \false; break;
			}
			while(0);
			if( $_385 === \false) { $_387 = \false; break; }
			$_387 = \true; break;
		}
		while(0);
		if( $_387 === \false) { $_389 = \false; break; }
		$_389 = \true; break;
	}
	while(0);
	if( $_389 === \true ) { return $this->finalise($result); }
	if( $_389 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_403 = \null;
	do {
		$res_391 = $result;
		$pos_391 = $this->pos;
		if (( $subres = $this->rx( '/[\s\{\};]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_391;
			$this->pos = $pos_391;
			$_403 = \false; break;
		}
		else {
			$result = $res_391;
			$this->pos = $pos_391;
		}
		$_401 = \null;
		do {
			$_399 = \null;
			do {
				$res_392 = $result;
				$pos_392 = $this->pos;
				$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_399 = \true; break;
				}
				$result = $res_392;
				$this->pos = $pos_392;
				$_397 = \null;
				do {
					$res_394 = $result;
					$pos_394 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_397 = \true; break;
					}
					$result = $res_394;
					$this->pos = $pos_394;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_397 = \true; break;
					}
					$result = $res_394;
					$this->pos = $pos_394;
					$_397 = \false; break;
				}
				while(0);
				if( $_397 === \true ) { $_399 = \true; break; }
				$result = $res_392;
				$this->pos = $pos_392;
				$_399 = \false; break;
			}
			while(0);
			if( $_399 === \false) { $_401 = \false; break; }
			$_401 = \true; break;
		}
		while(0);
		if( $_401 === \false) { $_403 = \false; break; }
		$_403 = \true; break;
	}
	while(0);
	if( $_403 === \true ) { return $this->finalise($result); }
	if( $_403 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null);
	$_411 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_411 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_411 = \false; break; }
		$res_409 = $result;
		$pos_409 = $this->pos;
		$_408 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_408 = \false; break; }
			$_408 = \true; break;
		}
		while(0);
		if( $_408 === \false) {
			$result = $res_409;
			$this->pos = $pos_409;
			unset( $res_409 );
			unset( $pos_409 );
		}
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_411 = \false; break; }
		$_411 = \true; break;
	}
	while(0);
	if( $_411 === \true ) { return $this->finalise($result); }
	if( $_411 === \false) { return \false; }
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
	$_418 = \null;
	do {
		$res_415 = $result;
		$pos_415 = $this->pos;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_418 = \true; break;
		}
		$result = $res_415;
		$this->pos = $pos_415;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres );
			$_418 = \true; break;
		}
		$result = $res_415;
		$this->pos = $pos_415;
		$_418 = \false; break;
	}
	while(0);
	if( $_418 === \true ) { return $this->finalise($result); }
	if( $_418 === \false) { return \false; }
}


/* Program: ( !/$/ __ Statement? > SEP )+ __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null);
	$_428 = \null;
	do {
		$count = 0;
		while (\true) {
			$res_426 = $result;
			$pos_426 = $this->pos;
			$_425 = \null;
			do {
				$res_420 = $result;
				$pos_420 = $this->pos;
				if (( $subres = $this->rx( '/$/' ) ) !== \false) {
					$result["text"] .= $subres;
					$result = $res_420;
					$this->pos = $pos_420;
					$_425 = \false; break;
				}
				else {
					$result = $res_420;
					$this->pos = $pos_420;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_425 = \false; break; }
				$res_422 = $result;
				$pos_422 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else {
					$result = $res_422;
					$this->pos = $pos_422;
					unset( $res_422 );
					unset( $pos_422 );
				}
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_425 = \false; break; }
				$_425 = \true; break;
			}
			while(0);
			if( $_425 === \false) {
				$result = $res_426;
				$this->pos = $pos_426;
				unset( $res_426 );
				unset( $pos_426 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_428 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_428 = \false; break; }
		$_428 = \true; break;
	}
	while(0);
	if( $_428 === \true ) { return $this->finalise($result); }
	if( $_428 === \false) { return \false; }
}




}
