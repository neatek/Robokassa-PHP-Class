<?php
// data from POST method of <form>
/*
$_REQUEST['nd_email']
$_REQUEST['nd_sum']
$_REQUEST['nd_regular'] // if payment is recurrent
$_REQUEST['nd_once'] // if payment is not regular (recurrent)
$_REQUEST['nd_agreed'] // agreement
*/
if($_REQUEST && isset($_REQUEST['nd_email']) && filter_var($_REQUEST['nd_email'], FILTER_VALIDATE_EMAIL) && isset($_REQUEST['nd_sum']) && ( isset($_REQUEST['nd_regular']) || isset($_REQUEST['nd_once']) ) && isset($_REQUEST['nd_agreed'])):
	include 'Robokassa.params.php';
	$recurrent = 0;
	if(isset($_REQUEST['nd_regular'])) {
		$recurrent = 1;
	}
	$shp_params = array(
		'email'=> trim($_REQUEST['nd_email']),
		'recurrent' => $recurrent
	);
	$robo->doRecurrentRedirect(
		$_REQUEST['nd_sum'], 
		"Описание платежа", 
		rand(0,99), // invid will be automatic from Database
		$shp_params,
		'ru',
		$recurrent
	);
else:
	header('Location: ' . $_SERVER['HTTP_REFERER']);
endif;