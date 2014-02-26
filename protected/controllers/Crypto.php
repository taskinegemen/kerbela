<?php

class CryptoController extends Controller
{
	public function actionAuthenticate()
	{
		$TGT='';
		$TGT_client=$this->encrypt('gizli',$TGS_client_key="000000000000000000000000000000000000000000000000");
		REST::sendResponse(200,CJSON::encode(array('TGT'=>base64_encode($TGT),'TGT_client'=>base64_encode($TGT_client))));

	}
	private function decrypt($data,$key){
		return openssl_decrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));   
		//return openssl_decrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}
	private function encrypt($data,$key){
		return openssl_encrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));
		//return openssl_encrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}

}