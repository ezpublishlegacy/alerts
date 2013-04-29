<?php
$Module = array(
	'name' => 'Alerts',
	'variable_params' => true
);

$ViewList = array();

$ViewList['items'] = array(
	'name' => 'Alert Items',
	'script' => 'items.php',
	'params' => array('ClassID', 'SourceNodeID')
);


?>