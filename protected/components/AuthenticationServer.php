<?php
class AuthenticationServer{
	private $userId;
	private $requestedService;
	private $ip;
	private $requestedLifetime;

	private $encryptionLib;
	public function __construct($userId,$requestedService,$requestedLifetime,$ip="",$encryptionLib){
		$this->setSuper($userId,$requestedService,$requestedLifetime,$ip,$encryptionLib);
	}

	private function setSuper($userId,$requestedService,$requestedLifetime,$ip,$encryptionLib){
		$this->setUserId($userId);
		$this->setRequestedService($requestedService);
		$this->setIp($ip);
		$this->setRequestedLifetime($requestedLifetime);
		$this->setEncryptionLib($encryptionLib);
	} 
	public function authenticate(){
		if($this->is_user_available() && $this->is_ip_valid()){

			$TGS_session_key=hash('sha256',rand()+time());
			$TGS_secret_key=$this->getTGSSecretKey();
			$TGS_client_key=$this->getClientKey();


			$timestamp=time();
			$TGT=$this->getEncryptionLib()->encrypt(json_encode(array('user_id'=>$this->getUserId(),'requested_service'=>$this->getRequestedService(),'timestamp'=>$timestamp,'ip'=>$this->getIp(),'requested_lifetime'=>$this->getRequestedLifetime(),'TGS_session_key'=>$TGS_session_key)),$TGS_secret_key);
			$TGT_client=$this->getEncryptionLib()->encrypt(json_encode(array('requested_service'=>$this->getRequestedService(),'timestamp'=>$timestamp,'requested_lifetime'=>$this->getRequestedLifetime(),'TGS_session_key'=>$TGS_session_key)),$TGS_client_key);
			error_log('as sent tgt:');error_log(print_r($TGT,1));
			error_log(print_r($TGT_client,1));
			REST::sendResponse(200,json_encode(array('TGT'=>$TGT,'TGT_client'=>$TGT_client)));
			


		}
		else
		{
			REST::sendResponse(200,CJSON::encode(array('status'=>False,'message'=>'Authentication failed!')));
		}
	}
	/*internal apis*/

	private function is_user_available(){
		return User::model()->exists('user_id=:user_id',array('user_id'=>$this->getUserId()));
		
	}
	private function getTGSSecretKey(){
		$tgs=Tgs::model()->find('tgs_name=:tgs_name',array('tgs_name'=>$this->getRequestedService()));
		return $tgs->tgs_secret_key;
	}
	private function is_ip_valid(){
		return $this->getIp()==Yii::app()->request->getUserHostAddress();
	}
	private function getClientKey(){
		$user=User::model()->find('user_id=:user_id',array('user_id'=>$this->getUserId()));
		return $user->password;
	}
	private function setEncryptionLib($encryptionLib){
		$this->encryptionLib=$encryptionLib;
	}
	private function getEncryptionLib(){
		return $this->encryptionLib;
	}	
	/*
	private function encrypt($data,$key){
		//return openssl_encrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));
		//return openssl_encrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
		

		$output=array();
		//exec('echo "'.$data.'" | openssl enc -e -aes-256-ecb -a -k '.$key="0000000000000000",$output);
		//$data="mylovelysalthere";
		exec('echo "'.$data.'" | openssl enc -e -aes-256-cbc -a -k '.$key="ThisIsMyPassword",$output);
		error_log("my array:".base64_encode($output[0]));
		//error_log(print_r($output,1));
		return $this->merge($output);
		//return openssl_encrypt ($data,'aes-256-ecb', $key,$options=2);
	}*/
	private function merge(Array $output){
		$result="";
		error_log("merge:");
		error_log(print_r($output,1));
		foreach($output as $item){
			$result.=str_replace('\n','',$item);
		}
		return $result;
	}
	/*
	private function decrypt($data,$key){
		//return openssl_decrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));   
		//return openssl_decrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
		$output=array();
		exec('echo "'.$data.'" | openssl enc -d -aes-256-cfb -k '.$key,$output);
		return $output[0];
	}*/
	/*setter and getters*/
	public function setUserId($userId){
		$this->userId=$userId;
	}
	
	public function getUserId(){
		return $this->userId;
	}

	public function setRequestedService($requestedService){
		$this->requestedService=$requestedService;
	}

	public function getRequestedService(){
		return $this->requestedService;
	}

	public function setIp($ip){
		$this->ip=$ip;
	}

	public function getIp(){
		if($this->ip==""){

			$this->setIp(Yii::app()->request->getUserHostAddress());
		}
		return $this->ip;
	}

	public function setRequestedLifetime($requestedLifetime){
		$this->requestedLifetime=$requestedLifetime;
	}

	public function getRequestedLifetime(){
		return $this->requestedLifetime;
	}
}

?>