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


class BackupController extends Controller
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
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin', 'manualCreate', 'downloadBackup'),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Creates a backup.
	 */
	public function actionManualCreate()
	{
		$model=new Backup;
		$backupDir = Yii::app()->basePath.'/runtime/manualbackup/';

		if(!is_dir($backupDir)){
			createDirectory($backupDir);
		}else{
			$nodes = scandir($backupDir);
			foreach($nodes as $node){
				if(strpos($node, $model->filenamePrefix) !== FALSE)
					unlink($backupDir.$node);
			}
		}
		list($path, $file, $dump_error) = $model->siteBackup($backupDir);

		if (file_exists($path.$file)) {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".$file."\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($path.$file));
			ob_end_flush();
			@readfile($path.$file);
			exit;
		}
		if($dump_error){
			Yii::app()->user->setFlash('error', __($dump_error));
			$this->redirect(array('/user/panel'));
		}
	}

	/**
	 * Manages all vaults.
	 */
	public function actionAdmin()
	{
		$this->redirect(array('/vault/admin'));
	}

	/**
	 * Admin can download the copy made on this server
	 */

	public function actionDownloadBackup($id)
	{
		$model=$this->loadModel($id);
		$model->download();
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
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Backup the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Backup::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Backup $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='backup-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
