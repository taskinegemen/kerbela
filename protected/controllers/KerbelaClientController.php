<?php

class KerbelaClientController extends Controller
{
	public function actionIndex()
	{

    
		$this->render('index');

	}
	public function actionStep1(){
///////////////////////////////////////////////////////////////////////////////////////
		$url = 'http://kerbela.lindneo.com/api/authenticate/';
		$fields = array(
						'user_id' => urlencode('egemen@linden-tech.com'),
						'requested_service' => urlencode('kerbela'),
						'ip' => urlencode('31.210.53.80'),//31.210.53.80
						'requested_lifetime' => urlencode('6000')
				);
		$step1=CJSON::decode($this->makeRequest($url,$fields));
		$TGT=$step1['TGT'];
		$TGT_client=base64_decode($step1['TGT_client']);
		$TGS_client_key=hash('sha256','12548442');//password of user
		$TGT_client_decrypted=CJSON::decode($this->decrypt($TGT_client,$TGS_client_key));
		//print_r($TGT_client);
		//print_r($TGT_client_decrypted);
///////////////////////////////////////////////////////////////////////////////////////
		$TGS_session_key=$TGT_client_decrypted['TGS_session_key'];
		$url = 'http://kerbela.lindneo.com/api/ticketgrant';
		$AUTH=$this->encrypt(CJSON::encode(array('user_id'=>'egemen@linden-tech.com','timestamp'=>time())),$TGS_session_key);

		$fields = array(
						'requested_http_service' => urlencode('koala'),
						'requested_lifetime' => urlencode($TGT_client_decrypted['requested_lifetime']),
						'auth'=>urlencode(base64_encode($AUTH)),
						'tgt'=>urlencode($TGT)
				);
		$step2=CJSON::decode($this->makeRequest($url,$fields));
		//print_r($step2);
		$HTTP_service_ticket=$step2['HTTP_service_ticket'];
		$HTTP_session_ticket=base64_decode($step2['HTTP_session_ticket']);
		$HTTP_session_ticket_decrypted=CJSON::decode($this->decrypt($HTTP_session_ticket,$TGS_session_key));
		//print_r($HTTP_session_ticket_decrypted);die();
///////////////////////////////////////////////////////////////////////////////////////
		$HTTP_service_session_key=$HTTP_session_ticket_decrypted['HTTP_service_session_key'];
		//$url='http://kerbela.lindneo.com/httpservice/authenticate';
		$url='http://koala.lindneo.com/api/authenticate';
		$AUTH=$this->encrypt(CJSON::encode(array('user_id'=>'egemen@linden-tech.com','timestamp'=>time())),$HTTP_service_session_key);
		$fields = array(
						'auth'=>urlencode(base64_encode($AUTH)),
						'http_service_ticket'=>urlencode($HTTP_service_ticket)
				);
		$step3=CJSON::decode($this->decrypt(base64_decode($this->makeRequest($url,$fields)),$HTTP_service_session_key));
		$requested_service=$step3['requested_http_service'];//koala
		$timestamp=$step3['timestamp'];//current time stamp is larger but at most 2 minutes
		print_r($step3);
///////////////////////////////////////////////////////////////////////////////////////
		//for future request, use httpserviceticket and new auth:D
		sleep(1);
		//$url='http://kerbela.lindneo.com/httpservice/service';
		$url='http://koala.lindneo.com/api/service';
		$AUTH=$this->encrypt(CJSON::encode(array('user_id'=>'egemen@linden-tech.com','timestamp'=>time())),$HTTP_service_session_key);
		$fields = array(
						'auth'=>urlencode(base64_encode($AUTH)),
						'http_service_ticket'=>urlencode($HTTP_service_ticket)
				);
		$step4=CJSON::decode($this->decrypt(base64_decode($this->makeRequest($url,$fields)),$HTTP_service_session_key));
		$requested_service=$step4['requested_http_service'];//koala
		$timestamp=$step4['timestamp'];//current time stamp is larger but at most 2 minutes
		print_r($step4);






	}
	public function makeRequest($url,$fields){
		
			$fields_string="";
			//url-ify the data for the POST
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');

			//echo $fields_string;
			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

			//execute post
			$result = curl_exec($ch);

			//close connection
			curl_close($ch);
			return $result;
	}
	private function encrypt($data,$key){
		return openssl_encrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));
		//return openssl_encrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}
	private function decrypt($data,$key){
		return openssl_decrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));   
		//return openssl_decrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}
}