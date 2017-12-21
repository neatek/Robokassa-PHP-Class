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
	"100", // outsum
	"Description for payment...", // payment desc
	1, // invoice_id
	array(
		'param'=>'helloworld' // without shp_
	), 
	'ru' // IncCurrLabel
);
```
3) Check payment, its really easy.
```
// ResultURL
if($robo->isSuccess()) {
	// we got shp_ params without shp_
	$shp_params = $robo->get_shp_params();
	file_put_contents('results.log', 'Yeah payment is successful!'."\r\n".print_r($shp_params,true), FILE_APPEND);
}
else {
	file_put_contents('results.log', 'Oh no! payment is not successful!'."\r\n", FILE_APPEND);
}
```

# Support developer
Если я помог вам и вам это понравилось, можете немного вознаградить меня :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/neatek/3)

* Или так https://neatek.ru/support/ https://qiwi.me/neatek