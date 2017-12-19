<?php
require_once '../Robokassa.class.php';
include 'Robokassa.params.php';
$robo->doRedirect("100", "Description for payment...", 0, array(), 'ru');