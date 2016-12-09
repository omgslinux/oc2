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

/**

#### The vault creation proceedure ####

Andy = I will save Dave's copies on my server. I am LOCAL-Andy.
Dave = Andy will save my copies on his server. I am REMOTE-Dave.

0.	LOCAL-Andy = I create a LOCAL vault because I will save REMOTE-Dave's copies on my server
	vault->create() LOCAL-Andy defines available schedule.
	vault->beforeSave() generates key for local vault.
	
0.	REMOTE-Dave = I create a REMOTE vault becuase I will save my copies on LOCAL-Andy's server

1.	concept:	LOCAL-Andy says to REMOTE-Dave. "Hey Dave, here is the vault key."
				!! This step is not done via OCAx software. This is human communication VIA EMAIL OR TELF. !!
	human:		Andy tells Dave the key.
	
2.	concept:	REMOTE-Dave submits the key
	function:	REMOTE-Dave actionConfigureKey();
	function:	REMOTE-Dave calls LOCAL-Andy's vault/verifyKey

	// Key exchange completed //

3.	concept:	REMOTE-Dave chooses a subset of avaibable days to make copies
	function:	REMOTE-Dave calls LOCAL-Andy's vault/getSchedule
	function:	REMOTE-Dave selectes backup schedule. actionConfigureSchedule()
	function:	REMOTE-Dave calls LOCAL-Andy's vault/setSchedule


#### The backup transfer proceedure #####

LOCAL-Andy = I will save REMOTE-Dave's copies on my server
REMOTE-Dave = LOCAL-Andy will save my copies on his server

0.	function:	LOCAL-Andy's server runs VaultSchedule::model()->runVaultSchedule();	// sort of a cronjob

1.	concept:	LOCAL-Andy asks REMOTE-Dave "Hey, Dave, have you got your dump ready?"
	function:	LOCAL-Andy's server calls REMOTE-Dave's vault/localWaitingToStartCopyingBackup
	
2.	concept:	REMOTE-Dave replies to LOCAL-Andy "Yes. start copying."
	function:	REMOTE-Dave's server calls LOCAL-Andy's vault/startCopyingBackup
	
3.	concept:	LOCAL-Andy says to REMOTE-Dave "Ok. Give me the file"
	function:	LOCAL-Andy's server calls REMOTE-Dave's vault/startTransfer

4.	concept:	LOCAL-Andy informs REMOTE-Dave "Ok. We've finished copying."
	function:	LOCAL-Andy's server calls REMOTE-Dave's vault/transferComplete

 */

class VaultController extends Controller
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
			array('allow',  // allow automated backups
				'actions'=>array(	'verifyKey', 'getSchedule', 'setSchedule',
									'localWaitingToStartCopyingBackup',
									'startCopyingBackup',
									'startTransfer',
									'transferComplete',
									'changeCapacity',
									'deleteBackup',
								),
				'expression'=>"Config::model()->findByPk('siteAutoBackup')->value",
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(	'view', 'viewSchedule', 'admin',
									'create',
									'configureKey', 'configureSchedule',
									'updateCapacity',
									'delete'),
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
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Backup vault'));
		$model=$this->loadModel($id);
		$backups = Backup::model()->getDataproviderByVault($model->id);
		$this->render('view',array('model'=>$model,'backups'=>$backups));
	}

	/**
	 * 
	 * Show all configured Vault schedules
	 */
	public function actionViewSchedule()
	{
		$localVaults = Vault::model()->findAllByAttributes(array('type'=>LOCAL, 'state'=>READY));
		$remoteVaults = Vault::model()->findAllByAttributes(array('type'=>REMOTE, 'state'=>READY));
		if(Yii::app()->request->isAjaxRequest){
			$layout='//layouts/column1';
			echo $this->renderPartial('schedule',array(
												'localVaults' =>$localVaults,
												'remoteVaults'=>$remoteVaults
												),
										true,false);
		}else{
			$this->render('schedule',array(
								'localVaults' =>$localVaults,
								'remoteVaults'=>$remoteVaults,
						));
		}
	}

