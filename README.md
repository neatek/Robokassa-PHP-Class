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
		true // Test or not test (true/false)
	);
	$robo = new Robokassa($params);
```
2) Get payment link
```
	$robo->doRedirect("100", "Description for payment...", 0, array(), 'ru'); // Here 0 is a InvoiceID, array() => 'sh_' params, and IncCurrLabel
```
3) Check payment, its really easy.
```
	if($robo->isSuccess(0)) { // Here 0 is a InvoiceID
		file_put_contents('results.log', 'Yeah payment is successful!'."\r\n", FILE_APPEND);
	}
	else {
		file_put_contents('results.log', 'Oh no! payment is not successful!'."\r\n", FILE_APPEND);
	}
```

# Support developer
Если я помог вам и вам это понравилось, можете немного вознаградить меня :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/neatek/3)

* Или так https://neatek.ru/support/ https://qiwi.me/neatek