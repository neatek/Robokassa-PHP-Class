<?php
/***
Example:

- Redirect to do payment
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
	$robo->doRedirect("100", "Description for payment...", 0, array(), 'ru'); // Here 0 is a InvoiceID, array() => 'sh_' params, and IncCurrLabel
- Check Success
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
		true // Test or not test
	);
	$robo = new Robokassa($params);
	if($robo->isSuccess(0)) { // Here 0 is a InvoiceID
		file_put_contents('results.log', 'Yeah payment is successful!'."\r\n", FILE_APPEND);
	}
	else {
		file_put_contents('results.log', 'Oh no! payment is not successful!'."\r\n", FILE_APPEND);
	}
***/
class Robokassa {
	protected $MerchLogin = "";
	protected $Passwords = array();
	protected $Testing = false;
	protected $Debug = false; // Here you can enable DEBUG for output in debug.log

	function __construct($params) 
	{
		if(is_array($params)) 
		{
			if(!empty($params[0])) $this->MerchLogin = $params[0];
			if(is_array($params[1])) 
			{
				$xx = count($params[1]);
				for( $x = 0; $x < $xx; $x++ )
					$this->Passwords[$x] = (string) $params[1][$x];
			}
			$this->Testing = (bool) $params[2];
		}
	}
	function genSig($sum, $invid = 0, $params=array(), $check = false) {
		if($check == false) {
			if($this->Testing  == true) $sig = $this->MerchLogin.":".$sum.":".$invid.":".$this->Passwords[2];
			else $sig = $this->MerchLogin.":".$sum.":".$invid.":".$this->Passwords[0];
		}
		else {
			if($this->Testing  == true) $sig = $sum.":".$invid.":".$this->Passwords[3];
			else $sig = $sum.":".$invid.":".$this->Passwords[1];
		}
		if(!empty($params))
		{
			foreach ($params as $key => $value) {
				$sig .= ":".$key."=".$value;
			}
		}
		if($this->Debug == true) {
			if($check == false) {
				$this->debug($sig." = ".md5(trim($sig)),'FIRST_CLEAR_SIGNATURE');
			}
			else {
				$this->debug($sig." = ".md5(trim($sig)),'CHECK_SIGN');
			}
		}
		return md5(trim($sig));
	}

	function debug($data=array(),$name='') {
		file_put_contents('debug.log', date('[H:i:s] ').$name."\r\nRESULT:::\r\n".print_r($data,true)."\r\n=====\r\n",FILE_APPEND);
	}

	function doRedirect( $sum = 100, $desc = '', $invid = 0, $params = array(), $IncCurrLabel = 'ru'  ) {
		header("X-Redirect: Powered by neatek");
		header("Location: ".$this->getPayment($sum, $desc, $invid, $params, $IncCurrLabel));
	}

	function getPayment( $sum = 100, $desc = '', $invid = 0, $params = array(), $IncCurrLabel = 'ru'  ) {
		$signature = $this->genSig($sum, $invid, $params);
		$redirect_url = "http://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=".$this->MerchLogin."&OutSum=".$sum."&InvId=".$invid."&IncCurrLabel=".$IncCurrLabel."&InvDesc=".urlencode($desc) ."&SignatureValue=".$signature;
		if($this->Testing) {
			$redirect_url .= "&isTest=1";
		}
		if(!empty($params))
		{
			foreach ($params as $key => $value)
				$redirect_url .= "&".$key."=".$value;
		}
		return $redirect_url;
	}

	function isSuccess($invid = 0, $params=array()) {
		if(isset($_REQUEST["OutSum"]) && isset($_REQUEST["InvId"]) && isset($_REQUEST["SignatureValue"]))
		{
			$crc = strtoupper($_REQUEST["SignatureValue"]);
			$my_crc = strtoupper($this->genSig($_REQUEST["OutSum"], $_REQUEST["InvId"], $params, true));
			if($this->Debug == true) {
				$this->debug('GENERATED: '.$my_crc."\r\n".'NEEDED_CRC: '.$crc,'CHECK_CRC');
			}
			if(strcmp($my_crc,$crc) == 0) {
				return true;
			}
		}
		return false;
	}
}

