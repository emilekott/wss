<?php

$autoLoadConfig[200][] = array('autoType' => 'class',
	'loadFile' => 'observers/class.ceon_manual_cardSessionCleaner.php');
	
$autoLoadConfig[200][] = array('autoType' => 'classInstantiate',
	'className' => 'ceon_manual_cardSessionCleaner',
	'objectName' => 'ceon_manual_card_session_cleaner');

?>