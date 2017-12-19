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
	true // test or not test
);
$robo = new Robokassa($params);
