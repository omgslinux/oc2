<?php
/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2014 OCAX Contributors. See AUTHORS.

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

class UserController extends Controller
{

	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete, disable, enable, optout', // we only allow deletion via POST request
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('panel','update','block','optout'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('view','admin','delete','updateRoles','enable'),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('allow',
				'actions'=>array('disable'),
				'expression'=>"Yii::app()->user->isManager() || Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * User's panel.
	 */
	public function actionPanel()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('My page'));
		$user=User::model()->findByAttributes(array('username'=>Yii::app()->user->id));
		if ($user===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		$userid=Yii::app()->user->getUserID();
		$enquirys=new CActiveDataProvider('Enquiry', array(
			'criteria'=>array(
				'condition'=>"user=$userid",
			),
			'sort'=>array('defaultOrder'=>'modified DESC'),
		));

		$subscribed=new CActiveDataProvider('Enquiry',array(
			'criteria'=>array(
				'with'=>array('subscriptions'),
				'condition'=>'	subscriptions.enquiry = t.id AND
								subscriptions.user = '.$userid.' AND
								t.user != '.$userid.' AND
								( t.team_member != '.$userid.' || t.team_member IS NULL )',
				'together'=>true,
			),
			'sort'=>array('defaultOrder'=>'t.modified DESC'),
		));
		
		if(Yii::app()->user->isAdmin()){
			$config = Config::model();
			
			// update schema.
			$schema = new Schema;
			if(!$schema->isSchemaUptodate($config->getOCAxVersion())){
				$schema->migrate();
				$postInstallChecked = $config->findByPk('siteConfigStatusPostInstallChecked');
				$postInstallChecked->value = 0;
				$postInstallChecked->save();			
			}
			// check for OCAx updates once a week
			$latest_version_file = Yii::app()->basePath.'/runtime/latest.ocax.version';
			if (!file_exists($latest_version_file))
				$config->updateVersionInfo();
			else{
				$date = new DateTime();
				if( $date->getTimestamp() - filemtime($latest_version_file) > 86400 ) // 604800 a week
					$config->updateVersionInfo();
			}
			if($config->isOCAXUptodate())
				$config->updateSiteConfigurationStatus('siteConfigStatusUptodate', 1);
			else
				$config->updateSiteConfigurationStatus('siteConfigStatusUptodate', 0);
		}
		$this->render('panel',array(
				'model'=>$this->loadModel($user->id),
				'enquirys'=>$enquirys,
				'subscribed'=>$subscribed,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{

		$enquirys=new CActiveDataProvider('Enquiry', array(
			'criteria'=>array(
				'condition'=>"user=$id",
				'order'=>'created ASC',
			),
			'pagination'=>array(
				'pageSize'=>20,
			),
		));

		$this->layout='//layouts/column2';
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'enquirys'=>$enquirys,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
		$model=User::model()->findByAttributes(array('username'=>Yii::app()->user->id));
		if ($model===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		$email = $model->email;
		$language = $model->language;

		if(isset($_POST['User']))
		{
			$model->scenario = 'update_user';
			$model->attributes=$_POST['User'];

			if($_POST['User']['new_password'] || $_POST['User']['password_repeat']){

				$model->new_password=$_POST['User']['new_password'];
				$model->password_repeat=$_POST['User']['password_repeat'];
				$model->scenario = 'change_password';

				if(!$model->validate()){
					$this->render('update',array('model'=>$model,));
					Yii:app()->end();
				}
				$model->salt=$model->generateSalt();
				$model->password = $model->hashPassword($model->new_password,$model->salt);
			}
			if ($email != $model->email){
				$model->is_active=0;
			}
			if($model->save()){
				Yii::app()->language = $model->language;
				$cookie = new CHttpCookie('lang', $model->language);
				$cookie->expire = time()+60*60*24*180;
				Yii::app()->request->cookies['lang'] = $cookie;

				Yii::app()->user->setFlash('success', __('Changes saved Ok'));
				if(!$model->is_active)
					$this->redirect(array('/site/sendActivationCode'));
				else
					$this->redirect(array('panel'));
			}
		}
		$this->render('update',array(
			'model'=>$model,
		));
	}
/*
	public function actionBlock($id)
	{
		$blocked_user=User::model()->findByAttributes(array('username'=>$id));
		if(!$blocked_user)
			$this->redirect(array('panel'));
		if($blocked_user->username != Yii::app()->user->id){
			$userid=Yii::app()->user->getUserID();
			if(isset($_GET['confirmed'])){
				$block = new BlockUser;
				if(! $block->findByAttributes(array('user'=>$userid, 'blocked_user'=>$blocked_user->id))){

					$block->user=$userid;
					$block->blocked_user=$blocked_user->id;
					$block->save();
				}
				Yii::app()->user->setFlash('success', $blocked_user->fullname.' '.__('is blocked'));
			}else{
				if(! BlockUser::model()->findByAttributes(array('user'=>$userid, 'blocked_user'=>$blocked_user->id)))
					Yii::app()->user->setFlash('prompt_blockuser', $blocked_user->fullname.'|'.$id);
			}
		}
		$this->redirect(array('panel'));
	}
*/
	public function actionUpdateRoles($id)
	{
		$this->layout='//layouts/column2';
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->scenario = 'update_roles';
			$model->attributes=$_POST['User'];
			if($model->save()){
				Log::model()->write('User',__('User permissiones changed for').' "'.$model->username.'"',$model->id);
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('updateRoles',array(
			'model'=>$model,
		));
	}

	/**
	 * A disabled user cannot login
	 * ajax call from enquiry/manage
	 */
	public function actionDisable($id)
	{
		$model=$this->loadModel($id);
		$model->disableUser();
		if(Yii::app()->request->isAjaxRequest){
			Yii::app()->user->setFlash('success', __('User disabled'));
			echo 1;
		}else
			$this->redirect(array('view','id'=>$model->id));
	}

	public function actionEnable($id)
	{
		$model=$this->loadModel($id);
		$model->enableUser();
		$this->redirect(array('view','id'=>$model->id));
	}

	/*
	 * The user deletes his account
	 * We don't delete the database entry because it might be referenced by other models
	 */
	public function actionOptout()
	{
		if(Yii::app()->user->isGuest){
			$this->redirect(array('site/index'));
			Yii:app()->end();
		}
		$model=$this->loadModel(Yii::app()->user->getUserID());

		$username = $model->username;
		$model->smudgeUser();

		Yii::app()->user->logout();
		//Yii::app()->user->setFlash('success', __('Your profile has been deleted.'));
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model=$this->loadModel($id);
		$model->smudgeUser();
		Yii::app()->user->setFlash('success', __('User deleted'));
		$this->redirect(array('/user/admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
