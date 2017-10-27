<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Basic {

    // Add these properties so PHPStan doesn't complain about undefined properties.

    /** @var int **/
    public $pos;

    /** @var string **/
    public $string;

/* StringLiteral: /("(.|\n)*?"|'(.|\n)*?')/ */
protected $match_StringLiteral_typestack = array('StringLiteral');
function match_StringLiteral ($stack = array()) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/("(.|\n)*?"|\'(.|\n)*?\')/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = array('NumberLiteral');
function match_NumberLiteral ($stack = array()) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/-?\d+(\.\d+)?/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = array('BoolLiteral');
function match_BoolLiteral ($stack = array()) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	$_5 = NULL;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_5 = TRUE; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		if (( $subres = $this->literal( 'false' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_5 = TRUE; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$_5 = FALSE; break;
	}
	while(0);
	if( $_5 === TRUE ) { return $this->finalise($result); }
	if( $_5 === FALSE) { return FALSE; }
}


/* RegexLiteral: "/" /(\\\/|[^\/])+/ "/" */
protected $match_RegexLiteral_typestack = array('RegexLiteral');
function match_RegexLiteral ($stack = array()) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	$_10 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = FALSE; break; }
		if (( $subres = $this->rx( '/(\\\\\/|[^\/])+/' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_10 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = FALSE; break; }
		$_10 = TRUE; break;
	}
	while(0);
	if( $_10 === TRUE ) { return $this->finalise($result); }
	if( $_10 === FALSE) { return FALSE; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = array()) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_23 = NULL;
	do {
		$res_12 = $result;
		$pos_12 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_23 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$_21 = NULL;
		do {
			$res_14 = $result;
			$pos_14 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_21 = TRUE; break;
			}
			$result = $res_14;
			$this->pos = $pos_14;
			$_19 = NULL;
			do {
				$res_16 = $result;
				$pos_16 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_19 = TRUE; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_19 = TRUE; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$_19 = FALSE; break;
			}
			while(0);
			if( $_19 === TRUE ) { $_21 = TRUE; break; }
			$result = $res_14;
			$this->pos = $pos_14;
			$_21 = FALSE; break;
		}
		while(0);
		if( $_21 === TRUE ) { $_23 = TRUE; break; }
		$result = $res_12;
		$this->pos = $pos_12;
		$_23 = FALSE; break;
	}
	while(0);
	if( $_23 === TRUE ) { return $this->finalise($result); }
	if( $_23 === FALSE) { return FALSE; }
}


/* VariableName: /([a-zA-Z_][a-zA-Z0-9_]*)/ */
protected $match_VariableName_typestack = array('VariableName');
function match_VariableName ($stack = array()) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([a-zA-Z_][a-zA-Z0-9_]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Variable: ( core:VariableName post:UnaryOperator? ) | ( pre:UnaryOperator? core:VariableName ) */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = array()) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, null);
	$_35 = NULL;
	do {
		$res_26 = $result;
		$pos_26 = $this->pos;
		$_29 = NULL;
		do {
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "core" );
			}
			else { $_29 = FALSE; break; }
			$res_28 = $result;
			$pos_28 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "post" );
			}
			else {
				$result = $res_28;
				$this->pos = $pos_28;
				unset( $res_28 );
				unset( $pos_28 );
			}
			$_29 = TRUE; break;
		}
		while(0);
		if( $_29 === TRUE ) { $_35 = TRUE; break; }
		$result = $res_26;
		$this->pos = $pos_26;
		$_33 = NULL;
		do {
			$res_31 = $result;
			$pos_31 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "pre" );
			}
			else {
				$result = $res_31;
				$this->pos = $pos_31;
				unset( $res_31 );
				unset( $pos_31 );
			}
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "core" );
			}
			else { $_33 = FALSE; break; }
			$_33 = TRUE; break;
		}
		while(0);
		if( $_33 === TRUE ) { $_35 = TRUE; break; }
		$result = $res_26;
		$this->pos = $pos_26;
		$_35 = FALSE; break;
	}
	while(0);
	if( $_35 === TRUE ) { return $this->finalise($result); }
	if( $_35 === FALSE) { return FALSE; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = array()) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, null);
	$_44 = NULL;
	do {
		$res_41 = $result;
		$pos_41 = $this->pos;
		$_40 = NULL;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "key" );
			}
			else { $_40 = FALSE; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_40 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_40 = FALSE; break; }
			$_40 = TRUE; break;
		}
		while(0);
		if( $_40 === FALSE) {
			$result = $res_41;
			$this->pos = $pos_41;
			unset( $res_41 );
			unset( $pos_41 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_44 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "value" );
		}
		else { $_44 = FALSE; break; }
		$_44 = TRUE; break;
	}
	while(0);
	if( $_44 === TRUE ) { return $this->finalise($result); }
	if( $_44 === FALSE) { return FALSE; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = array()) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_59 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_59 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_59 = FALSE; break; }
		$res_56 = $result;
		$pos_56 = $this->pos;
		$_55 = NULL;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "items" );
			}
			else { $_55 = FALSE; break; }
			while (true) {
				$res_54 = $result;
				$pos_54 = $this->pos;
				$_53 = NULL;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_53 = FALSE; break; }
					if (substr($this->string,$this->pos,1) == ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_53 = FALSE; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_53 = FALSE; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "items" );
					}
					else { $_53 = FALSE; break; }
					$_53 = TRUE; break;
				}
				while(0);
				if( $_53 === FALSE) {
					$result = $res_54;
					$this->pos = $pos_54;
					unset( $res_54 );
					unset( $pos_54 );
					break;
				}
			}
			$_55 = TRUE; break;
		}
		while(0);
		if( $_55 === FALSE) {
			$result = $res_56;
			$this->pos = $pos_56;
			unset( $res_56 );
			unset( $pos_56 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_59 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_59 = FALSE; break; }
		$_59 = TRUE; break;
	}
	while(0);
	if( $_59 === TRUE ) { return $this->finalise($result); }
	if( $_59 === FALSE) { return FALSE; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_68 = NULL;
	do {
		$res_61 = $result;
		$pos_61 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_68 = TRUE; break;
		}
		$result = $res_61;
		$this->pos = $pos_61;
		$_66 = NULL;
		do {
			$res_63 = $result;
			$pos_63 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_66 = TRUE; break;
			}
			$result = $res_63;
			$this->pos = $pos_63;
			$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_66 = TRUE; break;
			}
			$result = $res_63;
			$this->pos = $pos_63;
			$_66 = FALSE; break;
		}
		while(0);
		if( $_66 === TRUE ) { $_68 = TRUE; break; }
		$result = $res_61;
		$this->pos = $pos_61;
		$_68 = FALSE; break;
	}
	while(0);
	if( $_68 === TRUE ) { return $this->finalise($result); }
	if( $_68 === FALSE) { return FALSE; }
}


