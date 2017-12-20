<?php
require_once '../Robokassa.class.php';
include 'Robokassa.params.php';
$robo->doRedirect(
	"100", 
	"Description for payment...", 
	0, // InvID
	array(
		// Here is 'SHP_PARAMS', without 'SHP_'
		//'param'=>'helloworld'
	), 
	'ru' // IncCurrLabel
);