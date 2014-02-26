<?php

class CryptoController extends Controller
{
	public function actionAuthenticate()
	{
		$TGT='';
		$TGT_client=$this->encrypt("selam dostum",$TGS_client_key="password");
		REST::sendResponse(200,CJSON::encode(array('TGT'=>base64_encode($TGT),'TGT_client'=>base64_encode($TGT_client))));

	}
	public function actionEncrypt(){
		$data="egementaskinegementaskinegementaskinegementaskinegementaskinegementaskinegementaskinegementaskin{},:\"";
		$key="00000000000000000000000000000000";

		$deneme=openssl_encrypt($data,'aes-256-cfb', $key,$options=0,$iv=substr($key,0,16));
		print_r($deneme);
		print_r("<br>");
		$deneme2=openssl_decrypt($deneme,'aes-256-cfb', $key,$options=0,$iv=substr($key,0,16));
		print_r($deneme2);
		//print_r(base64_encode($data));
		print(base64_encode($deneme));

		/*
		$fields = array(
						'user_id' => 'egemen',
						'timestamp'=>'taskin'
				);
		$step2=base64_encode(CJSON::encode($fields));
		print_r($step2);*/

	}
	public function actionDecrypt(){
		$deneme=openssl_decrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));
		echo base64_encode($deneme);
	}
	private function decrypt($data,$key){
		$output=array();
		exec('echo "'.$data.'" | openssl enc -d -aes-256-cbc -k '.$key,$output);
		return $output[0];
		//return openssl_decrypt ($data,'aes-256-cfb', $key,$options=2,$iv=substr($key,0,16));   
		//return openssl_decrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}
	private function encrypt($data,$key){
		//return openssl_encrypt($data,'aes-256-cbc -a', $key,$options=2);
		$output=array();
		exec('echo "'.$data.'" | openssl enc -e -aes-256-cbc -k '.$key,$output);
		return $output[0];
		//return openssl_encrypt ($data,'aes256' , $key,true,$iv=substr($key,0,16));
	}

}