/* DereferencableValue: core:Value ( "[" __ dereference:Expression __ "]" )* */
protected $match_DereferencableValue_typestack = array('DereferencableValue');
function match_DereferencableValue ($stack = array()) {
	$matchrule = "DereferencableValue"; $result = $this->construct($matchrule, $matchrule, null);
	$_78 = NULL;
	do {
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "core" );
		}
		else { $_78 = FALSE; break; }
		while (true) {
			$res_77 = $result;
			$pos_77 = $this->pos;
			$_76 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_76 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_76 = FALSE; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_76 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_76 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_76 = FALSE; break; }
				$_76 = TRUE; break;
			}
			while(0);
			if( $_76 === FALSE) {
				$result = $res_77;
				$this->pos = $pos_77;
				unset( $res_77 );
				unset( $pos_77 );
				break;
			}
		}
		$_78 = TRUE; break;
	}
	while(0);
	if( $_78 === TRUE ) { return $this->finalise($result); }
	if( $_78 === FALSE) { return FALSE; }
}


/* VariableVector: core:Variable ( "[" __ ( vector:Expression | vector:"" ) __ "]" )+ */
protected $match_VariableVector_typestack = array('VariableVector');
function match_VariableVector ($stack = array()) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, null);
	$_94 = NULL;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "core" );
		}
		else { $_94 = FALSE; break; }
		$count = 0;
		while (true) {
			$res_93 = $result;
			$pos_93 = $this->pos;
			$_92 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_92 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_92 = FALSE; break; }
				$_88 = NULL;
				do {
					$_86 = NULL;
					do {
						$res_83 = $result;
						$pos_83 = $this->pos;
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres, "vector" );
							$_86 = TRUE; break;
						}
						$result = $res_83;
						$this->pos = $pos_83;
						$stack[] = $result; $result = $this->construct( $matchrule, "vector" ); 
						if (( $subres = $this->literal( '' ) ) !== FALSE) {
							$result["text"] .= $subres;
							$subres = $result; $result = array_pop($stack);
							$this->store( $result, $subres, 'vector' );
							$_86 = TRUE; break;
						}
						else { $result = array_pop($stack); }
						$result = $res_83;
						$this->pos = $pos_83;
						$_86 = FALSE; break;
					}
					while(0);
					if( $_86 === FALSE) { $_88 = FALSE; break; }
					$_88 = TRUE; break;
				}
				while(0);
				if( $_88 === FALSE) { $_92 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_92 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_92 = FALSE; break; }
				$_92 = TRUE; break;
			}
			while(0);
			if( $_92 === FALSE) {
				$result = $res_93;
				$this->pos = $pos_93;
				unset( $res_93 );
				unset( $pos_93 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_94 = FALSE; break; }
		$_94 = TRUE; break;
	}
	while(0);
	if( $_94 === TRUE ) { return $this->finalise($result); }
	if( $_94 === FALSE) { return FALSE; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = array('Mutable');
function match_Mutable ($stack = array()) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, null);
	$_99 = NULL;
	do {
		$res_96 = $result;
		$pos_96 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_99 = TRUE; break;
		}
		$result = $res_96;
		$this->pos = $pos_96;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_99 = TRUE; break;
		}
		$result = $res_96;
		$this->pos = $pos_96;
		$_99 = FALSE; break;
	}
	while(0);
	if( $_99 === TRUE ) { return $this->finalise($result); }
	if( $_99 === FALSE) { return FALSE; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = array('ObjectResolutionOperator');
function match_ObjectResolutionOperator ($stack = array()) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = array()) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_105 = NULL;
	do {
		$res_102 = $result;
		$pos_102 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_105 = TRUE; break;
		}
		$result = $res_102;
		$this->pos = $pos_102;
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_105 = TRUE; break;
		}
		$result = $res_102;
		$this->pos = $pos_102;
		$_105 = FALSE; break;
	}
	while(0);
	if( $_105 === TRUE ) { return $this->finalise($result); }
	if( $_105 === FALSE) { return FALSE; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = array()) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_110 = NULL;
	do {
		$res_107 = $result;
		$pos_107 = $this->pos;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_110 = TRUE; break;
		}
		$result = $res_107;
		$this->pos = $pos_107;
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_110 = TRUE; break;
		}
		$result = $res_107;
		$this->pos = $pos_107;
		$_110 = FALSE; break;
	}
	while(0);
	if( $_110 === TRUE ) { return $this->finalise($result); }
	if( $_110 === FALSE) { return FALSE; }
}


