<?php
$passwords = array(
	"Pass1", 
	"Pass2", 
	"Pass3", 
	"Pass4"
);
$params = array(
	"MerchLogin",
	$passwords, 
	'test' => true,
	'debug' => true
);
$robo = new Robokassa($params);
