<?php
require_once '../Robokassa.class.php';
include 'Robokassa.params.php';
$robo->doRedirect(
    "100", // outsum
    "Description for payment...", // payment desc
    1, // invoice_id
    array(
        'param' => 'helloworld', // without shp_
    ),
    'ru' // IncCurrLabel
);