/* AssignmentOperator: "=" | "+=" | "-=" | "*=" | "/=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = array()) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_127 = NULL;
	do {
		$res_112 = $result;
		$pos_112 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_127 = TRUE; break;
		}
		$result = $res_112;
		$this->pos = $pos_112;
		$_125 = NULL;
		do {
			$res_114 = $result;
			$pos_114 = $this->pos;
			if (( $subres = $this->literal( '+=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_125 = TRUE; break;
			}
			$result = $res_114;
			$this->pos = $pos_114;
			$_123 = NULL;
			do {
				$res_116 = $result;
				$pos_116 = $this->pos;
				if (( $subres = $this->literal( '-=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_123 = TRUE; break;
				}
				$result = $res_116;
				$this->pos = $pos_116;
				$_121 = NULL;
				do {
					$res_118 = $result;
					$pos_118 = $this->pos;
					if (( $subres = $this->literal( '*=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_121 = TRUE; break;
					}
					$result = $res_118;
					$this->pos = $pos_118;
					if (( $subres = $this->literal( '/=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_121 = TRUE; break;
					}
					$result = $res_118;
					$this->pos = $pos_118;
					$_121 = FALSE; break;
				}
				while(0);
				if( $_121 === TRUE ) { $_123 = TRUE; break; }
				$result = $res_116;
				$this->pos = $pos_116;
				$_123 = FALSE; break;
			}
			while(0);
			if( $_123 === TRUE ) { $_125 = TRUE; break; }
			$result = $res_114;
			$this->pos = $pos_114;
			$_125 = FALSE; break;
		}
		while(0);
		if( $_125 === TRUE ) { $_127 = TRUE; break; }
		$result = $res_112;
		$this->pos = $pos_112;
		$_127 = FALSE; break;
	}
	while(0);
	if( $_127 === TRUE ) { return $this->finalise($result); }
	if( $_127 === FALSE) { return FALSE; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = array()) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_148 = NULL;
	do {
		$res_129 = $result;
		$pos_129 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_148 = TRUE; break;
		}
		$result = $res_129;
		$this->pos = $pos_129;
		$_146 = NULL;
		do {
			$res_131 = $result;
			$pos_131 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_146 = TRUE; break;
			}
			$result = $res_131;
			$this->pos = $pos_131;
			$_144 = NULL;
			do {
				$res_133 = $result;
				$pos_133 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_144 = TRUE; break;
				}
				$result = $res_133;
				$this->pos = $pos_133;
				$_142 = NULL;
				do {
					$res_135 = $result;
					$pos_135 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_142 = TRUE; break;
					}
					$result = $res_135;
					$this->pos = $pos_135;
					$_140 = NULL;
					do {
						$res_137 = $result;
						$pos_137 = $this->pos;
						if (substr($this->string,$this->pos,1) == '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_140 = TRUE; break;
						}
						$result = $res_137;
						$this->pos = $pos_137;
						if (substr($this->string,$this->pos,1) == '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_140 = TRUE; break;
						}
						$result = $res_137;
						$this->pos = $pos_137;
						$_140 = FALSE; break;
					}
					while(0);
					if( $_140 === TRUE ) { $_142 = TRUE; break; }
					$result = $res_135;
					$this->pos = $pos_135;
					$_142 = FALSE; break;
				}
				while(0);
				if( $_142 === TRUE ) { $_144 = TRUE; break; }
				$result = $res_133;
				$this->pos = $pos_133;
				$_144 = FALSE; break;
			}
			while(0);
			if( $_144 === TRUE ) { $_146 = TRUE; break; }
			$result = $res_131;
			$this->pos = $pos_131;
			$_146 = FALSE; break;
		}
		while(0);
		if( $_146 === TRUE ) { $_148 = TRUE; break; }
		$result = $res_129;
		$this->pos = $pos_129;
		$_148 = FALSE; break;
	}
	while(0);
	if( $_148 === TRUE ) { return $this->finalise($result); }
	if( $_148 === FALSE) { return FALSE; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = array()) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_153 = NULL;
	do {
		$res_150 = $result;
		$pos_150 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_153 = TRUE; break;
		}
		$result = $res_150;
		$this->pos = $pos_150;
		if (( $subres = $this->literal( '--' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_153 = TRUE; break;
		}
		$result = $res_150;
		$this->pos = $pos_150;
		$_153 = FALSE; break;
	}
	while(0);
	if( $_153 === TRUE ) { return $this->finalise($result); }
	if( $_153 === FALSE) { return FALSE; }
}


/* Expression: skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = array()) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_162 = NULL;
	do {
		$res_155 = $result;
		$pos_155 = $this->pos;
		$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_162 = TRUE; break;
		}
		$result = $res_155;
		$this->pos = $pos_155;
		$_160 = NULL;
		do {
			$res_157 = $result;
			$pos_157 = $this->pos;
			$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_160 = TRUE; break;
			}
			$result = $res_157;
			$this->pos = $pos_157;
			$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_160 = TRUE; break;
			}
			$result = $res_157;
			$this->pos = $pos_157;
			$_160 = FALSE; break;
		}
		while(0);
		if( $_160 === TRUE ) { $_162 = TRUE; break; }
		$result = $res_155;
		$this->pos = $pos_155;
		$_162 = FALSE; break;
	}
	while(0);
	if( $_162 === TRUE ) { return $this->finalise($result); }
	if( $_162 === FALSE) { return FALSE; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = array()) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
	$_169 = NULL;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_169 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_169 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_169 = FALSE; break; }
		$_169 = TRUE; break;
	}
	while(0);
	if( $_169 === TRUE ) { return $this->finalise($result); }
	if( $_169 === FALSE) { return FALSE; }
}


/* Assignment: left: Mutable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = array()) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, null);
	$_176 = NULL;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_176 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_176 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_176 = FALSE; break; }
		$_176 = TRUE; break;
	}
	while(0);
	if( $_176 === TRUE ) { return $this->finalise($result); }
	if( $_176 === FALSE) { return FALSE; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = array()) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, null);
	$_185 = NULL;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_185 = FALSE; break; }
		while (true) {
			$res_184 = $result;
			$pos_184 = $this->pos;
			$_183 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_183 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_183 = FALSE; break; }
				$_183 = TRUE; break;
			}
			while(0);
			if( $_183 === FALSE) {
				$result = $res_184;
				$this->pos = $pos_184;
				unset( $res_184 );
				unset( $pos_184 );
				break;
			}
		}
		$_185 = TRUE; break;
	}
	while(0);
	if( $_185 === TRUE ) { return $this->finalise($result); }
	if( $_185 === FALSE) { return FALSE; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = array()) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, null);
	$_194 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_194 = FALSE; break; }
		while (true) {
			$res_193 = $result;
			$pos_193 = $this->pos;
			$_192 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_192 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_192 = FALSE; break; }
				$_192 = TRUE; break;
			}
			while(0);
			if( $_192 === FALSE) {
				$result = $res_193;
				$this->pos = $pos_193;
				unset( $res_193 );
				unset( $pos_193 );
				break;
			}
		}
		$_194 = TRUE; break;
	}
	while(0);
	if( $_194 === TRUE ) { return $this->finalise($result); }
	if( $_194 === FALSE) { return FALSE; }
}


/* Operand: ( ( "(" > core:Expression > ")" ) | core:FunctionCall | core:DereferencableValue ) ( ObjectResolutionOperator next:Operand )? */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = array()) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, null);
	$_217 = NULL;
	do {
		$_211 = NULL;
		do {
			$_209 = NULL;
			do {
				$res_196 = $result;
				$pos_196 = $this->pos;
				$_202 = NULL;
				do {
					if (substr($this->string,$this->pos,1) == '(') {
						$this->pos += 1;
						$result["text"] .= '(';
					}
					else { $_202 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_202 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					if (substr($this->string,$this->pos,1) == ')') {
						$this->pos += 1;
						$result["text"] .= ')';
					}
					else { $_202 = FALSE; break; }
					$_202 = TRUE; break;
				}
				while(0);
				if( $_202 === TRUE ) { $_209 = TRUE; break; }
				$result = $res_196;
				$this->pos = $pos_196;
				$_207 = NULL;
				do {
					$res_204 = $result;
					$pos_204 = $this->pos;
					$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_207 = TRUE; break;
					}
					$result = $res_204;
					$this->pos = $pos_204;
					$matcher = 'match_'.'DereferencableValue'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_207 = TRUE; break;
					}
					$result = $res_204;
					$this->pos = $pos_204;
					$_207 = FALSE; break;
				}
				while(0);
				if( $_207 === TRUE ) { $_209 = TRUE; break; }
				$result = $res_196;
				$this->pos = $pos_196;
				$_209 = FALSE; break;
			}
			while(0);
			if( $_209 === FALSE) { $_211 = FALSE; break; }
			$_211 = TRUE; break;
		}
		while(0);
		if( $_211 === FALSE) { $_217 = FALSE; break; }
		$res_216 = $result;
		$pos_216 = $this->pos;
		$_215 = NULL;
		do {
			$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_215 = FALSE; break; }
			$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "next" );
			}
			else { $_215 = FALSE; break; }
			$_215 = TRUE; break;
		}
		while(0);
		if( $_215 === FALSE) {
			$result = $res_216;
			$this->pos = $pos_216;
			unset( $res_216 );
			unset( $pos_216 );
		}
		$_217 = TRUE; break;
	}
	while(0);
	if( $_217 === TRUE ) { return $this->finalise($result); }
	if( $_217 === FALSE) { return FALSE; }
}


