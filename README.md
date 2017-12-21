# ROBOKASSA-PHP-CLASS (2017)

> Very simple and light PHP Class for working with Robokassa.ru 
> Author web-site - [https://neatek.ru/](https://neatek.ru/)

Function | What it does
------------ | -------------
$robo = new Robokassa(array("MerchLogin", array('pass1','pass2','pass3','pass4'), 'test'=>true, 'debug'=>true)) | Create class
$robo->doRedirect($sum = '100', $desc = 'Text', $invid = '0', $shp_params = array(), $IncCurrLabel = 'ru') | Redirect to do payment on Robokassa web-site, __do not use 'shp_' into $shp_params__
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
$robo->doRedirect(
	"100", // outsum
	"Description for payment...", // payment desc
	1, // invoice_id
	array(
		'param'=>'helloworld' // without shp_
	), 
	'ru' // IncCurrLabel
);
```
3) Check payment
```php
// ResultURL
if($robo->isSuccess()) {
	// we got shp_ params without shp_
	$shp_params = $robo->get_shp_params();
	// your code
}
```

# Support developer
Если я помог вам и вам это понравилось, можете немного вознаградить меня :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/neatek/3)

* Или так https://neatek.ru/support/ https://qiwi.me/neatek