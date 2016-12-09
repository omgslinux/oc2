<?php
/**
 * OCAX -- Citizen driven Municipal Observatory software
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
 
class IntroPageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	public $defaultAction = 'admin';

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
				'users'=>array('*'),
				'actions'=>array('getPage'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('view','admin','delete','create','update'),
				'expression'=>"Yii::app()->user->isEditor()",
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
	public function actionView($id,$lang)
	{
		$this->layout='//layouts/column1';
		$model = $this->loadModel($id);
		$content=$model->getContent($lang);

		$this->render('view',array(
			'model'=>$model,
			'content'=>$content,
		));
	}
	
	public function actionGetPage($id)
	{
		$model = $this->loadModel($id);
		$content=$model->getContent(Yii::app()->user->getState('applicationLanguage'));
		echo $this->renderPartial('show', array('model'=>$model,'content'=>$content));
	}	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Create page'));
		$model=new IntroPage;
		$content=new IntroPageContent;

		$languages=explode(',', Config::model()->findByPk('languages')->value);
		$content->language=$languages[0];
				
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['IntroPage'], $_POST['IntroPageContent']))
		{
			$model->attributes=$_POST['IntroPage'];
			$content->attributes=$_POST['IntroPageContent'];
			$content->body=nl2br($content->body);
			$content->page=0;	// dummy value. should do this with validation rule but it didn't work.
			if($model->validate() && $content->validate()){
				$model->save();
				$content->page=$model->id;
				$content->save();
				Log::model()->write('introPage',__('introPage').' "'.$content->title.'" '.__('created'), $model->id);
				$this->redirect(array('view','id'=>$model->id,'lang'=>$content->language));
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'content'=>$content,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Update page'));
		if(isset($_GET['lang']))
			$lang=$_GET['lang'];
		else{
			$languages=explode(',', Config::model()->findByPk('languages')->value);
			$lang=$languages[0];
		}
		$model=$this->loadModel($id);
		$content=IntroPageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>$lang));
		if (!$content){
			$orig_content=IntroPageContent::model()->find(array('condition'=> 'page = '.$model->id));
			if ($orig_content===null){
				throw new CHttpException(404,'The requested Original content does not exist.');
			}
			$content = new IntroPageContent;
			$content->language = $lang;
			$content->title = $orig_content->title;
			$content->subtitle = $orig_content->subtitle;
			$content->body = $orig_content->body;
			$content->page=$model->id;
			$content->save();
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['IntroPage'], $_POST['IntroPageContent']))
		{
			$model->attributes=$_POST['IntroPage'];
			$content->attributes=$_POST['IntroPageContent'];
			$content->body=nl2br($content->body);
			if($model->validate() && $content->validate()){
				$model->save();
				$content->save();
				Log::model()->write('introPage',__('introPage').' "'.$content->title.'" '.__('updated'), $model->id);
				$this->redirect(array('view','id'=>$model->id,'lang'=>$content->language));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'content'=>$content,
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
		$content=IntroPageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>getDefaultLanguage()));
		$content ? $title= $content->title : $title = '';
		
		foreach($model->introPageContents as $content)
				$content->delete();		
		$model->delete();
		
		Log::model()->write('introPage',__('introPage').' "'.$title.'" '.__('deleted'), $model->id);
		
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Manage pages'));
		$model=new IntroPage('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['IntroPage']))
			$model->attributes=$_GET['IntroPage'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return IntroPage the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=IntroPage::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param IntroPage $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='intro-page-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
