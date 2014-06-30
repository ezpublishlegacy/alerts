<?php
header('Content-Type: application/json');
$ModuleTools = ModuleTools::initialize($Params);
$ModuleTools->isCacheable(true);

$ContentNode = eZContentObjectTreeNode::fetch($SourceNodeID);

if (!is_object($ContentNode)) {
	return array(
		'count' => 0,
		'content' => ''
	);
}

$ContentClass = eZContentClass::fetch($ClassID);
$RootNodeID = eZINI::instance('content.ini')->variable('NodeSettings', 'RootNode');
$Settings = eZINI::instance('alert.ini')->group('AlertSettings');

$ContentPath = array_reverse($ContentNode->pathArray());
$Continue = true;

if($Settings['SortType'] == 'tree' || $Settings['SortType'] == 'current'){
	$FetchParameters = array(
		'Depth' => 1,
		'ClassFilterType' => 'include',
		'ClassFilterArray' => array($ContentClass->Identifier)
	);

	$SortArray = $ContentNode->sortArray();
	if($SortArray[0][0] == 'priority'){
		$FetchParameters = array_merge($FetchParameters, array(
			'SortBy' => array('priority', true)
		));
	}
	$ItemsNodeList = array();
	do{
		$PathNodeID = current($ContentPath);

		if($ContentNode->NodeID != $PathNodeID){
			$FetchParameters = array_merge($FetchParameters, array(
				'AttributeFilter' => array(array($ContentClass->Identifier.'/persistent', '=', 1))
			));
		}

		if($Items = eZContentObjectTreeNode::subTreeByNodeID($FetchParameters, $PathNodeID)){
			if($Settings['SortType'] == 'current'){
				foreach($Items as $Item) {
					array_unshift($ItemsNodeList, $Item);
					continue;
				}
			}else{
				$ItemsNodeList = array_merge($ItemsNodeList, $Items);
			}
		}

		if($PathNodeID == $RootNodeID || next($ContentPath) === false){
			$Continue = false;
		}

	}while($Continue);
}

if($Settings['SortType'] == 'severity'){
	$high_root = array();
	$CurrentNodeList = array();
	$PersistParameters = array(
		'Depth' => 1,
		'SortBy' => array('priority', true),
		'ClassFilterType' => 'include',
		'ClassFilterArray' => array($ContentClass->Identifier),
		'AttributeFilter' => array(array($ContentClass->Identifier.'/persistent', '=', 1))
	);
	$FetchParameters = array(
		'Depth' => 1,
		'SortBy' => array('priority', true),
		'ClassFilterType' => 'include',
		'ClassFilterArray' => array($ContentClass->Identifier)
	);
	
    foreach($ContentPath as $key => $alert_node){
		if($key == 0){
			$Items = eZContentObjectTreeNode::subTreeByNodeID($FetchParameters, $alert_node);
			$CurrentNodeList = array_merge($CurrentNodeList, $Items);
		}
		if($key != 0 && $alert_node != 1 && $alert_node != 2){

			$Items = eZContentObjectTreeNode::subTreeByNodeID($PersistParameters, $alert_node);
			$CurrentNodeList = array_merge($CurrentNodeList, $Items);
		}
		if($alert_node == $RootNodeID){
			$Items = eZContentObjectTreeNode::subTreeByNodeID($PersistParameters, $alert_node);
			foreach($Items as $i => $Item){
				$Datamap = $Item->dataMap();
				foreach($Settings['SortArray'] as $o => $match){
					if($Datamap['background_color']->SortKeyString == $match && $o > 1){
						array_push($high_root, $Item);
						unset($Items[$i]);
					}
				}
			}
			$CurrentNodeList = array_merge($CurrentNodeList, $Items);
		}
		if($alert_node == $RootNodeID) break;
	}
	
	$CurrentNodeList = array_merge($high_root, $CurrentNodeList);

	$Items = $ModuleTools->fetchResult(array(
		'variables' => array(
			'items' => $CurrentNodeList
		)
	));

	return array(
		'count' => count($CurrentNodeList),
		'content' => $Items['content']
	);
}

$Items = $ModuleTools->fetchResult(array(
	'variables' => array(
		'items' => $ItemsNodeList
	)
));

return array(
	'count' => count($ItemsNodeList),
	'content' => $Items['content']
);

?>