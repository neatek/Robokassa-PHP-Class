<?php
class Robokassa {
	protected $MerchLogin = "";
	protected $Passwords = array();
	protected $Testing = false;
	protected $Debug = true;
	protected $SHP_params = array();
	function __construct($params) 
	{
		if(is_array($params)) 
		{
			if(!empty($params[0])) $this->MerchLogin = $params[0];
			if(is_array($params[1])) $this->Passwords = $params[1];
			if(isset($params['test'])) $this->Testing = (bool) $params['test'];
			else $this->Testing = (bool) $params[2];
			if(isset($params['debug'])) $this->Debug = (bool) $params['debug'];
		}
	}
	function genSig($sum, $invid = '0', $params=array(), $check = false, $forcepass = -1) {
		$pwdkey=0;
		if($check) {
			if($forcepass == -1) if($this->Testing) $pwdkey=3; else $pwdkey=1;
			else $pwdkey=$forcepass;
			$sig = $sum.":".$invid.":".$this->Passwords[$pwdkey];
		}
		else {
			if($forcepass == -1) if($this->Testing) $pwdkey=2; else $pwdkey=0;
			else $pwdkey=$forcepass;
			$sig = $this->MerchLogin.":".$sum.":".$invid.":".$this->Passwords[$pwdkey];
		}
		if(!empty($params)) foreach ($params as $key => $value) $sig .= ":shp_".$key."=".$value;
		if($this->Debug) if($check) $this->debug($sig." = ".md5(trim($sig)),'CHECK_SIGN');
		else $this->debug($sig." = ".md5(trim($sig)),'FIRST_CLEAR_SIGNATURE');
		return md5(trim($sig));
	}
	function debug($data=array(),$name='') {
		file_put_contents('debug'.date('dmY').'.log',date('[H:i:s] ').$name."\r\nRESULT:::\r\n".print_r($data,true)."\r\n=====\r\n",FILE_APPEND);
	}
	function doRedirect( $sum = 100, $desc = '', $invid = '0', $params = array(), $IncCurrLabel = 'ru'  ) {
		header("X-Redirect: Powered by neatek");
		header("Location: ".$this->getPayment($sum, $desc, $invid, $params, $IncCurrLabel));
	}
	function getPayment( $sum = 100, $desc = '', $invid = '0', $params = array(), $IncCurrLabel = 'ru'  ) {
		$email = ""; if(isset($params['email'])) $email = $params['email'];
		$signature = $this->genSig($sum, $invid, $params);
		$redirect_url = "http://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=".$this->MerchLogin."&OutSum=".$sum."&InvId=".$invid."&IncCurrLabel=".$IncCurrLabel."&InvDesc=".urlencode($desc) ."&Desc=".urlencode($desc) ."&Encoding=UTF-8&Email=".$email."&SignatureValue=".$signature;
		if($this->Testing) $redirect_url .= "&isTest=1";
		if(!empty($params)) foreach ($params as $key => $value) $redirect_url .= "&shp_".$key."=".urlencode($value);
		if($this->Debug) $this->debug('PAYMENT_URL: '.$redirect_url."\r\n");
		return $redirect_url;
	}
	function get_shp_params_request($request = array()) {
		if(empty($request)) $request = $_REQUEST;
		$params = array();
		if(!empty($request)) {
			foreach ($request as $key => $value) {
				if(strpos($key, 'shp_') !== false) {
					$x = str_replace('shp_', '', $key);
					$params[$x] = $value;
				}
			}
		}
		if($this->Debug) $this->debug('WE_GOT_REQUEST_SHP_PARAMS: '.print_r($params, true));
		return $params;
	}
	function get_shp_params() {
		return $this->SHP_params;
	}
	function compareCRC() {
		if(isset($_REQUEST["OutSum"]) && isset($_REQUEST["InvId"]) && isset($_REQUEST["SignatureValue"])) {
			$invid = (string) $_REQUEST['InvId'];
			$crc = strtoupper($_REQUEST["SignatureValue"]);
			$shp_params = $this->get_shp_params_request();
			for($x=0;$x<=3;$x++) {
				$gensign=strtoupper($this->genSig($_REQUEST["OutSum"], $invid, $shp_params, true, $x));
				if($this->Debug) $this->debug('COMPARE_CRC: '.$gensign.' ?= '.$crc);
				if(strcmp($gensign,$crc) == 0) {
					$this->SHP_params = $shp_params;
					return true;
				}
			}
		}
		return false;
	}
	function isSuccess() {
		if($this->compareCRC()) return true;
		return false;
	}
}