<?php

class HttpServiceController extends Controller
{
	public function init()
    {
        $this->layout = false;
    }
	public function actionIndex()
	{
		$this->render('index',array('client_ip',CHttpRequest::getUserHostAddress()));
	}
	public function actionService(){
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$type=Yii::app()->request->getPost('type','android');
		$kerberized=new KerberizedServer($auth,$http_service_ticket,KerbelaEncryptionFactory::create($type));
		$myarray=$kerberized->ticketValidation();

		error_log("ticket validation:".print_r($myarray,1));
		error_log("user id".$kerberized->getUserId());


		$kerberized->authenticate();			
	}
	public function actionAuthenticate(){


		
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$type=Yii::app()->request->getPost('type','android');
		$kerberized=new KerberizedServer($auth,$http_service_ticket,KerbelaEncryptionFactory::create($type));
		

		//$myarray=$kerberized->ticketValidation();
		//error_log("user_id:".$kerberized->getUserId());
		$kerberized->authenticate();
			
		}
	private function is_http_service_correct($requested_http_service){
		return $requested_http_service=='koala';
	}
	private function is_replay_attack($AUTH){
		return false;
	}
	private function getHTTPServiceSecretKey(){

		$httpservice=Httpservice::model()->find('http_service_name=:http_service_name',array('http_service_name'=>'koala'));
		return $httpservice->https_service_secret_key;
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