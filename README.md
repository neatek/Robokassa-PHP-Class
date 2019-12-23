> Very simple and light PHP Class for working with Robokassa.ru 
> Author web-site - [https://neatek.ru/](https://neatek.ru/)

[![HitCount](http://hits.dwyl.io/neatek/Robokassa-PHP-Class.svg)](http://hits.dwyl.io/neatek/Robokassa-PHP-Class)

## Robokassa.class.php (Once payment)

Function | What it does
------------ | -------------
$robo = new Robokassa(array("MerchLogin", array('pass1','pass2','pass3','pass4'), 'test'=>true, 'debug'=>true)) | Create class
$robo->doRedirect($sum = '100', $desc = 'Text', $invid = '0', $shp_params = array(), $IncCurrLabel = 'ru') | Redirect to do payment on Robokassa web-site,    **do not use 'shp_' into $shp_params**
$robo->isSuccess() | Check if payment successful - params : __no params__.
$robo->get_shp_params() | You can get all shp_ params without = '_shp' - params: __no params__.

1) Create class
```php
require_once 'Robokassa.class.php';
$params = array(
	"MerchLogin",
	array(
		"Pass1", 
		"Pass2", 
		"Pass3", 
		"Pass4"
	), 
	'test' => true, // enable test or not
	'debug' => true // logging into debug.log file
);
$robo = new Robokassa($params);
```
2) Redirect to do payment
```php
require_once 'Robokassa.class.php';
$robo->doRedirect(
	"100", // outsum
	"Description for payment...", // payment desc
	1, // invoice_id
	array(
		'param'=>'helloworld' // without shp_

		// you can get this into ResultURL $robo->get_shp_params();
		'email'=>'email_from_form' 
	), 
	'ru' // IncCurrLabel
);
```
3) Check payment
```php
require_once 'Robokassa.class.php';
// ResultURL
if($robo->isSuccess()) {
	// we got shp_ params without shp_
	$shp_params = $robo->get_shp_params();
	// your code
}
```

## Robokassa.recurrent.class.php (Recurrent payment)

See: ./examples_recurrent, create database and run SQL from readme.txt file, also edit database config in Robokassa.recurrent.class.php

Function | What it does
------------ | -------------
$robo = new Robokassa(array("MerchLogin", array('pass1','pass2','pass3','pass4'), 'test'=>true, 'debug'=>true)) | Create class
$robo->doRecurrentRedirect($sum = '100', $desc = 'Text', $invid = '0', $shp_params = array(), $IncCurrLabel = 'ru') | Redirect to do payment on Robokassa web-site, with recurrent support,    **do not use 'shp_' into $shp_params**
$robo->isSuccess() | Check if payment successful - params : __no params__.
$robo->get_shp_params() | You can get all shp_ params into ResultURL without = '_shp' - params: __no params__.
$robo->doRecurrents() | You can get recurrent payments via this function by Crontab */1 every minute
$recurrent=$robo->getRecurrent($_GET['invid']) | You can get recurrent info about payment
$robo->cancelRecurrent($invid) | Cancel recurrent payment for Invid

```php
date_default_timezone_set('Europe/Moscow');
require_once 'Robokassa.class.php';
require_once 'Robokassa.recurrent.class.php'; // Extends Robokassa.class.php
```

```php
require_once 'Robokassa.params.php';
$recurrent = 1; // you can handle it from $_GET or $_POST
$shp_params = array(
	'email'=> trim($_REQUEST['nd_email']), // email from $_GET or $_POST
	'recurrent' => $recurrent
);
$robo->doRecurrentRedirect(
	$_REQUEST['nd_sum'], // data from your form
	"Описание платежа", 
	rand(0,99), // invid will be automatic from Database
	$shp_params,
	'ru',
	$recurrent
);
```

ResultURL - check if payment success

```php
require_once 'Robokassa.params.php';
if($robo->isSuccess()) {
	$shp_params = $robo->get_shp_params();
	$robo->setPaymentSuccess($_REQUEST['InvId']);
	$inv_id = (int) $_REQUEST['InvId'];
	// success payment once & regular
	if(isset($shp_params['recurrent']) && !empty($shp_params['recurrent'])) {
		$v = (int) $shp_params['recurrent'];
		if($v > 0) {
			// if first recurrent payment
		}
	}
	echo "OK$inv_id\n";
}
else {
	echo "bad sign\n";
	exit();
}
```

Crontab for getting automatic payments

```php
require_once 'Robokassa.params.php';
$robo->doRecurrents();
echo 'cronjob finished.';
```

# Support developer
Если я помог вам и вам это понравилось, можете немного вознаградить меня :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/neatek/3)

* Или так https://neatek.ru/support/ https://qiwi.me/neatek
