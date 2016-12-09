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

class NewsletterController extends Controller
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
			'postOnly + delete', // we only allow deletion via POST request
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','feed'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('adminView','create','send','update','admin','showRecipients','delete'),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	// http://www.yiiframework.com/wiki/20/how-to-generate-web-feed-for-an-application/
	public function actionFeed()
	{
		Yii::import('application.vendors.*');
		require_once 'Zend/Loader/Autoloader.php';
		spl_autoload_unregister(array('YiiBase','autoload')); 
		spl_autoload_register(array('Zend_Loader_Autoloader','autoload')); 
		spl_autoload_register(array('YiiBase','autoload'));

		$newsletters = Newsletter::model()->getNewslettersForRSS();
		// convert to the format needed by Zend_Feed
		$entries=array();
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
		// generate and render RSS feed
		$feed=Zend_Feed::importArray(array(
			'title'			=> Config::model()->findByPk('siglas')->value.' '.__('Newsletters'),
			'description'	=> Config::model()-> getObservatoryName(),
			'link'			=> Yii::app()->createUrl('newsletter'),
			'image'			=> Yii::app()->createAbsoluteUrl('files/logo.png'),
			'charset'		=> 'UTF-8',
			'entries'		=> $entries,      
			),
			'rss'
		);
		$feed->send();  
	}

	public function actionView($id)
	{
		$this->pageTitle = Config::model()->findByPk('siglas')->value.' '.__('Newsletter');
		$this->layout='//layouts/column1';
		$model=$this->loadModel($id);
		if($model->sent != 2)
			$this->redirect(array('site/index'));
		
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionAdminView($id)
	{
		$model=$this->loadModel($id);
		if($model->sent == 0){
	        $sql = "SELECT id FROM user WHERE is_active = 1";
	        $cnt = "SELECT COUNT(*) FROM ($sql) subq";
			$count = Yii::app()->db->createCommand($cnt)->queryScalar();
		}else{
			$count=count(explode(',',$model->recipients));
		}


		$this->render('adminView',array(
			'model'=>$model,
			'total_recipients'=>$count,
		));
	}

	public function actionShowRecipients($id=Null)
	{
		$model=Null;
		if($id)
			$model=$this->loadModel($id);
		if($model && $model->sent != 0)
			echo $this->renderPartial('showRecipients',array('recipients'=>$model->recipients,'draft'=>Null),false,true);
		else{
			$users = Yii::app()->db->createCommand()
					->select('email')
					->from('user')
					->where('is_active = 1')
					->queryAll();

			$result='';
			foreach($users as $recipient)
			    $result=$result.$recipient['email'].', ';
			echo $this->renderPartial('showRecipients',array('recipients'=>substr_replace($result ,"",-2),'draft'=>1),false,true);
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Newsletter;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Newsletter']))
		{
			$model->attributes=$_POST['Newsletter'];

			$model->created = date('c');
			$model->sent=0;
			$model->sender=Yii::app()->user->getUserID();
			$model->body = htmLawed::hl($model->body, array('safe'=>1, 'deny_attribute'=>'script, class, id'));
			
			if($model->save())
				$this->redirect(array('adminView','id'=>$model->id));
		}

        $sql = "SELECT id FROM user WHERE is_active = 1";
        $cnt = "SELECT COUNT(*) FROM ($sql) subq";
		$count = Yii::app()->db->createCommand($cnt)->queryScalar();  

		$this->render('create',array(
			'model'=>$model,
			'total_recipients'=>$count,
		));
	}

	/**
	 * Preview email
	 */
	public function actionSend($id)
	{
		$model=$this->loadModel($id);
		$model->setScenario('send');

 		$mailer = new Mailer();

		$users = Yii::app()->db->createCommand()
				->select('email')
				->from('user')
				->where('is_active = 1')
				->queryAll();

		$addresses = array();
		foreach($users as $recipient){
		    $model->recipients=$model->recipients.$recipient['email'].', ';
		    $addresses[] = trim($recipient['email']);
		}
		$model->recipients = substr_replace($model->recipients ,"",-2);
		$model->sender=Yii::app()->user->getUserID();
		$mailer->SetFrom($model->sent_as, Config::model()->findByPk('siglas')->value);
		$mailer->Subject=$model->subject;
		$mailer->Body=$model->body;

		if($mailer->sendBatches($addresses)){
			$model->sent=2;
			$model->published = date('c');
			Yii::app()->user->setFlash('success',__('Email sent OK'));
			Log::model()->write('Newsletter', __('Newsletter published'), $model->id);
		}else{
			$model->published = Null;
			$model->sent=1;
			Yii::app()->user->setFlash('error',__('Error while sending email').'<br />"'.$mailer->ErrorInfo.'"');
		}
		$model->save();
		echo 1;
		//$this->redirect(array('adminView','id'=>$model->id));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Newsletter'));
		$model=new Newsletter('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Newsletter']))
			$model->attributes=$_GET['Newsletter'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Newsletter']))
		{
			$model->attributes=$_POST['Newsletter'];
			if($model->save())
				$this->redirect(array('adminView','id'=>$model->id));
		}

        $sql = "SELECT id FROM user WHERE is_active = 1";
        $cnt = "SELECT COUNT(*) FROM ($sql) subq";
		$count = Yii::app()->db->createCommand($cnt)->queryScalar();  

		$this->render('update',array(
			'model'=>$model,
			'total_recipients'=>$count,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */

	public function actionDelete($id)
	{
		$model=$this->loadModel($id);
		if($model->sent == 0)	// admin can delete a draft
			$model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */

	public function actionIndex()
	{
		$this->layout='//layouts/column1';
		$this->pageTitle = Config::model()->findByPk('siglas')->value.' '.__('Newsletter');
		$criteria=new CDbCriteria;
		$criteria->addCondition('published IS NOT NULL');
		$criteria->order = 'published DESC';

		$dataProvider=new CActiveDataProvider('Newsletter',array('criteria'=>$criteria,'pagination'=>array('pageSize'=>1)));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Newsletter the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Newsletter::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Newsletter $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='newsletter-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
