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


class EmailController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete, test', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform
				'actions'=>array('contactPetition'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','create'/*,'update'*/),
				'expression'=>"(Yii::app()->user->isManager() || Yii::app()->user->isTeamMember())",	//not working? check this.
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('test'),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	private function getReturnURL($menu_type)
	{
		if($menu_type == 'team')
			return 'enquiry/teamView';
		return 'enquiry/admin';
	}

	/**
	 * Creates and sends an email.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		//if(!Yii::app()->request->isAjaxRequest)
		//	Yii::app()->end();

		$model=new Email;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Email']))
		{
			$returnURL=$_POST['Email']['returnURL'];

			$model->attributes=$_POST['Email'];
			$model->created = date('c');
			$model->sent=0;

			if ($model->sender == 0){
				$model->sent_as=Config::model()->findByPk('emailNoReply')->value;
			}else{
				$user = User::model()->findByPk($model->sender);
				if ($user===null){
					throw new CHttpException(404,'The requested User does not exist.');
				}
				$model->sent_as=$user->email;
			}
			$model->sender = Yii::app()->user->getUserID();
			if($model->save()){
 				$mailer = new Mailer();
 				
				$mailer->SetFrom($model->sent_as, Config::model()->findByPk('siglas')->value);
				$mailer->Subject=$model->title;
				$mailer->Body=$model->body;
				$addresses = explode(',', $model->recipients);
				
				$model->sent = $mailer->sendBatches($addresses);
				$model->save();
				
				if($model->sent){
					//$link=CHtml::link(__('View email'),array('email/index/'.$model->enquiry.'?menu=manager'));	// need to fix this!!
					Yii::app()->user->setFlash('success',__('Email sent OK'));
				}else
					Yii::app()->user->setFlash('error',__('Error while sending email').'<br />"'.$mailer->ErrorInfo.'"');

				$this->redirect(array($returnURL,'id'=>$model->enquiry));
			}

		}
		else{	//Get enquiry id
			if (isset($_GET['enquiry']) && !$model->enquiry){
				$model->enquiry=(int)$_GET['enquiry'];				
			}
			if (isset($_GET['menu']))
				$returnURL=$this->getReturnURL($_GET['menu']);
		}
		$model->sender=Yii::app()->user->getUserID();
		$enquiry=Enquiry::model()->findByPk($model->enquiry);
		if ($enquiry===null){
			throw new CHttpException(404,'The requested page does not exist.');
		}

		if(!$model->body)
			$model->body=EmailTemplate::model()->findByPk($enquiry->state)->getBody($enquiry);
		if(!$model->title)
			$model->title=$enquiry->getHumanStates($enquiry->state);

		$this->render('create',array(
			'model'=>$model,
			'returnURL'=>$returnURL,
			'enquiry'=>$enquiry,
		));
	}


	public function actionContactPetition($recipient_id=Null, $enquiry_id=Null)
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();

		$model=new Email;

		if(isset($_POST['Email']))
		{
			$model->attributes=$_POST['Email'];

			$recipient = User::model()->findByAttributes(array('email'=>$model->recipients));
			if ($recipient===null){
				throw new CHttpException(404,'The requested Recipient does not exist.');
			}
			if(BlockUser::model()->findByAttributes(array('user'=>$recipient->id, 'blocked_user'=>Yii::app()->user->getUserID()))){
					echo $recipient->fullname.' '.__('has blocked you');
					Yii::app()->end();
			}

			$model->created = date('c');
			$model->sent=0;
			$model->type=1;
			$model->sent_as = Config::model()->findByPk('emailNoReply')->value;
			$user_text = htmLawed::hl($model->body, array('elements'=>'-*', 'keep_bad'=>0));
			$user_text = nl2br($user_text);
			$model->body = '<p>'.$model->title.'</p>';	//get the preamble from the title

			$model->title = str_replace("%s", Config::model()->findByPk('siglas')->value, __('Private email request from a user at the %s'));

			if($model->save()){
 				$mailer = new Mailer();
				$mailer->SetFrom($model->sent_as, Config::model()->findByPk('siglas')->value);
				$mailer->AddAddress($model->recipients);
				$mailer->Subject=$model->title;
				$mailer->Body=$model->body.'<p><i>'.$user_text.'</i></p>';

				if($mailer->send()){
					$model->sent=1;
					$model->save();
					echo 1;
				}else{
					echo $mailer->ErrorInfo.' '.__('Email not sent');
				}
				Yii::app()->end();
			}

			echo 1;
			Yii::app()->end();
		}

		if(isset($_GET['recipient_id']) && isset($_GET['enquiry_id'])){			
			$enquiry = Enquiry::model()->findByPk((int)$_GET['enquiry_id']);
			if ($enquiry===null){
				throw new CHttpException(404,'The requested Enquiry does not exist.');
			}
			$model->enquiry=$enquiry->id;
			$recipient = User::model()->findByPk((int)$_GET['recipient_id']);
			if ($recipient===null){
				throw new CHttpException(404,'The requested page does not exist.');
			}			
			echo $this->renderPartial('_contactPetition', array('model'=>$model, 'recipient'=>$recipient, false,true));
		}else
			echo 0;
	}

	/**
	 * Admin can test email parameters.
	 */
	public function actionTest($id)
	{
		$user = User::model()->findByPk(Yii::app()->user->getUserID());
		if ($user===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		$mailer = new Mailer();
		$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);
		$mailer->AddAddress($user->email);
		$mailer->Subject=$id;
		$mailer->Body='<p>This is a test</p>';

		if($mailer->send()){
			Yii::app()->user->setFlash('success',__('Email sent OK'));
			Config::model()->updateSiteConfigurationStatus('siteConfigStatusEmail', 1);
		}else{
			Yii::app()->user->setFlash('error',__('Error while sending email').'<br />"'.$mailer->ErrorInfo.'"');
			Config::model()->updateSiteConfigurationStatus('siteConfigStatusEmail', 0);
		}
		echo 1;
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($id)
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Sent emails'));
		$enquiry=Enquiry::model()->findByPk((int)$id);
		if ($enquiry===null){
			throw new CHttpException(404,'The requested Enquiry does not exist.');
		}
		$criteria=new CDbCriteria;
		$criteria->addCondition('enquiry=:enquiry');
		$criteria->order = 'created DESC';
		$criteria->params[':enquiry'] = $id;
		$dataProvider=new CActiveDataProvider('Email', array('criteria'=>$criteria));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'enquiry'=>$enquiry,
			'menu'=>$_GET['menu'],
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Email the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Email::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Email $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='email-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
