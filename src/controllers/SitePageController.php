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

class SitePageController extends Controller
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
				'actions'=>array('show'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(	'admin', 'delete', 'create', 'toggleSafeHTML', 'toggleShowTitle',
									'update', 'preview', 'editPreview', 'savePreview'),
				'expression'=>"Yii::app()->user->isEditor()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Preview page fro CMS editor
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionPreview($id)
	{
		if(isset($_GET['lang']))
			$lang=$_GET['lang'];
		else
			Yii::app()->end();
		
		$this->layout='//layouts/column1';
		$model = $this->loadModel($id);
		$content=SitePageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>$lang));
		if ($content===null){
			throw new CHttpException(404,'The requested Content does not exist.');
		}
		$this->performAjaxValidation($model);
		if(isset($_POST['SitePage'], $_POST['SitePageContent']))
		{
			$model->attributes=$_POST['SitePage'];	
			$content->attributes=$_POST['SitePageContent'];
			if($model->validate() && $content->validate()){
				$model->save();
				$this->render('show',array(
					'model'=>$model,
					'content'=>$content,
					'preview'=>1,
				));

				Yii::app()->end();
			}
		}
		$this->render('update',array(
			'model'=>$model,
			'content'=>$content,
		));
	}

	public function actionShow($pageURL)
	{
		$_404=Null;
		$content=SitePageContent::model()->findByAttributes(array('pageURL'=>$pageURL));
		if ($content===null){
			$_404=1;
		}else{
			$model = $this->loadModel($content->page);
			if ($model->published == 0 && !Yii::app()->user->isEditor()){
				$_404=1;
			}
		}
		if($_404){
			throw new CHttpException(404,'The requested page does not exist.');
			return $model;
		}
		$this->layout='//layouts/column1';		
		$content = $model->getContentForModel(Yii::app()->language);
		$this->render('show',array(
			'model'=>$model,
			'content'=>$content,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Create page'));
		// http://www.yiiframework.com/wiki/19/
		$model=new SitePage;
		$content =new SitePageContent;
		
		if ($cookie = Yii::app()->request->cookies['lang']){
			$content->language=$cookie->value;
		}else{
			$languages=explode(',', Config::model()->findByPk('languages')->value);
			$content->language=$languages[0];
		}

		$model->setScenario('create');
		//$this->performAjaxValidation($model);

		if(isset($_POST['SitePage'], $_POST['SitePageContent']))
		{
			$model->attributes=$_POST['SitePage'];
			$model->advancedHTML=0;
			$model->showTitle=1;
			$model->published=0;
			$content->attributes=$_POST['SitePageContent'];
			$content->page=0;	// dummy value. should do this with validation rule but it didn't work.
		
			if($model->validate() && $content->validate()){
				$model->save();
				$content->page=$model->id;
				$content->save();
				$word = Null;
				if(Config::model()->isSiteMultilingual())
					$word = 'language "'.$content->language.'" ';
				Log::model()->write('sitePage',__('Page').' "'.$content->pageTitle.'" '.$word.__('created'), $model->id);

				$this->layout='//layouts/column1';
				$this->render('show',array(
					'model'=>$model,
					'content'=>$content,
					'preview'=>1,
				));
				Yii::app()->end();
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
			//$lang=getDefaultLanguage();
			$lang=Yii::app()->user->getState('applicationLanguage');
		}
		$model=$this->loadModel($id);
		$content=SitePageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>$lang));
		if(!$content){
			// editing a language for the fisrt time. So we copy content from original language to help with the translation
			$orig_content=SitePageContent::model()->findByAttributes(array('page' => $model->id), array('condition'=>'pageURL IS NOT NULL'));
			if ($orig_content===null){
				throw new CHttpException(404,'The requested Original content does not exist.');
			}
			$content = new  SitePageContent;
			$content->language = $lang;
			$content->pageURL = $orig_content->pageURL;
			$content->pageTitle = $orig_content->pageTitle;
			$content->previewBody = $orig_content->body;
			$content->page=$model->id;
			$content->save();
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['SitePage'], $_POST['SitePageContent']))
		{
			$model->attributes=$_POST['SitePage'];
			$content->attributes=$_POST['SitePageContent'];

			if($model->validate() && $content->validate()){
				$model->save();
				$content->save();
				$this->redirect(array('preview','id'=>$model->id,'lang'=>$content->language));
			}
		}

		if(!$content->previewBody)
			$content->previewBody = $content->body; 

		$this->render('update',array(
			'model'=>$model,
			'content'=>$content,
		));
	}

	public function actionEditPreview($id,$lang)
	{
		$model=$this->loadModel($id);
		$content=SitePageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>$lang));
		if ($content===null){
			throw new CHttpException(404,'The requested Content does not exist.');
		}
		if($_POST['SitePageContent'])
			$content->attributes=$_POST['SitePageContent'];
		else
			Yii::app()->end();

		$this->render('update',array(
			'model'=>$model,
			'content'=>$content,
		));
	}

	public function actionSavePreview($id,$lang)
	{
		$model=$this->loadModel($id);
		$content=SitePageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>$lang));
		if ($content===null){
			throw new CHttpException(404,'The requested Content does not exist.');
		}
		if($_POST['SitePageContent'])
			$content->attributes=$_POST['SitePageContent'];
		else
			Yii::app()->end();
		
		$content->body = $content->previewBody;
		$content->previewBody = '';
		$content->save();

		$word = Null;
		if(Config::model()->isSiteMultilingual())
			$word = 'language "'.$content->language.'" ';
		Log::model()->write('sitePage',__('Page').' "'.$content->pageTitle.'" '.$word.__('updated'), $model->id);
		Yii::app()->user->setFlash('success', __('Changes saved Ok'));
		$this->redirect($this->createUrl('p/'.$content->pageURL));
	}

	public function actionToggleSafeHTML($id)
	{
		$model=$this->loadModel($id);
		$model->advancedHTML ? $model->advancedHTML = 0 : $model->advancedHTML = 1;
		$model->save();
		echo $model->advancedHTML;
	}

	public function actionToggleShowTitle($id)
	{
		$model=$this->loadModel($id);
		$model->showTitle ? $model->showTitle = 0 : $model->showTitle = 1;
		$model->save();
		echo $model->showTitle;
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model=$this->loadModel($id);
		$content=SitePageContent::model()->findByAttributes(array('page'=>$model->id,'language'=>getDefaultLanguage()));
		if ($content===null){
			$pageTitle = '';
		}else{
			$pageTitle = $content->pageTitle;
		}
		foreach($model->sitePageContents as $content)
				$content->delete();
				
		$model->delete();
		Log::model()->write('sitePage',__('Page').' "'.$pageTitle.'" '.__('deleted'), $model->id);

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('SitePage');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Manage pages'));
		$model=new SitePage('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SitePage']))
			$model->attributes=$_GET['SitePage'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return SitePage the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=SitePage::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param SitePage $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sitePage-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