/* FunctionCall: function:VariableName "(" __ args:FunctionCallArgumentList? __ ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = array()) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, null);
	$_225 = NULL;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_225 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_225 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_225 = FALSE; break; }
		$res_222 = $result;
		$pos_222 = $this->pos;
		$matcher = 'match_'.'FunctionCallArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_222;
			$this->pos = $pos_222;
			unset( $res_222 );
			unset( $pos_222 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_225 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_225 = FALSE; break; }
		$_225 = TRUE; break;
	}
	while(0);
	if( $_225 === TRUE ) { return $this->finalise($result); }
	if( $_225 === FALSE) { return FALSE; }
}


/* FunctionCallArgumentList: skip:Expression ( __ "," __ skip:Expression )* */
protected $match_FunctionCallArgumentList_typestack = array('FunctionCallArgumentList');
function match_FunctionCallArgumentList ($stack = array()) {
	$matchrule = "FunctionCallArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_234 = NULL;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_234 = FALSE; break; }
		while (true) {
			$res_233 = $result;
			$pos_233 = $this->pos;
			$_232 = NULL;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_232 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_232 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_232 = FALSE; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_232 = FALSE; break; }
				$_232 = TRUE; break;
			}
			while(0);
			if( $_232 === FALSE) {
				$result = $res_233;
				$this->pos = $pos_233;
				unset( $res_233 );
				unset( $pos_233 );
				break;
			}
		}
		$_234 = TRUE; break;
	}
	while(0);
	if( $_234 === TRUE ) { return $this->finalise($result); }
	if( $_234 === FALSE) { return FALSE; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( > "," > skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = array()) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_243 = NULL;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_243 = FALSE; break; }
		while (true) {
			$res_242 = $result;
			$pos_242 = $this->pos;
			$_241 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_241 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_241 = FALSE; break; }
				$_241 = TRUE; break;
			}
			while(0);
			if( $_241 === FALSE) {
				$result = $res_242;
				$this->pos = $pos_242;
				unset( $res_242 );
				unset( $pos_242 );
				break;
			}
		}
		$_243 = TRUE; break;
	}
	while(0);
	if( $_243 === TRUE ) { return $this->finalise($result); }
	if( $_243 === FALSE) { return FALSE; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" > args:FunctionDefinitionArgumentList? > ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = array()) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_256 = NULL;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_256 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_256 = FALSE; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_256 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_256 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_256 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_251 = $result;
		$pos_251 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_251;
			$this->pos = $pos_251;
			unset( $res_251 );
			unset( $pos_251 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_256 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_256 = FALSE; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "body" );
		}
		else { $_256 = FALSE; break; }
		$_256 = TRUE; break;
	}
	while(0);
	if( $_256 === TRUE ) { return $this->finalise($result); }
	if( $_256 === FALSE) { return FALSE; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) > */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = array()) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_270 = NULL;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_270 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_270 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_270 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_270 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_270 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_270 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_270 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_270 = FALSE; break; }
		$_267 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_267 = FALSE; break; }
			$_267 = TRUE; break;
		}
		while(0);
		if( $_267 === FALSE) { $_270 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_270 = TRUE; break;
	}
	while(0);
	if( $_270 === TRUE ) { return $this->finalise($result); }
	if( $_270 === FALSE) { return FALSE; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) > */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = array()) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_284 = NULL;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		$_281 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_281 = FALSE; break; }
			$_281 = TRUE; break;
		}
		while(0);
		if( $_281 === FALSE) { $_284 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_284 = TRUE; break;
	}
	while(0);
	if( $_284 === TRUE ) { return $this->finalise($result); }
	if( $_284 === FALSE) { return FALSE; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = array()) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_301 = NULL;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_301 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_301 = FALSE; break; }
		if (( $subres = $this->literal( 'as' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "item" );
		}
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_301 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_301 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_301 = FALSE; break; }
		$_299 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_299 = FALSE; break; }
			$_299 = TRUE; break;
		}
		while(0);
		if( $_299 === FALSE) { $_301 = FALSE; break; }
		$_301 = TRUE; break;
	}
	while(0);
	if( $_301 === TRUE ) { return $this->finalise($result); }
	if( $_301 === FALSE) { return FALSE; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = array()) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_314 = NULL;
	do {
		$res_303 = $result;
		$pos_303 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_314 = TRUE; break;
		}
		$result = $res_303;
		$this->pos = $pos_303;
		$_312 = NULL;
		do {
			$res_305 = $result;
			$pos_305 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_312 = TRUE; break;
			}
			$result = $res_305;
			$this->pos = $pos_305;
			$_310 = NULL;
			do {
				$res_307 = $result;
				$pos_307 = $this->pos;
				$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_310 = TRUE; break;
				}
				$result = $res_307;
				$this->pos = $pos_307;
				$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_310 = TRUE; break;
				}
				$result = $res_307;
				$this->pos = $pos_307;
				$_310 = FALSE; break;
			}
			while(0);
			if( $_310 === TRUE ) { $_312 = TRUE; break; }
			$result = $res_305;
			$this->pos = $pos_305;
			$_312 = FALSE; break;
		}
		while(0);
		if( $_312 === TRUE ) { $_314 = TRUE; break; }
		$result = $res_303;
		$this->pos = $pos_303;
		$_314 = FALSE; break;
	}
	while(0);
	if( $_314 === TRUE ) { return $this->finalise($result); }
	if( $_314 === FALSE) { return FALSE; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = array()) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_319 = NULL;
	do {
		$res_316 = $result;
		$pos_316 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_319 = TRUE; break;
		}
		$result = $res_316;
		$this->pos = $pos_316;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_319 = TRUE; break;
		}
		$result = $res_316;
		$this->pos = $pos_316;
		$_319 = FALSE; break;
	}
	while(0);
	if( $_319 === TRUE ) { return $this->finalise($result); }
	if( $_319 === FALSE) { return FALSE; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = array()) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_324 = NULL;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_324 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_324 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_324 = FALSE; break; }
		$_324 = TRUE; break;
	}
	while(0);
	if( $_324 === TRUE ) { return $this->finalise($result); }
	if( $_324 === FALSE) { return FALSE; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = array()) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_331 = NULL;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_331 = FALSE; break; }
		$res_330 = $result;
		$pos_330 = $this->pos;
		$_329 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			else { $_329 = FALSE; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_329 = FALSE; break; }
			$_329 = TRUE; break;
		}
		while(0);
		if( $_329 === FALSE) {
			$result = $res_330;
			$this->pos = $pos_330;
			unset( $res_330 );
			unset( $pos_330 );
		}
		$_331 = TRUE; break;
	}
	while(0);
	if( $_331 === TRUE ) { return $this->finalise($result); }
	if( $_331 === FALSE) { return FALSE; }
}


