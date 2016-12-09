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

// this is a mess !!!

class FileController extends Controller
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
			array('allow',
				'actions'=>array('wallpaper','showCMSfiles'),
				'expression'=>"Yii::app()->user->isEditor()",
			),
			array('allow',
				'actions'=>array(/*'view',*/'create','validateFileName',/*'update','admin',*/'delete'),
				'expression'=>"Yii::app()->user->isEditor() || Yii::app()->user->isTeamMember() || Yii::app()->user->isAdmin()",
			),
			array('allow',
				'actions'=>array('showBudgetFiles','databaseDownload','createZipFile','adminArchive'),
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
/*
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}
*/

	private function getPath($modelName,$modelID=Null){
		$path=$modelName;
		if($modelID)
			$path=$path.'/'.$modelID;
/*
		if($modelName == 'Reply')
			$path=$path.'/'.$modelID;
*/
		return $path;
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new File;

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['File']))
		{
			$model->attributes=$_POST['File'];
			$model->file=CUploadedFile::getInstance($model,'file');

			if($model->file){
				if($model->model == 'logo')
					$path = '';
				else
					$path=$this->getPath($model->model,$model->model_id);

				$model->path='/files/'.$path;

				if(!is_dir($model->getURI()))
					createDirectory($model->getURI());

				if($model->model == 'logo'){
					$ext = pathinfo($model->file->name, PATHINFO_EXTENSION);
					$model->path = $model->path.'logo.'.$ext;
				}else{
					$normalized_name = $model->normalize($model->file->name);
					$model->path=$model->path.'/'.$normalized_name;
				}
				if(!$model->name)
					$model->name=$model->file->name;

				if($model->model == 'logo'){
					if($logo = $model->findByAttributes(array('model'=>'logo')))
						$logo->delete();
				}
				if ($file_saved = $model->file->saveAs($model->getURI())){
					$model->save();
				}
				
				if($model->model == 'SitePage'){
					if($file_saved)
						Yii::app()->user->setFlash('success', __('File uploaded correctly'));
					$this->redirect(array('sitePage/admin'));

				}elseif($model->model == 'Reply'){
					$reply = Reply::model()->findByPk($model->model_id);
					if ($reply===null){
						throw new CHttpException(404,'The requested Reply does not exist.');
					}
					$enquiry = Enquiry::model()->findByPk($reply->enquiry);
					if($file_saved)
						$enquiry->promptEmail();
					$this->redirect(array('enquiry/teamView','id'=>$enquiry->id));

				}elseif($model->model == 'Enquiry'){
					if ($file_saved){
						$enquiry = Enquiry::model()->findByPk($model->model_id);
						if ($enquiry===null){
							throw new CHttpException(404,'The requested Enquiry does not exist.');
						}
						$enquiry->documentation = $model->id;
						$enquiry->save();
					}
					$this->redirect(array('enquiry/submit','id'=>$model->model_id));

				}elseif($model->model == 'wallpaper'){
					$this->redirect(array('file/wallpaper'));

				}elseif($model->model == 'DatabaseDownload/docs'){
					$this->redirect(array('file/databaseDownload'));

				}elseif($model->model == 'logo'){
					resizeLogo($model->getURI());
					$this->redirect(array('config/image'));

				}else
					$this->redirect(array('site/index'));
			}
		}

		if(isset($_GET['model']))
			$model->model=$_GET['model'];

		if(isset($_GET['model_id']))
			$model->model_id=$_GET['model_id'];

		echo $this->renderPartial('create',array('model'=>$model),false,true);
	}

	public function actionValidateFileName()
	{
		$model=new File;
		// doing validation like this because I think I can't do it with ajax in a modal window
		if(isset($_GET['file_name']))
		{
			$file_name = $model->normalize($_GET['file_name']);
			$path=$model->baseDir.'/files/'.$this->getPath($_GET['model'],$_GET['model_id']).'/'.$file_name;

			if (!$file_name){
				echo 'File required.';
			}
			elseif (!preg_match('/^[a-zA-Z0-9_\-]+\.[a-zA-Z]{3,4}$/', $file_name)){
    	        echo '"'.$file_name.'" Only characters a-z A-Z and 0-9 are allowed. ej: file.pdf';
			}
			elseif (file_exists($path)){
    	        echo '"'.$file_name.'" File already uploaded';
			}
			else{
				echo 1;
			}
			Yii::app()->end();
		}
		echo 'File required.';
	}

	public function actionCreateZipFile()
	{
		$file = new File;
		$zip_name = $file->normalize(Config::model()->findByPk('siglas')->value).'.zip';

		$file->model = 'DatabaseDownload';
		$file->path='/files/'.$file->model.'/'.$zip_name;
		$file->name = $zip_name;

		//$tmp_fn = tempnam(sys_get_temp_dir(), 'zip-');
		$tmpDir = Yii::app()->basePath.'/runtime/tmp/';
		$tmp_fn = $tmpDir.'zip-'.mt_rand(10000,99999);
		$zip = new ZipArchive();

		if ($zip->open($tmp_fn, ZIPARCHIVE::CREATE)){
			$source = $file->baseDir.'/files/'.$file->model;

			$zip->addEmptyDir('data');
			$nodes = scandir($source.'/data');
			foreach($nodes as $node){
				if(strpos($node, '.') !== 0)
					$zip->addFile($source.'/data/'.$node, 'data/'.$node);
			}
			$zip->addEmptyDir('docs');
			$nodes = scandir($source.'/docs');
			foreach($nodes as $node){
				if(strpos($node, '.') !== 0)
					$zip->addFile($source.'/docs/'.$node, 'docs/'.$node);
			}
			$zip->close();

			$old_zip = File::model()->findByAttributes(array('model'=>'DatabaseDownload'));
			$archive=Null;
			if($old_zip){
				$archive = Archive::model()->findByAttributes(array('path'=>$old_zip->path));
				if(file_exists($old_zip->getURI()))
					unlink($old_zip->getURI());
				// siglas may have changed
				$old_zip->path=$file->path;
				$old_zip->name=$file->name;

				$file = $old_zip;
			}
			if(!$archive)	// this is the first time zip has been created
				$archive = new Archive;

			$archive->name = str_replace(".zip", "", $file->name);
			$archive->path = $file->path;

			$language = Yii::app()->language;
			Yii::app()->language = getDefaultLanguage();
			$archive->description = __('MSG_ZIP_DESCRIPTION');
			Yii::app()->language = $language;

			$archive->extension = 'zip';
			$archive->created = date('Y-m-d');
			$archive->author = Yii::app()->user->getUserID();
			$archive->save();

			copy($tmp_fn, $file->getURI());
			unlink($tmp_fn);

			$file->save();
			Config::model()->updateSiteConfigurationStatus('siteConfigStatusZipFileUpdated', 1);
			Log::model()->write('ZipFile', 'Zip file updated');
			Yii::app()->user->setFlash('success',__('Zip file updated'));
		}else{
			Yii::app()->user->setFlash('error',__('Error: zip file not created'));
		}
		$this->redirect(array('file/databaseDownload'));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model=$this->loadModel($id);
		
		if(strpos($model->path, '/runtime') === 0)
			$model->baseDir = Yii::app()->basePath;
			
		$model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		//if(!isset($_GET['ajax']))
		//	$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */

	public function actionDatabaseDownload()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Zip file'));
		$this->render('adminDatabaseDownload');
	}
	public function actionShowCMSfiles()
	{
		echo $this->renderPartial('showCMSfiles',array(),false,true);
	}
	public function actionShowBudgetFiles()
	{
		echo $this->renderPartial('showBudgetFiles',array(),false,true);
	}
	public function actionWallpaper()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Wallpaper'));
		echo $this->render('wallpaper');
	}

	public function actionAdminArchive()
	{
		$model=new File('search');
		$model->unsetAttributes();  // clear any default values

		if(isset($_GET['File']))
			$model->attributes=$_GET['File'];

		$model->model='archive';

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
/*
	public function actionAdmin()
	{
		$model=new File('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['File']))
			$model->attributes=$_GET['File'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return File the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=File::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param File $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='file-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
