<?php

class Robokassa {
	// Robokassa Login
	protected $MerchLogin = "";
	// Passwords (2,3 - testing password)
	protected $Passwords = array();
	// Sandbox
	protected $Testing = false;

	/*
		Params (array)
			- MerchLogin (string)
			- Password (array)
			- Test
	*/

	function __construct($params) 
	{
		if(is_array($params)) 
		{
			// Check MerchLogin and collect
			if(!empty($params[0]))
				$this->MerchLogin = $params[0];
			// Check password, and collect
			if(is_array($params[1])) 
			{
				$xx = count($params[1]);
				for( $x = 0; $x < $xx; $x++ )
					$this->Passwords[$x] = (string) $params[1][$x];
			}
			// Check test parameter
			$this->Testing = (bool) $params[2];
		}
	}

	function getData() {

		$out = array();

		$out[0] = $this->MerchLogin;
		$out[1] = $this->Passwords;
		$out[2] = $this->Testing;

		echo json_encode($out);
	}

	function genSig($sum, $params) {
		$signature = array(
			(string) $this->MerchLogin,
			(float) $sum,
			(integer) 0
		);

		if($this->Testing)
		{
			$signature[3] = $this->Passwords[2];
		}
		else
		{
			$signature[3] = $this->Passwords[0];
		}

		$sig = "";
		$xx = count($signature);
		for($x=0;$x<$xx;$x++)
		{
			if(empty($sig)) 
				$sig .= $signature[$x];
			else 
				$sig .= ":".$signature[$x];
		}

		if(!empty($params))
		{
			foreach ($params as $key => $value)
				$sig .= ":".$key."=".$value;
		}

		return md5(trim($sig));
	}

	function getPayment($sum, $desc, $params) {

		$signature = $this->genSig($sum, $params);
		$redirect_url = "http://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=".$this->MerchLogin."&OutSum=".$sum."&InvId=0&IncCurrLabel=ru&Desc=". urlencode($desc) ."&SignatureValue=".$signature;

		if($this->Testing) 
			$redirect_url .= "&isTest=1";

		if(!empty($params))
		{
			foreach ($params as $key => $value)
				$redirect_url .= "&".$key."=".$value;
		}

		return $redirect_url;
	}

	function isSuccess($params) {
		if($_REQUEST['rbs_responce'] == 'result')
		{
			$signature = array(
				$_REQUEST["OutSum"],
				$_REQUEST["InvId"],
			);
			
			if($this->Testing)
			{
				$signature[2] = $this->Passwords[2];
			}
			else
			{
				$signature[2] = $this->Passwords[0];
			}

			$sig = "";

			for ($x=0; $x < 3; $x++) { 
				if(empty($sig)) 
					$sig .= $signature[$x];
				else 
					$sig .= ":".$signature[$x];
			}

			if(!empty($params))
			{
				foreach ($params as $key => $value)
					$sig .= ":".$key."=".$value;
			}

			$crc = $_REQUEST["SignatureValue"];
			$crc = strtoupper($crc);
			$my_crc = strtoupper(md5($sig));
			if(strcmp($my_crc,$crc) == 0)
				return true;
		}

		return false;
	}


}