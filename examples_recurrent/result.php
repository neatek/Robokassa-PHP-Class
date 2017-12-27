<?php
header('Content-type: text/html; charset=utf-8');
include 'Robokassa.params.php';
if ($robo->isSuccess()) {
    $shp_params = $robo->get_shp_params();
    $robo->setPaymentSuccess($_REQUEST['InvId']);
    $inv_id = (int) $_REQUEST['InvId'];
    // success payment once & regular
    if (isset($shp_params['recurrent']) && !empty($shp_params['recurrent'])) {
        $v = (int) $shp_params['recurrent'];
        if ($v > 0) {
            // if first recurrent payment
        }
    }
    echo "OK$inv_id\n";
} else {
    echo "bad sign\n";
    exit();
}
