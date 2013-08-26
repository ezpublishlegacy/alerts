<?php
header('Content-Type: application/json');
$ModuleTools = ModuleTools::initialize($Params);
$ModuleTools->isCacheable(true);

$ContentNode = eZContentObjectTreeNode::fetch($SourceNodeID);
$ContentClass = eZContentClass::fetch($ClassID);
$RootNodeID = eZINI::instance('content.ini')->variable('NodeSettings', 'RootNode');

$ContentPath = array_reverse($ContentNode->pathArray());
$Continue = true;

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
		if($TreeSort == 'true'){
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