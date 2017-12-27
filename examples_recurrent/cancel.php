<?php
//cancel for recurrent payments
header('Content-type: text/html; charset=utf-8');
include 'Robokassa.params.php';
if (!isset($_GET['hash']) || !isset($_GET['invid']) || empty($_GET['hash']) || empty($_GET['invid']) || strlen($_GET['hash']) != 32) {
    die('Попробуйте перейти по ссылке из письма еще раз.');
}
$recurrent = $robo->getRecurrent($_GET['invid']);
$invid     = (int) $_GET['invid'];
if (!empty($recurrent)) {
    if (strcmp(md5($_GET['invid'] . $recurrent['email']), $_GET['hash']) == 0) {
        $robo->cancelRecurrent($invid); // do cancel
        die('Отписка от регулярных платежей успешно совершена.');
    } else {
        die('Попробуйте перейти по ссылке из письма еще раз.');
    }
} else {
    die('К сожалению данного платежа не найдено.');
}