/* Statement: skip:BlockStatements | skip:CommandStatements | skip:Expression */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = array()) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, null);
	$_340 = NULL;
	do {
		$res_333 = $result;
		$pos_333 = $this->pos;
		$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_340 = TRUE; break;
		}
		$result = $res_333;
		$this->pos = $pos_333;
		$_338 = NULL;
		do {
			$res_335 = $result;
			$pos_335 = $this->pos;
			$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_338 = TRUE; break;
			}
			$result = $res_335;
			$this->pos = $pos_335;
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_338 = TRUE; break;
			}
			$result = $res_335;
			$this->pos = $pos_335;
			$_338 = FALSE; break;
		}
		while(0);
		if( $_338 === TRUE ) { $_340 = TRUE; break; }
		$result = $res_333;
		$this->pos = $pos_333;
		$_340 = FALSE; break;
	}
	while(0);
	if( $_340 === TRUE ) { return $this->finalise($result); }
	if( $_340 === FALSE) { return FALSE; }
}


/* Block: "{" __ ( skip:Program )? __ "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = array()) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, null);
	$_349 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_349 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_349 = FALSE; break; }
		$res_346 = $result;
		$pos_346 = $this->pos;
		$_345 = NULL;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_345 = FALSE; break; }
			$_345 = TRUE; break;
		}
		while(0);
		if( $_345 === FALSE) {
			$result = $res_346;
			$this->pos = $pos_346;
			unset( $res_346 );
			unset( $pos_346 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_349 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_349 = FALSE; break; }
		$_349 = TRUE; break;
	}
	while(0);
	if( $_349 === TRUE ) { return $this->finalise($result); }
	if( $_349 === FALSE) { return FALSE; }
}


/* __: /([\s\n]*)/ */
protected $match____typestack = array('__');
function match___ ($stack = array()) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([\s\n]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* SEP: ";" | __ */
protected $match_SEP_typestack = array('SEP');
function match_SEP ($stack = array()) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule, null);
	$_355 = NULL;
	do {
		$res_352 = $result;
		$pos_352 = $this->pos;
		if (substr($this->string,$this->pos,1) == ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_355 = TRUE; break;
		}
		$result = $res_352;
		$this->pos = $pos_352;
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_355 = TRUE; break;
		}
		$result = $res_352;
		$this->pos = $pos_352;
		$_355 = FALSE; break;
	}
	while(0);
	if( $_355 === TRUE ) { return $this->finalise($result); }
	if( $_355 === FALSE) { return FALSE; }
}


/* Program: __ Statement __ ( SEP __ Statement __ )* SEP? __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = array()) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, null);
	$_368 = NULL;
	do {
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_368 = FALSE; break; }
		$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_368 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_368 = FALSE; break; }
		while (true) {
			$res_365 = $result;
			$pos_365 = $this->pos;
			$_364 = NULL;
			do {
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_364 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_364 = FALSE; break; }
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_364 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_364 = FALSE; break; }
				$_364 = TRUE; break;
			}
			while(0);
			if( $_364 === FALSE) {
				$result = $res_365;
				$this->pos = $pos_365;
				unset( $res_365 );
				unset( $pos_365 );
				break;
			}
		}
		$res_366 = $result;
		$pos_366 = $this->pos;
		$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else {
			$result = $res_366;
			$this->pos = $pos_366;
			unset( $res_366 );
			unset( $pos_366 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_368 = FALSE; break; }
		$_368 = TRUE; break;
	}
	while(0);
	if( $_368 === TRUE ) { return $this->finalise($result); }
	if( $_368 === FALSE) { return FALSE; }
}




}
