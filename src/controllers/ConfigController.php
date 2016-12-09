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

class ConfigController extends Controller
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
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(	'index','update',
									'email','observatory','social','locale',
									'image','backups','misc',
								),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model));
	}

	public function actionEmail()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'email'));
	}

	public function actionObservatory()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'observatory'));
	}

	public function actionSocial()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'social'));
	}

	public function actionLocale()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'locale'));
	}

	public function actionImage()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'image'));
	}

	public function actionBackups()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'backups'));
	}

	public function actionRequirements()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'checkSystemRequirements'));
	}

	public function actionMisc()
	{
		$model = new Config;
		$this->render('index',array('model'=>$model, 'page'=>'misc'));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'returnURL' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(!$model->can_edit)
			$this->redirect(array('index'));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$returnURL=Null;
		if(isset($_GET['returnURL']))
			$returnURL=$_GET['returnURL'];

		if(isset($_POST['Config']))
		{
			$model->attributes=$_POST['Config'];

			if($model->parameter == 'languages'){
				$model->value = str_replace(' ','',$model->value);
				$model->value = rtrim($model->value, ',');
				$model->setScenario('language');
			}
			elseif($model->parameter == 'observatoryBlog' || $model->parameter == 'socialFacebookURL' || $model->parameter == 'socialTwitterURL')
				$model->setScenario('URL');

			elseif($model->parameter == 'currencySymbol')
				$model->setScenario('currenyCollocation');

			elseif($model->parameter == 'socialTwitterUsername')
				$model->value = ltrim($model->value, '@');

			elseif($model->parameter == 'emailContactAddress' || $model->parameter == 'emailNoReply')
				$model->setScenario('email');

			elseif($model->parameter == 'siteColor')
				$model->setScenario('siteColor');

			elseif($model->parameter == 'year' || $model->parameter == 'vaultDefaultCapacity')
				$model->setScenario('positiveNumber');

			elseif($model->required)
				$model->setScenario('cannotBeEmpty');

			if(Yii::app()->params['ocaxnetwork']){
				$opts = array('http' => array(
										'method'  => 'POST',
										'header'  => 'Content-type: application/x-www-form-urlencoded',
										'ignore_errors' => '1',
										'timeout' => 0.5,
										'user_agent' => 'ocax-'.getOCAXVersion(),
									));
				$url = Yii::app()->request->hostInfo.Yii::app()->baseUrl;
				$url = str_replace("/", "|", $url);
				$context = stream_context_create($opts);
				@file_get_contents('http://network.ocax.net/register/'.$url, false, $context);
			}
			if($model->save()){
				if ($model->getScenario() == 'siteColor'){
					file_put_contents(dirname(Yii::app()->request->scriptFile).'/css/color.css', $this->renderPartial('//layouts/color',false,true));
				}
				$this->generateFoot();
				if (Yii::app()->request->isAjaxRequest){
					echo '1';
				}else{
					if ($returnURL){
						$this->redirect(array($returnURL));
					}else{
						$this->redirect(array('index'));
					}
				}
				Yii::app()->end();
			}else{
				if (Yii::app()->request->isAjaxRequest){
					echo CJavaScript::jsonEncode($model->getErrors());
					Yii::app()->end();
				}
			}
		}
		if (Yii::app()->request->isAjaxRequest){
			echo 0;
		}else{
			$this->render('update',array('model'=>$model,'returnURL'=>$returnURL));
		}
	}

	private function generateFoot()
	{
		if(!file_exists(Yii::app()->basePath.'/runtime/html/foot/'))
			createDirectory(Yii::app()->basePath.'/runtime/html/foot/');

		$user_lang = Yii::app()->language;
		$languages=explode(',', Config::model()->findByPk('languages')->value);
		foreach($languages as $lang){
			Yii::app()->language=$lang;
			file_put_contents(Yii::app()->basePath.'/runtime/html/foot/'.$lang.'.html', $this->renderPartial('//layouts/foot',false,true));
		}
		Yii::app()->language=$user_lang;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Config the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Config::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Config $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='config-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
