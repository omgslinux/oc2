<?php
/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2013 OCAX Contributors. See AUTHORS.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

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
		// run automated backups
		VaultSchedule::model()->runVaultSchedule();

		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		if(isset($_GET['lang']) && strlen($_GET['lang']) == 2){
			$this->changeLanguage($_GET['lang']);
			$this->redirect(array('index'));
		}else
			$this->render('index', array('lang'=>Yii::app()->user->getState('applicationLanguage')));
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

	public function actionChat()
	{
		if(!Yii::app()->user->isPrivileged())
			$this->redirect(array('index'));
		else
			$this->renderPartial('chat');
	}

	public function actionCandy()
	{
		if(!Yii::app()->user->isPrivileged())
			$this->redirect(array('index'));
		else
			$this->renderPartial('candy');
	}

	private function getActivationEmailText($user)
	{
		return '<p>'.__('Please click the link below to activate your account').'.<br />'.
		'<a href="'.Yii::app()->createAbsoluteUrl('site/activate', array('c' => $user->activationcode)).'">'.
		Yii::app()->createAbsoluteUrl('site/activate', array('c' => $user->activationcode)).'</a></p>';
	}

	public function actionSendActivationCode()
	{
		if(Yii::app()->user->isGuest) // add accessRules() to $this controller instead?
			Yii::app()->end();

		$user=User::model()->findByAttributes(array('username'=>Yii::app()->user->id));
		if ($user===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		if($user->is_disabled)
			Yii::app()->end();

		$user->activationcode = $user->generateActivationCode();
		$user->save();

 		$mailer = new Mailer();
		$mailer->AddAddress($user->email);
		$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);

		$mailer->Subject = __('Activate your account');
		$mailer->Body = '<p>'.__('Hello').' '.$user->fullname.',</p>'.
						$this->getActivationEmailText($user).
						'<p>'.__('Thank you').',<br />'.
						Config::model()->getObservatoryName().'</p>';
		if($mailer->send())
			Yii::app()->user->setFlash('success',__('We sent you an email'));
		else
			Yii::app()->user->setFlash('newActivationCodeError',__('Error while sending email').'<br />"'.$mailer->ErrorInfo.'"');

		$this->redirect(array('/user/panel'));
	}

	public function actionSendWelcomeText()
	{
		if(Yii::app()->user->isGuest) // add accessRules() to $this controller instead?
			Yii::app()->end();

		$user=User::model()->findByAttributes(array('username'=>Yii::app()->user->id));
		if ($user===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		$mailer = new Mailer();
		$mailer->AddAddress($user->email);
		$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);
		$mailer->Subject = __('Welcome to the').' '.Config::model()->getObservatoryName();

		$mailer->Body = '<p>'.__('Welcome to the').' '.Config::model()->getObservatoryName().',</p>'.
						'<p>'.__('NEW_USER_EMAIL_MSG').'</p>'.
						'<p>'.$this->getActivationEmailText($user).'</p>'.
						'<p>'.__('Thank you').',<br />'.Config::model()->getObservatoryName().'</p>';

		if($mailer->send())
			Yii::app()->user->setFlash('success',__('We sent you an email'));
		else
			Yii::app()->user->setFlash('newActivationCodeError',__('Error while sending email').'<br />'.$mailer->ErrorInfo);

		$this->redirect(array('/user/panel'));
	}


	public function actionRegister()
	{
		if(!Yii::app()->user->isGuest)
			$this->redirect(array('/user/panel'));

		$model=new RegisterForm;
		$newUser = new User;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

 		$model->is_socio = 0;	// maybe admin doesn't encourage membership. 0 = default
		if(isset($_POST['RegisterForm']))
		{
			$model->attributes=$_POST['RegisterForm'];

			$newUser->username = $model->username;
			$newUser->fullname = $model->fullname;
			$newSalt=$newUser->generateSalt();
 			$newUser->password = $newUser->hashPassword($model->password,$newSalt);
			$newUser->salt = $newSalt;
 			$newUser->generateActivationCode();
			$newUser->is_active = 0;
			$newUser->is_disabled = 0;
 			$newUser->username = $model->username;
 			$newUser->email = $model->email;
 			$newUser->is_socio = $model->is_socio;
			$newUser->joined = date('Y-m-d');

			if ($model->validate() && $newUser->save())
			{
				Log::model()->write('User',__('New user').' "'.$newUser->username.'" id='.$newUser->id, $newUser->id);
				//if want to go login, just uncomment this below
				$identity=new UserIdentity($newUser->username,$model->password);
				//$identity->authenticate();
				Yii::app()->user->login($identity,0);
				$this->actionSendWelcomeText();
			}
		}
		$this->render('register',array('model'=>$model));
	}

	/**
	 * Activation Action
	*/
	public function actionActivate()
	{
		$code = Yii::app()->request->getQuery('c');
		if($code)
		{
			$model = User::model()->findByAttributes(array('activationcode'=>$code));
			if($model && !$model->is_active && !$model->is_disabled){
				$model->is_active=1;
				if($model->save()){
					//Log::model()->write('User',__('User account activated'),$model->id);
					Yii::app()->user->setFlash('success',__('Your account is active'));
				}
			}elseif(!Yii::app()->user->isGuest)
				$this->redirect(array('/user/panel'));
			else
				$this->redirect(array('/site/index'));
		}
		if(!Yii::app()->user->isGuest)
			$this->redirect(array('/user/panel'));
		else
			$this->redirect(array('login'));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if(!Yii::app()->user->isGuest)
			$this->redirect(array('/user/panel'));

		$model=new LoginForm;
        if (Yii::app()->user->getState('attempts-login') > 3) { //make the captcha required if the unsuccessful attemps are more of thee
            $model->scenario = 'withCaptcha';
        }
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
			if($model->validate() && $model->login()){
				Yii::app()->user->setState('attempts-login', 0); //if login is successful, reset the attemps
				$lang = User::model()->findByPk(Yii::app()->user->getUserID())->language;
				if($lang != Null){
					$cookie = new CHttpCookie('lang', $lang);
					$cookie->expire = time()+60*60*24*180;
					Yii::app()->request->cookies['lang'] = $cookie;
				}
				if(Yii::app()->user->returnUrl != Yii::app()->getHomeUrl())
					Yii::app()->request->redirect(Yii::app()->user->returnUrl);
				else
					$this->redirect(array('user/panel'));
			}else{
				//if login is not successful, increase the attemps 
                Yii::app()->user->setState('attempts-login', Yii::app()->user->getState('attempts-login', 0) + 1);
                if (Yii::app()->user->getState('attempts-login') > 3) { 
                    $model->scenario = 'withCaptcha'; //useful only for view
                }
			}
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	private function changeLanguage($lang)
	{
		//$available_langs = Yii::app()->coreMessages->basePath;
		$available_langs = Yiibase::getPathOfAlias('application.messages');
		if(is_dir($available_langs.'/'.$lang)){
			$cookie = new CHttpCookie('lang', $lang);
			$cookie->expire = time()+60*60*24*180;
			Yii::app()->request->cookies['lang'] = $cookie;
		}
	}

	public function actionLanguage()
	{
		if(isset($_GET['lang']) && strlen($_GET['lang']) == 2){
			$this->changeLanguage($_GET['lang']);
		}
		$reffer = Yii::app()->request->getUrlReferrer();
		if(!$reffer)
			$this->redirect(array('/site/index'));
		Yii::app()->request->redirect($reffer);
	}

	public function actionRequestNewPassword()
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();
		if(isset($_POST['email'])){
			$email = htmLawed::hl(trim($_POST['email']), array('elements'=>'-*', 'keep_bad'=>0));
			if(filter_var($email, FILTER_VALIDATE_EMAIL) && $user = User::model()->findByAttributes(array('email'=>$email))){
				if($user->is_disabled){
					echo '<span style="color:red">'.__('Invalid email address').'.</span>';
					Yii::app()->end();
				}
				$reset = new ResetPassword;
				$reset->user=$user->id;
				$reset->created = date('c');
				$reset->createCode();
				$reset->used=0;

				$link=Yii::app()->createAbsoluteUrl('site/resetPassword',array('reset'=>$reset->code));
				$link='<a href="'.$link.'">'.$link.'</a>';

 				$mailer = new Mailer();
				$mailer->AddAddress($user->email);
				$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);
				$mailer->Subject=__('New password request');

				$mailer->Body='<p><p>'.__('Hello').' '.$user->fullname.',</p>';
				$mailer->Body=$mailer->Body.'<p>'.Config::model()->findByPk('siglas')->value.' '.__('has received a request to reset your password').'.<br />';
				$mailer->Body=$mailer->Body.__('If you have not forgotten your password, please ignore this email').'.</p>';
				$mailer->Body=$mailer->Body.'<p>'.__('To reset your password, follow this link').'<br />'.$link.'</p>';
				$mailer->Body=$mailer->Body.'<p>'.__('Kind regards').',<br />'.Config::model()->getObservatoryName().'</p></p>';

				if($mailer->send()){
					$reset->save();
					echo '<span style="color:green">'.__('We\'ve just sent you an email').'.</span>';
				}else
					echo '<span style="color:red">'.$mailer->ErrorInfo.'</span>';
			}else
				echo '<span style="color:red">'.__('Sorry, we cannot find you on our database').'.</span>';
		}else
			echo '<span style="color:red">Email missing.</span>';
	}

	public function actionResetPassword()
	{
		if(isset($_GET['reset'])){
			$code=htmLawed::hl(trim($_GET['reset']), array('elements'=>'-*', 'keep_bad'=>0));
			if($reset = ResetPassword::model()->findByAttributes(array('code'=>$code,'used'=>0))){
				$reset->used=1;
				$reset->save();
				$created = DateTime::createFromFormat('Y-m-d H:i:s', $reset->created);
				$dDiff = $created->diff(new DateTime('now'));

				if($dDiff->h > 2){
					Yii::app()->user->setFlash('error', __('The password recovery link has expired'));
					$this->redirect(array('/site/login'));
				}
				$user=User::model()->findByPk($reset->user);
				if ($user===null){
					throw new CHttpException(404,'The requested User does not exist.');
				}
				if($user->is_disabled)
					$this->redirect(array('/site/index'));

				Yii::app()->user->login(UserIdentity::createAuthenticatedIdentity($user->username),0);
				Yii::app()->user->setFlash('success', __('Please change your password now'));
				$this->redirect(array('/user/update'));
			}
		}
		$this->redirect(array('/site/index'));
	}


	public function actionFeed()
	{
		Yii::import('application.vendors.*');
		require_once 'Zend/Loader/Autoloader.php';
		spl_autoload_unregister(array('YiiBase','autoload'));
		spl_autoload_register(array('Zend_Loader_Autoloader','autoload'));
		spl_autoload_register(array('YiiBase','autoload'));

		$entries=array();

		$enquiries = Enquiry::model()->getEnquiriesForRSS();
		// convert to the format needed by Zend_Feed
		foreach($enquiries as $enquiry)
		{
			$date = new DateTime($enquiry->created);
			$entries[]=array(
				'title'=>$enquiry->title,
				'link'=>Yii::app()->createAbsoluteUrl('enquiry/view',array('id'=>$enquiry->id)),
				'description'=>$enquiry->body,
				'lastUpdate'=>$date->getTimestamp(),
			);
		}
		$newsletters = Newsletter::model()->getNewslettersForRSS();
		foreach($newsletters as $newsletter)
		{
			$date = new DateTime($newsletter->published);
			$entries[]=array(
				'title'=>$newsletter->subject,
				'link'=>Yii::app()->createAbsoluteUrl('newsletter/view',array('id'=>$newsletter->id)),
				'description'=>$newsletter->body,
				'lastUpdate'=>$date->getTimestamp(),
			);
		}
		usort($entries, function($a, $b) {
    		return $b['lastUpdate'] - $a['lastUpdate'];
		});
		// generate and render RSS feed
		$feed=Zend_Feed::importArray(array(
			'title'			=> Config::model()->findByPk('siglas')->value,
			'description'	=> Config::model()-> getObservatoryName(),
			'link'			=> Yii::app()->createUrl('site'),
			'image'			=> Yii::app()->createAbsoluteUrl('files/logo.png'),
			'charset'		=> 'UTF-8',
			'entries'		=> $entries,
			),
			'rss'
		);
		$feed->send();
	}

	/*
	* The user accepts cookies
	*/
	public function actionAcceptCookies()
	{
		$cookie = new CHttpCookie('cookiesAccepted', 1);
		$cookie->expire = time() + (10 * 365 * 24 * 60 * 60);
		Yii::app()->request->cookies['cookiesAccepted'] = $cookie;
		echo 1;
		Yii::app()->end();
	}

	/**
	 * Displays the local map
	 */
	public function actionMap()
	{
		$this->renderPartial('map');
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
