<?php

class TicketGrantingServerController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	public function actionTicketgrant(){
		$requested_http_service=Yii::app()->request->getPost('requested_http_service',0);
		$requested_lifetime=Yii::app()->request->getPost('requested_lifetime',0);
		$auth=Yii::app()->request->getPost('auth',0);
		$tgt=Yii::app()->request->getPost('tgt',0);
		$type=Yii::app()->request->getPost('type','android');
		error_log('tgs received tgt:');error_log($tgt);
		error_log('tgs received aut:');error_log(str_replace('\n', '', $auth));
		$tgs=new TicketGrantingServer($requested_http_service,$requested_lifetime,$auth,$tgt,KerbelaEncryptionFactory::create($type));
		$tgs->ticketgrant();
	}

}