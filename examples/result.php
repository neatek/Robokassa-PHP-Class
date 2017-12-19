<?php
require_once '../Robokassa.class.php';
include 'Robokassa.params.php';
file_put_contents('results.log', print_r($_REQUEST,true)."\r\n", FILE_APPEND);
if($robo->isSuccess(0)) {
	file_put_contents('results.log', 'Yeah payment is successful!'."\r\n", FILE_APPEND);
}
else {
	file_put_contents('results.log', 'Oh no! payment is not successful!'."\r\n", FILE_APPEND);
}