// #### The vault creation proceedure ####

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Create vault'));
		$model=new Vault;

		$model->schedule='0000000';	// seven 0's = seven days in a week. starts on monday.
		if(isset($_POST['Vault']))
		{
			$model->attributes=$_POST['Vault'];
			$model->host = rtrim($model->host, '/');
			$model->created = date('c');
			$model->count = 0;
			$model->capacity = Config::model()->findByPk('vaultDefaultCapacity')->value;
			$model->state=CREATED;
			if($model->type == REMOTE)
				$model->schedule='0000000';
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}
		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * REMOTE-Dave submits the key (LOCAL-Andy told him the key on the phone)
	 * Executed on REMOTE-Dave's server
	 */
	public function actionConfigureKey($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Vault']))
		{
			$model->attributes=$_POST['Vault'];
			if($model->state == CREATED && $model->key){
				$model->setScenario('newKey');
				if($model->validate()){
					if($model->type == REMOTE && $model->state < VERIFIED){
						$vaultName = $model->host2VaultName(Yii::app()->getBaseUrl(true),0);
						$reply=Null;
						// REMOTE-Dave calls LOCAL-Andy's vault/verifyKey
						$reply = @file_get_contents($model->host.'/vault/verifyKey'.
																	'?key='.$model->key.
																	'&vault='.$vaultName,
																	false,
																	$model->getStreamContext(3)
													);
						if(is_numeric($reply) && $reply > 0){	// positive reply = key is ok. $reply is LOCAL-Andy's vault capacity
							$model->state = VERIFIED;
							$model->saveKey();
							$model->capacity = $reply;
							$model->save();
							Yii::app()->user->setFlash('success', $model->host.' '.__('verifies the key ok'));
							$this->redirect(array('view','id'=>$model->id));
						}else
							Yii::app()->user->setFlash('error', $model->host.' '.__('rejects the key'));
					}
				}
			}
			elseif($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * REMOTE-Dave sends a key to LOCAL-Andy to be verified
	 * Executed on LOCAL-Andy's server
	 */
	public function actionVerifyKey()
	{
		if($model = Vault::model()->findByIncomingCreds(LOCAL)){
			if($model->state == CREATED){
				$model->state = VERIFIED;
				$model->save();
			}
			// Yes. the key is ok. LOCAL-Andy sends REMOTE-Dave the vault capacity as an answer.
			echo Config::model()->findByPk('vaultDefaultCapacity')->value;
		}else		
			echo 0;
	}

	/**
	 * REMOTE-Dave asks for available days
	 * Executed on LOCAL-Andy's server
	 */
	public function actionGetSchedule()
	{
		if($model = Vault::model()->findByIncomingCreds(LOCAL))
			echo $model->schedule;
		else
			echo 0;
	}

	/**
	 * REMOTE-Dave submits his choice of days (a subset of available days)
	 * Executed on REMOTE-Dave's server
	 */
	public function actionConfigureSchedule($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Vault']))
		{
			$model->attributes=$_POST['Vault'];
			if($model->type == REMOTE && $model->state == VERIFIED){
				$vaultName = $model->host2VaultName(Yii::app()->getBaseUrl(true),0);
				$reply = Null;
				$reply = @file_get_contents($model->host.'/vault/setSchedule'.
															'?key='.$model->key.
															'&vault='.$vaultName.
															'&schedule='.$model->schedule,
															false,
															$model->getStreamContext()
											);
				if($reply == 1){
					$model->state = READY;
					$model->save();
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}
		$this->render('view',array('model'=>$model));
	}
	
	/**
	 * REMOTE-Dave sends his choice of days (a subset of available days) to LOCAL-Andy
	 * Executed on LOCAL-Andy's server
	 */
	public function actionSetSchedule()
	{
		if($model = Vault::model()->findByIncomingCreds(LOCAL)){
			if($model->state >= READY){
				echo 0;
				Yii::app()->end();
			}
			$model->schedule = $_GET['schedule'];
			$model->state = READY;
			if($model->save()){
				echo 1;
				Yii::app()->end();
			}
		}	
		echo 0;
		Yii::app()->end();
	}

// #### The backup transfer proceedure #####

	/*
	 * LOCAL-Andy tells REMOTE-Dave to get the copy ready
	 * Executed on REMOTE-Dave's server
	 */
	public function actionLocalWaitingToStartCopyingBackup()
	{
		if($model = Vault::model()->findByIncomingCreds(REMOTE)){
			$model->makeReady();
			if($model->state == READY){
				// Don't start another backup if we've already created one today.
				if(Backup::model()->findByDay(date('Y-m-d'), $model->id )){
					echo 0;
					Yii::app()->end();
				}
				$backup = new Backup;
				$backup->vault = $model->id;
				$backup->created = date('c');
				// save it now because buildBackupFile() can take time and we don't want do to run it twice.
				$backup->save();
				
				if($backup->buildBackupFile()){
					$backup->filesize = filesize($model->getVaultDir().$backup->filename);	
					$backup->save();	
					$model->state = LOADED;
					$model->save();
				}else
					$backup->delete();

				echo 0;
				Yii::app()->end();
			}
			// LOADED	We've got the backup file ready for copying. Tell LOCAL-Andy.
			// BUSY		Maybe LOCAL-Andy didn't recieve vault/StartCopyingBackup. Let's send it again.
			if($model->state == LOADED || $model->state == BUSY){
				if($backup = Backup::model()->findByDay(date('Y-m-d'), $model->id )){
					// REMOTE-Dave has the copy ready and tells LOCAL-Andy to start the transfer
					$vaultName = $model->host2VaultName(Yii::app()->getBaseUrl(true), 0);
					@file_get_contents($model->host.'/vault/startCopyingBackup'.
													'?key='.$model->key.
													'&vault='.$vaultName.
													'&filename='.$backup->filename,
													false,
													$model->getStreamContext()
										);
				}
				echo 0;
				Yii::app()->end();
			}
		}
	}

	/*
	 * Main copying procedure.
	 * Executed on LOCAL-Andy's server
	 */
	public function actionStartCopyingBackup()
	{
		if($model = Vault::model()->findByIncomingCreds(LOCAL)){
			if(Backup::model()->findByDay(date('Y-m-d'), $model->id )){ // Don't copy twice
				echo 0;
				Yii::app()->end();
			}
			$backup = new Backup;
			$backup->vault = $model->id;
			$backup->filename = $_GET['filename'];
			$backup->created = date('c');
			$backup->initiated = date('c');
			if($backup->save()){
				$model->state = BUSY;
				$model->save();
					
				$vaultName = $model->host2VaultName(Yii::app()->getBaseUrl(true), 0);
				$source = $model->host.'/vault/startTransfer'.
										'?key='.$model->key.
										'&vault='.$vaultName;
							
				$dest = $model->getVaultDir().$backup->filename;
				copy($source, $dest);

				$backup->completed = date('c');
				$backup->state = FAIL;	// let's check the filesize before we say SUCCESS
				$backup->save();
				$model->state = READY;
				$model->save();
					
				if($backup->filesize = filesize($model->getVaultDir().$backup->filename)){
					$vaultName = $model->host2VaultName(Yii::app()->getBaseUrl(true), 0);
					$confirmation = 'nada';
					$confirmation = @file_get_contents($model->host.'/vault/transferComplete'.
											'?key='.$model->key.
											'&vault='.$vaultName.
											'&filesize='.$backup->filesize,
											false,
											$model->getStreamContext(3)
								);
					if($confirmation == 1){
						$backup->state = SUCCESS;
						$model->count = $model->count+1;
						$model->save();

						if($model->isVaultFull())
							$model->deleteOldestBackup();	// calls REMOTE-Dave's server first. Then delete LOCAL copy.
					}
				}
				$backup->save();
			}
		}
	}
	// Executed on REMOTE-Dave's server
	public function actionStartTransfer()
	{
		if($model = Vault::model()->findByIncomingCreds(REMOTE)){
			
			if($backup = Backup::model()->findByDay(date('Y-m-d'), $model->id )){
				if($backup->initiated){
					echo 0;
					Yii::app()->end();
				}
				$backup->initiated = date('c');
				$backup->save();
				$model->state = BUSY;
				$model->save();

				$backup->download();
			}
		}
		echo 0;
		Yii::app()->end();
	}

	// Executed on REMOTE-Dave's server
	public function actionTransferComplete()
	{
		if($model = Vault::model()->findByIncomingCreds(REMOTE)){
			if($backup = Backup::model()->findByDay(date('Y-m-d'), $model->id)){
				if($backup->state == SUCCESS ){
					echo $backup->state;
					Yii::app()->end();				
				}
				$model->state = READY;
				$backup->completed = date('c');
				
				if(isset($_GET['filesize']) && $_GET['filesize'] == $backup->filesize){
					$backup->state = SUCCESS;
					$model->count = $model->count+1;
				}else
					$backup->state = FAIL;

				$backup->save();
				$model->save();
				
				if($model->type == REMOTE)	// condition shouldn't be necessary
					unlink($model->getVaultDir().$backup->filename);
					
				if(Config::model()->findByPk('siteAutoBackupEmailAlert')){
					if($admins=User::model()->findAllByAttributes(array('is_admin'=>'1'))){
						$mailer = new Mailer();
						$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);

						foreach($admins as $admin)
							$mailer->AddAddress($admin->email);

						if($backup->state === SUCCESS){
							$mailer->Subject=Config::model()->findByPk('siglas')->value.' backup ok';
							$mailer->Body='<p>Backup copied ok</p>';
						}else{
							$mailer->Subject=Config::model()->findByPk('siglas')->value.' backup failed';
							$mailer->Body='<p>Backup failed</p>';
						}
						$mailer->Body=$mailer->Body.'<p>Filename: '.$backup->filename.'<br />';
						$mailer->Body=$mailer->Body.'Filesize: '.$backup->fileSizeForHumans().'<br />';
						$mailer->Body=$mailer->Body.__('Vault').': '.$backup->vault0->host.'</p>';
						$mailer->send();
					}
				}
				echo $backup->state;
				Yii::app()->end();
			}
		}
		echo 0;
		Yii::app()->end();
	}

// #####
// ##### Other admin actions #####
// #####

	/*
	 * LOCAL-Andy changes this vaults capacity. Andy is storing the backups, so Andy decides.
	 * Executed on LOCAL-Andy's server
	 */
	public function actionUpdateCapacity($id)
	{
		$model= $this->loadModel($id);
		if(isset($_POST['Vault']))
		{
			$oldCapacity=$model->capacity;
			$model->attributes=$_POST['Vault'];
			if($model->save()){
				$confirmation = Null;
				$vaultName = $model->host2VaultName(Yii::app()->getBaseUrl(true), 0);
				// Tell REMOTE-Dave that the vault capacity has changed
				$confirmation = @file_get_contents($model->host.'/vault/changeCapacity'.
										'?key='.$model->key.
										'&vault='.$vaultName.
										'&capacity='.$model->capacity,
										false,
										$model->getStreamContext(3)
							);
				if($confirmation == 1)		
					Yii::app()->user->setFlash('success', $model->host.' '.__('notified. Updated correctly'));
				else{
					$model->capacity=$oldCapacity;
					$model->save();
					Yii::app()->user->setFlash('notice', $model->host.' '.__('does not reply'));
				}
			}
			$this->redirect(array('view','id'=>$model->id));
		}
		echo $this->renderPartial('_capacity',array('model'=>$model),true,true);
	}
	/*
	 * LOCAL-Andy tells REMOTE-Dave the vault capacity has changed
	 * Executed on REMOTE-Dave's server
	 */
	public function actionChangeCapacity()
	{
		if($model = Vault::model()->findByIncomingCreds(REMOTE)){
			if(isset($_GET['capacity'])){
				$model->capacity = $_GET['capacity'];
				if($model->save()){
					echo 1;
					Yii::app()->end();
				}
			}
		}
		echo 0;
	}
	
	/*
	 * LOCAL-Andy tells REMOTE-Dave the vault capacity is full. LOCAL-Andy wants to delete a backup
	 * Executed on REMOTE-Dave's server
	 */
	public function actionDeleteBackup()
	{
		if($model = Vault::model()->findByIncomingCreds(REMOTE)){
			if(isset($_GET['filename'])){
				$backup = Backup::model()->findByAttributes(array('vault'=>$model->id,'filename'=>$_GET['filename']));
				if($backup){
					if($backup->delete()){
						echo 1;
						Yii::app()->end();
					}
				}
			}
		}
		echo 0;
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model= $this->loadModel($id);

		foreach($model->backups as $backup)
			$backup->delete();
			
		foreach($model->vaultSchedules as $schedules)
			$schedules->delete();
			
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
		$this->redirect(array('/backup/admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Backups'));
		$model=new Vault('search');
		$model->unsetAttributes();  // clear any default values
		
		$localVaults=new CActiveDataProvider('Vault', array(
							'criteria'=>array('condition'=>"type=0")
						));
		$remoteVaults=new CActiveDataProvider('Vault', array(
							'criteria'=>array('condition'=>"type=1")
						));
				
		$this->render('admin',array(
			'model'=>$model, 'localVaults'=>$localVaults, 'remoteVaults'=>$remoteVaults
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Vault the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Vault::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Vault $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='vault-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
