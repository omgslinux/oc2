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
 
class LogController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','modalIndex'),
				'expression'=>"Yii::app()->user->isPrivileged()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
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

	public function actionModalIndex()
	{
		$prefixes = Null;
		$id = Null;
		if(isset($_GET['prefixes']))	// a prefix is may be a model name
			$prefixes = $_GET['prefixes'];
		if(isset($_GET['id']))			// if defined, it is a model id
			$id = $_GET['id'];

		$title = $prefixes;
		if($id)
			$title = $title.' '.$id;
			
		$criteria = new CDbCriteria;
		$prefixes = explode(',', $prefixes);

		if(!$id){
			$condition = 'prefix =:prefix_0';
			$criteria->params[':prefix_0'] = trim(array_shift($prefixes));
			
			$cnt=1;
			foreach($prefixes as $prefix){
				$paramname = ":prefix_".$cnt;
				$condition = $condition.' OR prefix = '.$paramname ;
				$criteria->params[$paramname] = $prefix;
				$cnt=$cnt+1;
			}
			$criteria->addCondition($condition);
		}else{
			// we are displaying an object's log
			$criteria->addCondition('prefix = :prefix AND model_id =:id');
			$criteria->params[':prefix'] = trim($prefixes[0]);
			$criteria->params[':id'] = $id;
		}
		$criteria->order = 'created DESC';
		$criteria->limit = 20;

		$logs = Log::model()->findAll($criteria);
			
		//echo CJavaScript::jsonEncode($this->renderPartial('ajaxIndex',array('prefixes'=>$prefixes),false,false));
		echo $this->renderPartial('modalIndex',array('title'=>$title, 'logs'=>$logs),false,false);
	}
	
	public function actionIndex()
	{
		$model=new Log('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Log']))
			$model->attributes=$_GET['Log'];
		$this->render('index',array('model'=>$model));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Log the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Log::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Log $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='log-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
