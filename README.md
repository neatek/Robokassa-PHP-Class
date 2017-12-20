# Robokassa-PHP-Class
Very simple and light PHP Class for working with Robokassa.ru ([Author page](http://neatek.pw/))

1) Creating class:
```
require_once 'Robokassa.class.php';
$passwords = array(
	"Pass1", 
	"Pass2", 
	"Pass3", 
	"Pass4"
);
$params = array(
	"MerchLogin",
	$passwords, 
	'test' => true, // enable test or not
	'debug' => true // logging into debug.log file
);
$robo = new Robokassa($params);
```
2) Get payment link
```
$robo->doRedirect(
	"100", // Sum
	"Description for payment...", // Payment description
	0, // InvoiceID
	array(
		// Here is 'SHP_PARAMS', without 'SHP_'
		//'param'=>'helloworld'
	), 
	'ru' // IncCurrLabel
);
```
3) Check payment, its really easy.
```
if($robo->isSuccess(0)) {
	$robo->get_shp_params();
	// payment is successfull
}
else {
	// wrong crc
}
```

# Support developer
Если я помог вам и вам это понравилось, можете немного вознаградить меня :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/neatek/3)

* Или так https://neatek.ru/support/ https://qiwi.me/neatek