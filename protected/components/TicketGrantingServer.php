<?php
class TicketGrantingServer{
	public $requested_http_service;
	public $requested_lifetime;
	public $auth;
	public $tgt;
	public $encryptionLib;

	public function __construct($requested_http_service,$requested_lifetime,$auth,$tgt,$encryptionLib){
		$this->setSuper($requested_http_service,$requested_lifetime,$auth,$tgt,$encryptionLib);

	}
	private function setSuper($requested_http_service,$requested_lifetime,$auth,$tgt,$encryptionLib){
		$this->setRequestedHttpService($requested_http_service);
		$this->setRequestedLifetime($requested_lifetime);
		$this->setAuth($auth);
		$this->setTgt($tgt);
		$this->setEncryptionLib($encryptionLib);
	}
	private function decoder($string){

		if(!$this->isJson($string)){
		$json=array();
		$string=str_replace('{','',$string);
		$string=str_replace('}','',$string);
		$string=explode(',',$string);
		for($i=0;$i<sizeof($string);$i++){
			$item=explode(':',$string[$i]);
			//$json[$item[0].'']=trim($item[1]);
			$item[0]=utf8_encode($item[0]);
			$item[1]=utf8_encode($item[1]);
			$json+=array($item[0]=>$item[1]);
		}
		$json+=array('user_id'=>'sdfdsf');
		return CJSON::encode($json);
	}
		else
			{
				
				return $string;
			}
	}

	private function isJson($string) {
 			json_decode($string);
 			return (json_last_error() == JSON_ERROR_NONE);
	}

	public function ticketgrant(){


		if($this->is_http_service_available())
		{
			if(!$this->is_replay_attack()){
				$TGS_secret_key=$this->getTGSSecretKey();
				error_log('TGT decrypted');
				error_log(print_r($this->decoder($this->getEcryptionLib()->decrypt($this->getTgt(),$TGS_secret_key)),1));

				$TGT_decrypted=CJSON::decode($this->decoder($this->getEcryptionLib()->decrypt($this->getTgt(),$TGS_secret_key)));
				$TGS_session_key=$TGT_decrypted['TGS_session_key'];
				error_log('TGS session key');
				error_log($TGS_session_key);
				$AUTH_decrypted=CJSON::decode($this->decoder($this->getEcryptionLib()->decrypt($this->getAuth(),$TGS_session_key)));

				//$AUTH_decrypted=$this->getEcryptionLib()->decrypt($this->getAuth(),$TGS_session_key);
				error_log("tgt_decrypted");error_log(print_r($TGT_decrypted,1));
				error_log("auth_decrypted");error_log(print_r($AUTH_decrypted,1));
				error_log("auth user_id");error_log($AUTH_decrypted["user_id"]);
				if($TGT_decrypted['user_id']==$AUTH_decrypted['user_id']){
					error_log("tgt_decrypted");error_log(print_r($TGT_decrypted,1));

					$time_difference=2*60;//2 minutes
					error_log("DIFFerence:".$AUTH_decrypted['timestamp'].'-'.$TGT_decrypted['timestamp']);
					if(((int)$AUTH_decrypted['timestamp']-(int)$TGT_decrypted['timestamp'])<$time_difference){
						error_log("tgt timestamp".$TGT_decrypted['timestamp']."-requested lifetime".$TGT_decrypted['requested_lifetime'].'time'.time());
						if(((int)$TGT_decrypted['timestamp']+(int)$TGT_decrypted['requested_lifetime'])>time()){

							if($TGT_decrypted['ip']==Yii::app()->request->getUserHostAddress()){

								$HTTP_service_session_key=hash('sha256',rand()+time());
								$HTTP_service_secret_key=$this->getHTTPServiceSecretKey($this->getRequestedHttpService());
								$timestamp=time();
								$HTTP_session_ticket=$this->getEcryptionLib()->encrypt(CJSON::encode(array('requested_http_service'=>$this->getRequestedHttpService(),'timstamp'=>$timestamp,'requested_lifetime'=>$TGT_decrypted['requested_lifetime'],'HTTP_service_session_key'=>$HTTP_service_session_key)),$TGS_session_key);
								$HTTP_service_ticket=$this->getEcryptionLib()->encrypt(CJSON::encode(array('user_id'=>$TGT_decrypted['user_id'],'requested_http_service'=>$this->getRequestedHttpService(),'ip'=>Yii::app()->request->getUserHostAddress(),'timestamp'=>$timestamp,'requested_lifetime'=>$TGT_decrypted['requested_lifetime'],'HTTP_service_session_key'=>$HTTP_service_session_key)),$HTTP_service_secret_key);
								REST::sendResponse(200,CJSON::encode(array('HTTP_session_ticket'=>$HTTP_session_ticket,'HTTP_service_ticket'=>$HTTP_service_ticket)));

							}
							else{
								REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'access to server from different ip prevented!')));	
							}
						}
						else{
							REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'dead ticket!')));
						}
					}
					else{
						REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'ticket timeout!')));

					}



				}
				else{
					REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'user_id injection attack prevented!','auth'=>$AUTH_decrypted,'tgt'=>$TGT_decrypted)));
				}
			
			}
			else{
					REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'replay attack prevented!')));
			}
		}
		else
		{
			REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'Http service is not available!')));
		}


	}


	/*internal apis*/
	private function is_replay_attack(){
		$ticket=$this->getAuth();
		if(Tickettrash::model()->exists('ticket=:ticket',array('ticket'=>$ticket))){
			return True;
		}
		$tickettrash=new Tickettrash();
		$tickettrash->ticket=$ticket;
		$tickettrash->timestamp=date("Y-m-d");
		if($tickettrash->save()){
			return False;
		}
		return True;

	}
	private function getHTTPServiceSecretKey($requested_http_service){

		$httpservice=Httpservice::model()->find('http_service_name=:http_service_name',array('http_service_name'=>$requested_http_service));
		return $httpservice->https_service_secret_key;
	}
	/*
	private function encrypt($data,$key){
		return openssl_encrypt ($data,'aes-256-cfb', $key,$options=0,$iv=substr($key,0,16));
		//return openssl_encrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}
	private function decrypt($data,$key){
		//$key="00000000000000000000000000000000";
		return openssl_decrypt($data,'aes-256-cfb', $key,$options=0,$iv=substr($key,0,16));   
		//return openssl_decrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}*/
	private function is_http_service_available(){
		return Httpservice::model()->exists('http_service_name=:http_service_name',array('http_service_name'=>$this->getRequestedHttpService()));
	}
	private function getTGSSecretKey(){
		$tgs=Tgs::model()->find('tgs_name=:tgs_name',array('tgs_name'=>'kerbela'));
		return $tgs->tgs_secret_key;
	}
	/*Setters and getters*/
	public function setRequestedHttpService($requested_http_service){
		$this->requested_http_service=$requested_http_service;
	}
	public function getRequestedHttpService(){
		return $this->requested_http_service;
	}
	public function setRequestedLifetime($requested_lifetime){
		$this->requested_lifetime=$requested_lifetime;
	}
	public function getRequestedLifetime(){
		return $this->requested_lifetime;
	}
	public function setAuth($auth){
		$this->auth=$auth;
	}
	public function getAuth(){
		return $this->auth;
	}
	public function setTgt($tgt){
		$this->tgt=$tgt;
	}
	public function getTgt(){
		return $this->tgt;
	}
	public function setEncryptionLib($encryptionLib){
		$this->encryptionLib=$encryptionLib;
	}
	public function getEcryptionLib(){
		return $this->encryptionLib;
	}


}
?>