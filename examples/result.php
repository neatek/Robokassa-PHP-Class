<?php
require_once '../Robokassa.class.php';
include 'Robokassa.params.php';
if($robo->isSuccess(0)) {
	$robo->get_shp_params();
	file_put_contents('results.log', 'Yeah payment is successful!'."\r\n", FILE_APPEND);
}
else {
	file_put_contents('results.log', 'Oh no! payment is not successful!'."\r\n", FILE_APPEND);
}