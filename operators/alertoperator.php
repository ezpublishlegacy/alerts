<?php

	class AlertOperator
{
	var $Operators;

	static protected $alert=null;

	function AlertOperator(){
		$this->Operators = array("alert_count", "alert_list");
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}
	
	function namedParameterList(){
		return array(
			'alert_count' => array(
				'params' => array('type'=>'array', 'required'=>false, 'default'=>array())
				),
			'alert_list' => array(
				'params' => array('type'=>'array', 'required'=>false, 'default'=>array())
				)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		if(!self::$alert){
			self::$alert=eZContentObjectTreeNode::subTreeByNodeID(array('Depth'=>1,'SortBy'=>array('priority',true),'ClassFilterType'=>'include','ClassFilterArray'=>array('alert')),$operatorValue);
		}
		$operatorValue=($operatorName=='alert_count')?count(self::$alert):self::$alert;
		return true;
	}
}

?>

