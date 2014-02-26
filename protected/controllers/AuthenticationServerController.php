<?php

class AuthenticationServerController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	public function actionAuthenticate(){
		$user_id=Yii::app()->request->getPost('user_id',0);
		$requested_service=Yii::app()->request->getPost('requested_service',0);
		$ip=Yii::app()->request->getPost('ip',0);
		$requested_lifetime=Yii::app()->request->getPost('requested_lifetime',0);
		$type=Yii::app()->request->getPost('type','android');

		$auth=new AuthenticationServer($user_id,$requested_service,$requested_lifetime,$ip,KerbelaEncryptionFactory::create($type));
		$auth->authenticate();		
	}
	public function actionGetIp(){
		REST::sendResponse(200,CJSON::encode(array('ip'=>Yii::app()->request->getUserHostAddress())));
	}

}