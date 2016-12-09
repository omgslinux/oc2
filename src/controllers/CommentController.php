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

class CommentController extends Controller
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('getForm', 'create','delete'/*,'update'*/),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin'),
				'users'=>array('admin'),
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionGetForm($comment_on, $id)
	{
		//if(!Yii::app()->request->isAjaxRequest)
			//Yii::app()->end();

		$model = new Comment;
		$model->model = $comment_on;
		$model->model_id = $id;
		
		$user=User::model()->findByPk(Yii::app()->user->getUserID());
		if ($user===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		echo CJavaScript::jsonEncode(array(
						'html'=>$this->renderPartial('_form',array('model'=>$model,'fullname'=>$user->fullname),true,true)));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();

		$model=new Comment;
		$commentCreatedNewSubscription=0;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Comment']))
		{
			$model->attributes=$_POST['Comment'];
			$model->body = htmLawed::hl($model->body, array('elements'=>'-*', 'keep_bad'=>0));
			$model->body = nl2br($model->body);
			$model->user=Yii::app()->user->getUserID();
			$model->created=date('c');
			if($model->save()){
				if($model->model == 'Enquiry' || $model->model == 'Reply'){
					if ($model->model == 'Enquiry'){
						$enquiry = Enquiry::model()->findByPk($model->model_id);
						if ($enquiry===null){
							throw new CHttpException(404,'The requested Enquiry does not exist.');
						}
					}else{
						$reply = Reply::model()->findByPk($model->model_id);
						if ($reply===null){
							throw new CHttpException(404,'The requested Reply does not exist.');
						}
						$enquiry = $reply->enquiry0;
					}
					$commentCreatedNewSubscription = EnquirySubscribe::model()->subscribeUser($enquiry->id, $model->user);
					echo CJavaScript::jsonEncode(array(
						'html'=>$this->renderPartial('_view',array('data'=>$model),true,true),
						'newSubscription'=>$commentCreatedNewSubscription,
					));
					$criteria = array(
						'with'=>array('enquirySubscribes'),
						'condition'=>' enquirySubscribes.enquiry = :enquiry',
						'together'=>true,
						'params'=>array(':enquiry'=>$enquiry->id),
					);
					$subscribedUsers = User::model()->findAll($criteria);
					$mailer = new Mailer();
					foreach($subscribedUsers as $subscribed)
						$mailer->AddBCC($subscribed->email);

					$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);
					$mailer->Subject=__('New comment at').': '.$enquiry->title;

					$mailer->Body='	<p>'.__('A new comment has been added to the enquiry').' "'.$enquiry->title.'"<br />
									<a href="'.Yii::app()->createAbsoluteUrl('enquiry/view', array('id' => $enquiry->id)).'">'.
									Yii::app()->createAbsoluteUrl('enquiry/view', array('id' => $enquiry->id)).'</a></p><p><i>'.
									$model->body.'</i></p><p>'.__('Kind regards').',<br />'.
									Config::model()->getObservatoryName().'</p>';
					$mailer->send();
				}
			}else
				echo 0;
		}else
			echo 0;
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();

		$model=$this->loadModel($id);
		$user_id = Yii::app()->user->getUserID();
		$isModerator = $model->isModerator($user_id);
		if($model->user == $user_id || $isModerator){
			if($isModerator && $model->user != $user_id){
				if($enquiry = $model->belongsToEnquiry())
					Log::model()->write('Enquiry', __('Enquiry').' id='.$enquiry->id.' '.__('Team member deleted user comment'));
				else
					Log::model()->write('Comment', __($model->model).' id='.$model->model_id.' '.__('Team member deleted user comment'));
			}
			$model->delete();
			echo 1;
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Comment');

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Comment('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Comment']))
			$model->attributes=$_GET['Comment'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Comment the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Comment::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Comment $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

