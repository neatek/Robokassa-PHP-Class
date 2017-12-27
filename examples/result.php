<?php
require_once '../Robokassa.class.php';
include 'Robokassa.params.php';
// ResultURL
if ($robo->isSuccess()) {
    $shp_params = $robo->get_shp_params(); // we got shp_ params without shp_
    file_put_contents('results.log', 'Yeah payment is successful!' . "\r\n" . print_r($shp_params, true), FILE_APPEND);
} else {
    file_put_contents('results.log', 'Oh no! payment is not successful!' . "\r\n", FILE_APPEND);
}
