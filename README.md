# Robokassa-PHP-Class
PHP Class for working with Robokassa.ru ([Author page](http://neatek.pw/))

1) Creating class:

```
require_once 'robo.php';

$params = array(
	"MerchLogin", // MerchLogin
	array(
		"Password_1",
		"Password_2",
		"Password_3", // testing passwords
		"Password_4"
	),
	true // ?!testing or not (true/false)
);

$robo = new Robokassa($params);
```
2) Get payment link
```
$redirect = $robo->getPayment(10.0, "Description for payment...");
```
or you can use getPayment with addtional parameters:
```
$params = array(
  "SHP_Param1" => "My value", 
  "SHP_Param1" => "My value2", 
);
$redirect = $robo->getPayment(10.0, "Description for payment...", $params);
```
3) Check payment, its really easy.
```
if($robo->isSuccess()) {
  echo 'Yeah payment is successful!';
}
```
or with additional parameters:
```
$params = array(
  "SHP_Param1" => "My value", 
  "SHP_Param1" => "My value2", 
);
if($robo->isSuccess($params)) {
  echo 'Yeah payment is successful!';
}
```


# Support developer
If you like my job (plugin) you can send me some $$$ on beer.

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/neatek/3)

* Для русских пользователей вы можете использовать ссылку https://neatek.ru/support/ (Yandex деньги)