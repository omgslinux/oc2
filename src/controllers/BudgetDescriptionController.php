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

class BudgetDescriptionController extends Controller
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
				'actions'=>array('getDescription'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(	'view','create','update','translate','modify',
									'browseCommon','showCommon','browseState','showState',
									'admin','delete'),
				'expression'=>"Yii::app()->user->canEditBudgetDescriptions()",
			),
			/*
			array('allow',
				'actions'=>array('updateCommonDescriptions'),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			*/
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
		$this->render('update',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionShowCommon($id)
	{
		$model = BudgetDescCommon::model()->findByPk($id);
		if (!$model){
			$this->redirect(Yii::app()->createUrl('browseCommon'));
		}
		if($localModel = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$model->csv_id,'language'=>$model->language)))
			$this->redirect(Yii::app()->createUrl('/budgetDescription/update/'.$localModel->id));
		else
			$this->redirect(Yii::app()->createUrl('/budgetDescription/create?csv_id='.$model->csv_id.'&lang='.$model->language));
	}

	public function actionShowState($id)
	{
		$model = BudgetDescState::model()->findByPk($id);
		if (!$model){
			$this->redirect(Yii::app()->createUrl('browseState'));
		}
		if($localModel = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$model->csv_id,'language'=>$model->language)))
			$this->redirect(Yii::app()->createUrl('/budgetDescriptionupdate/'.$localModel->id));		
		else
			$this->redirect(Yii::app()->createUrl('/budgetDescription/create?csv_id='.$model->csv_id.'&lang='.$model->language));		
	}

	public function actionModify()
	{
		if(isset($_GET['budget'])){
			if($budget = Budget::model()->findByPk((int)$_GET['budget']))
				$csv_id = $budget->csv_id;
		}
		if(!isset($csv_id) && isset($_GET['csv_id'])){
				$csv_id = $_GET['csv_id'];
		}
		if(!$csv_id)
			$this->redirect(Yii::app()->createUrl('user/panel'));

		if(isset($_GET['lang']))
			$language = $_GET['lang'];
		else
			$language = Yii::app()->language;

		if($model = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$csv_id,'language'=>$language)))
			$this->redirect('update/'.$model->id);
		else
			$this->redirect('create?csv_id='.$csv_id.'&lang='.$language);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new BudgetDescLocal;
		$model->setScenario('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(!isset($_GET['csv_id']))
			$this->redirect(Yii::app()->createUrl('site/index'));

		if(isset($_POST['BudgetDescLocal']))
		{
			$model->attributes=$_POST['BudgetDescLocal'];
			$model->sanitize();
			$model->modified = date('c');
			if($model->save()){
				$word = Null;
				if(Config::model()->isSiteMultilingual())
					$word = 'language "'.$model->language.'" ';
				Log::model()->write('BudgetDescription',__('Description').' "'.$model->csv_id.'" '.$word.__('created'),$model->id);
				Yii::app()->user->setFlash('success', __('Budget description saved Ok')
													.'<br /><a href="http://agora.ocax.net/c/ocax/budget-descriptions">'
													.__('Do you want to share it with others observatories?').'</a>');
				$this->redirect(Yii::app()->createUrl('BudgetDescription/update/'.$model->id));
			}
		}

		if(isset($_GET['lang']))
			$language = $_GET['lang'];
		else
			$language = Yii::app()->language;

		// user may have changed language at create _form. Check to see if translation exists.
		if($local_desc = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$_GET['csv_id'],'language'=>$language)))
			$this->redirect(Yii::app()->createUrl('BudgetDescription/update/'.$local_desc->id));
		
		$common_desc = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$_GET['csv_id'],'language'=>$language));
		if(!$common_desc)
			$common_desc = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$_GET['csv_id']));

		if($common_desc){
			$model->csv_id = $common_desc->csv_id;
			$model->language = $common_desc->language;
			$model->concept = $common_desc->concept;
			$model->code = $common_desc->code;
			$model->label = $common_desc->label;
			//$model->description = $common_desc->description;
		}else{
			if($budget = Budget::model()->findByAttributes(array('csv_id'=>$_GET['csv_id']))){
				// these are the values that were imported with csvImport
				$model->concept = $budget->concept;
				$model->code = $budget->code;
				$model->label = $budget->label;
			}
			$model->csv_id = $_GET['csv_id'];
			$model->language = $language;
		}

		$this->pageTitle=CHtml::encode('Desc. '.$model->csv_id);
		$this->render('create',array('model'=>$model));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['BudgetDescLocal']))
		{
			$model->attributes=$_POST['BudgetDescLocal'];
			$model->sanitize();
			$model->modified = date('c');
			if($model->save()){
				$word = Null;
				if(Config::model()->isSiteMultilingual())
					$word = 'language "'.$model->language.'" ';
				Log::model()->write('BudgetDescription',__('Description').' "'.$model->csv_id.'" '.$word.__('updated'),$model->id);
				Yii::app()->user->setFlash('success', __('Budget description saved Ok')
													.'<br /><a href="http://agora.ocax.net/c/ocax/budget-descriptions" target="_blank">'
													.__('Do you want to share it with other observatories').'?</a>');
				$this->redirect(Yii::app()->createUrl('BudgetDescription/update/'.$model->id));
			}
		}

		// user wants to update the model but changed language.
		if(isset($_GET['lang'])){
			$csv_id = $model->csv_id;
			$model = $model->findByAttributes(array('csv_id'=>$model->csv_id,'language'=>$_GET['lang']));
			if(!$model){	
				$this->redirect(Yii::app()->createUrl('BudgetDescription/create',array('csv_id'=>$csv_id,'lang'=>$_GET['lang'])));
			}
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$this->pageTitle=CHtml::encode('Desc. '.$model->csv_id);
		$this->render('update',array('model'=>$model));
	}

	public function actionTranslate()
	{
		if(!isset($_GET['lang']) && !isset($_GET['csv_id']))
			$this->redirect(Yii::app()->createUrl('BudgetDescription/admin'));

		elseif($desc = BudgetDescLocal::model()->findbyAttributes(array('language'=>$_GET['lang'],'csv_id'=>$_GET['csv_id'])))
			$this->redirect(Yii::app()->createUrl('BudgetDescription/view/'.$desc->id));

		else{
			$budget = Budget::model()->findByAttributes(array('csv_id'=>$_GET['csv_id']));
			if ($budget===null){
				throw new CHttpException(404,'The requested Budget does not exist.');
			}
			$this->redirect(Yii::app()->createUrl('BudgetDescription/create?budget='.$budget->id.'&lang='.$_GET['lang']));
		}
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model=$this->loadModel($id);
		$model->delete();
		$word = Null;
		if(Config::model()->isSiteMultilingual())
			$word = 'language "'.$model->language.'" ';
		Log::model()->write('BudgetDescription',__('Description').' "'.$model->csv_id.'" '.$word.__('deleted'),$model->id);

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('BudgetDescLocal');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Budget descriptions'));
		$model=new BudgetDescLocal('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['BudgetDescLocal']))
			$model->attributes=$_GET['BudgetDescLocal'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionBrowseCommon()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Common descriptions'));
		$model=new BudgetDescCommon('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['BudgetDescCommon']))
			$model->attributes=$_GET['BudgetDescCommon'];

		$this->render('browseCommon',array(
			'model'=>$model,
		));
	}

	public function actionBrowseState()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('State descriptions'));
		$model=new BudgetDescState('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['BudgetDescState']))
			$model->attributes=$_GET['BudgetDescState'];

		$this->render('browseState',array(
			'model'=>$model,
		));
	}

	/*
	 * Only used to update commonDescriptions with localDescription data
	 * !!Remember to delete all localDescriptions!!
	 */
	public function actionUpdateCommonDescriptions()
	{
		$cnt=0;
		$local_descriptions = BudgetDescLocal::model()->findAll();
		foreach($local_descriptions as $local_description){
			echo $local_description->csv_id.'<br />';
			$common_description = BudgetDescCommon::model()->findByAttributes(
										array('csv_id'=>$local_description->csv_id, 'language'=>$local_description->language)
									);
			if(!$common_description){
				echo 'no common<br />';
				$common_description = new BudgetDescCommon;
				$common_description->csv_id = $local_description->csv_id;
				$common_description->language = $local_description->language;
				$common_description->code = $local_description->code;
			}else
				echo 'yes common<br />';
			if($local_description->label)
				$common_description->label = $local_description->label;
			if($local_description->concept)
				$common_description->concept = $local_description->concept;
			if($local_description->description){
				$common_description->description = $local_description->description;
				$common_description->text = $local_description->text;
			}
			if($local_description->label || $local_description->concept || $local_description->description){
				$common_description->modified = date('c');
				$common_description->save();
				//$local_description->delete();
				$cnt = $cnt+1;
			}
		}
		echo 'Updated/New Common Descriptions: '.$cnt;
		Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return BudgetDescLocal the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=BudgetDescLocal::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param BudgetDescription $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='budget-description-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
