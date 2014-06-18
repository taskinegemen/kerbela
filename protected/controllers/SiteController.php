<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	public function actionChangePasswordMobile(){
		$res=array();
		if (isset($_POST['email'])) {
			$email= $_POST['email'];
			$password= $_POST['password'];
			$newpassword= $_POST['newpassword'];

			$user=User::model()->find('user_id=:user_id',array('user_id'=>$_POST['email']));
			if ($user) {
				if ($password==$user->password) {
					$user->password=$newpassword;
					$user->save();

					$message="Şifre Şifreniz başarıyla değiştirilmiş";
					$mail=Yii::app()->Smtpmail;
			        $mail->SetFrom(Yii::app()->params['noreplyEmail'], "OKUTUS Reader");
			        $mail->Subject= "Password Reset";
			        $mail->MsgHTML($message);
			        $mail->AddAddress($email, "");
			        $meta->created=time();
			        if($mail->Send())
			        {
						$res['result']=1;
						$res['message']='Şifre başarıyla değiştirildi.';
			        }
			        else
			        {
						$res['result']=0;
						$res['message']='Mail gönderilemedi! Tekrar Deneyin.';
			        }
				}else{
					$res['result']=0;
					$res['message']='Şifrenizi yanlış girdiniz!';	
				}
			}
			else
			{
				$res['result']=0;
				$res['message']='Girilen email adresine ait kullanıcı bulunamadı.';
			}
		}
		echo json_encode($res);
	}

	public function actionSignUp()
	{
		$res=array();
		$isUser=User::model()->findAll('user_id=:user_id',array('user_id'=>$_POST['email']));
		//$isUser=Yii::app()->db->createCommand('SELECT * FROM user where user_id="'.$_POST['email'].'"')->queryRow();
		//print_r($isUser);
		if (empty($isUser)) {
			if ($_POST['password']==$_POST['passwordR']) {
				$user=new User;
				$user->password=hash('sha256',$_POST['password']);
				$user->user_id=$_POST['email'];
				$user->name=$_POST['name'];
				$user->surname=$_POST['surname'];
				// $user->birth_date=$_POST['birthdate'];
				// $user->gender=$_POST['gender'];
				// $user->tel=$_POST['tel'];
				// $user->city=$_POST['city'];
				if ($user->save()) {
					$res['result']=1;
					$res['message']='Başarılı';
					$res['user_id']=$user->user_id;
					$res['password']=$_POST['password'];
				}
				else
				{
					$res['result']=0;
					$res['message']='Beklenmeyen bir hatayla karşılaşıldı!';
				}
			}
			else
			{
				$res['result']=0;
				$res['message']='Şifre doğrulama hatalı!';
			}
		}else
		{
			$res['result']=0;
			$res['message']='Bu email adresine sahip bir kullanıcı zaten var.';
		}
		echo json_encode($res);
	}

	

	public function actionUpdatePassword()
	{
		if (isset($_POST['code'])&& isset($_POST['password'])) {
			$user=User::model()->find('recoveryCode=:recoveryCode',array('recoveryCode'=>$_POST['code']));
			$user->password=hash('sha256',$_POST['password']);
			$user->recoveryCode="";
			if ($user->save()) {
				echo 1;
			}
			else
			{
				echo 0;
			}
		}
	}

	public function actionCheckVerifyCode()
	{
		if (isset($_POST['id'])) {
			$user=User::model()->find('recoveryCode=:recoveryCode',array('recoveryCode'=>$_POST['id']));
			if ($user) {
				echo 1;
			}
			else
			{
				echo 0;
			}
		}

	}

	public function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}

	public function actionResetPassword()
	{
		$res=array();
		if (isset($_POST['email'])) {
			$email= $_POST['email'];
			$user=User::model()->find('user_id=:user_id',array('user_id'=>$_POST['email']));
			if ($user) {
				$id=$this->generateRandomString();
				//$id=base64_encode($user->user_id).uniqid();
				$user->recoveryCode=$id;
				$user->save();
				$link="";
				$link=$_POST['reader_host'];
				$link.='/site/forgetPassword/';
				$link .= $id;
				$message="Şifre sıfırlama isteği gönderdiniz. <a href='".$link."'>Buraya tıklayarak</a> şifrenizi değiştirebilirsiniz.<br>".$link;
				$mail=Yii::app()->Smtpmail;
		        $mail->SetFrom(Yii::app()->params['noreplyEmail'], "OKUTUS Reader");
		        $mail->Subject= "Password Reset";
		        $mail->MsgHTML($message);
		        $mail->AddAddress($email, "");
		        error_log("sifre sıfırlama id:".$id);
		        if($mail->Send())
		        {
		        	error_log("mail.sent");
					$res['result']=1;
					$res['message']='Şifre sıfırlama bilgileri mail adresinize gönderildi.';
		        }
		        else
		        {
		        	error_log("mail.couldNOT.sent");
					$res['result']=0;
					$res['message']='Mail gönderilemedi! Tekrar Deneyin.';
		        }
			}
			else
			{
				$res['result']=0;
				$res['message']='Girilen email adresine ait kullanıcı bulunamadı.';
			}
		}
		echo json_encode($res);
	}

	public function RandomString()
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randstring = '';
	    for ($i = 0; $i < 8; $i++) {
	        $randstring = $characters[rand(0, strlen($characters))];
	    }
	    return $randstring;
	}

	public function actionResetPasswordMobile()
	{
		$res=array();
		if (isset($_POST['email'])) {
			$email= $_POST['email'];
			$user=User::model()->find('user_id=:user_id',array('user_id'=>$_POST['email']));
			if ($user) {
				$id=base64_encode($user->user_id).uniqid();

				$newPass=$this->RandomString();

				$user->password=sha256($newPass);


				//$user->recoveryCode=$id;
				$user->save();
				$message="Yeni şifreniz oluşturuldu.<br>Şifreniz: ".$newPass."<br>kullanıcı adınız: ".$email;
				$mail=Yii::app()->Smtpmail;
		        $mail->SetFrom(Yii::app()->params['noreplyEmail'], "OKUTUS Reader");
		        $mail->Subject= "Password Reset";
		        $mail->MsgHTML($message);
		        $mail->AddAddress($email, "");
		        $meta->created=time();
		        if($mail->Send())
		        {
					$res['result']=1;
					$res['message']='Şifre sıfırlama bilgileri mail adresinize gönderildi.';
		        }
		        else
		        {
					$res['result']=0;
					$res['message']='Mail gönderilemedi! Tekrar Deneyin.';
		        }
			}
			else
			{
				$res['result']=0;
				$res['message']='Girilen email adresine ait kullanıcı bulunamadı.';
			}
		}
		echo json_encode($res);
	}


	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}