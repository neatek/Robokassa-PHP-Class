<?php
date_default_timezone_set('Europe/Moscow');
//date_default_timezone_set('Etc/GMT+3');
require_once 'Robokassa.class.php';
require_once 'Robokassa.recurrent.class.php';
$passwords = array(
    "PASS1", //0
    "PASS2", //1
    "PASS3", //2
    "PASS4", //3
);
$params = array(
    "merch_login",
    $passwords,
    'test'  => false,
    'debug' => true,
);
$robo = new RobokassaRecurrent($params);
