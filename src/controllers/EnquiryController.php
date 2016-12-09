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

class EnquiryController extends Controller
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
			'postOnly + delete, megaDelete, deleteSubmittedDocument', // we only allow deletion via POST request
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
				'actions'=>array('view','index','getEnquiry','export','feed'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','edit','subscribe','delete'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('teamView','assigned','validate','changeType',
								 'submit','deleteSubmittedDocument','assess','reformulate'),
				'expression'=>"Yii::app()->user->isTeamMember()",
			),
			array('allow',
				'actions'=>array('adminView','admin','manage'),
				'expression'=>"Yii::app()->user->isManager()",
			),
			array('allow',
				'actions'=>array('getMegaDelete', 'megaDelete'),
				'expression'=>"Yii::app()->user->isManager() || Yii::app()->user->isAdmin()",
			),
			array('allow',
				'actions'=>array('changeBudget'),
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
		$this->layout='//layouts/column1';
		$model=$this->loadModel($id);
		$this->pageTitle=__('Enquiry').': '.$model->title;

		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/column1';
		$this->pageTitle=__('Enquiries').' '.Config::model()->findByPk('administrationName')->value;
		$model=new Enquiry('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Enquiry']))
			$model->attributes=$_GET['Enquiry'];

		if(isset($_GET['display'])){
			$displayType = $_GET['display'];
			Yii::app()->request->cookies['enquiry_display_type'] = new CHttpCookie('enquiry_display_type', $displayType);
		}
		elseif(isset(Yii::app()->request->cookies['enquiry_display_type']))
			$displayType=Yii::app()->request->cookies['enquiry_display_type']->value;
		else
			$displayType='list';
			
		$this->render('index',array(
			'displayType'=>$displayType,
			'dataProvider'=>$model->publicSearch(),
			'model'=>$model,
		));
	}

	public function actionGetEnquiry($id)
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();

		$model=$this->loadModel($id);
		if($model){
			echo CJavaScript::jsonEncode(array('html'=>$this->renderPartial('view',array('model'=>$model),true,true)));
		}else
			echo 0;
	}

	public function actionSubscribe()
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();

		if(isset($_POST['enquiry']) && isset($_POST['subscribe']))
		{
			$user = Yii::app()->user->getUserID();
			$criteria = new CDbCriteria;
			$criteria->condition = 'enquiry = :enquiry AND user = :user';
			$criteria->params[':enquiry'] = $_POST['enquiry'];
			$criteria->params[':user'] = $user;
						
			$model=EnquirySubscribe::model()->find($criteria);
			
			if($model && $_POST['subscribe']=='false'){
				$model->delete();
				echo '-1';
			}elseif ($model && $_POST['subscribe']=='true'){
				// do nothing. user probably just made a comment and we automatically subscribed him
				echo '0';
			}else{
				$model=new EnquirySubscribe;
				$model->enquiry = $_POST['enquiry'];	// should check if enquiry id is valid.
				$model->user = $user;
				$model->save();
				echo '1';
			}
			Yii::app()->end();
		}
		echo '0';
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->layout='//layouts/column1';
		$this->pageTitle=CHtml::encode(__('New enquiry'));
		$model=new Enquiry;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_GET['budget'])){
			$model->budget=$_GET['budget'];
			$model->type = 1;
			
			if($budget=Budget::model()->findByPk($model->budget)){
				if(Config::model()->findByPk('year')->value != $budget->year){
					$msg = __('This budget is from the year %s.').'. '.__('Do you want to continue?');
					$msg = str_replace('%s', $budget->year, $msg);
					Yii::app()->user->setFlash('prompt_year', $msg );
				}
			}else
				Yii::app()->end();
		}
		$model->addressed_to = ADMINISTRATION;
		
		if(isset($_POST['Enquiry']))
		{
			$model->setScenario('create');
			$model->attributes=$_POST['Enquiry'];
			$model->user = Yii::app()->user->getUserID();
			$model->created = date('Y-m-d');
			$model->modified = date('c');
			$model->state = ENQUIRY_PENDING_VALIDATION;
			$model->title = htmLawed::hl($model->title, array('elements'=>'-*', 'keep_bad'=>0));
			$model->body = htmLawed::hl($model->body, array('safe'=>1, 'deny_attribute'=>'script, class, id'));

			if($model->save()){
				$description = new EnquiryText;
				$description->enquiry=$model->id;
				$description->title=$model->title;
				$description->body=trim(strip_tags(str_replace("<br />", " ", $model->body)));
				$description->save();
				
				$subscription=new EnquirySubscribe;
				$subscription->user = $model->user;
				$subscription->enquiry = $model->id;
				$subscription->save();

 				$mailer = new Mailer();

				$mailer->AddAddress($model->user0->email);
				$recipients=$model->user0->email.',';


				$managers=User::model()->findAllByAttributes(array('is_manager'=>'1'));
				foreach($managers as $manager){
					$mailer->AddBCC($manager->email);
					$recipients=$recipients.' '.$manager->email.',';
				}

				$recipients = substr_replace($recipients ,'',-1);

				$mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);
				$mailer->Subject=$model->getHumanStates($model->state);
				$mailer->Body=EmailTemplate::model()->findByPk($model->state)->getBody($model);

				$email = new Email;

				$email->created = date('c');
				$email->sender=Null;	//app generated email
				$email->sent_as=Config::model()->findByPk('emailNoReply')->value;
				$email->title=$mailer->Subject;
				$email->body=$mailer->Body;
				$email->recipients=$recipients;
				$email->enquiry=$model->id;

				Log::model()->write('Enquiry', __('New enquiry').'. id='.$model->id, $model->id);

				if($mailer->send()){
					$email->sent=1;
					$email->save();
					Yii::app()->user->setFlash('success', __('We have sent you an email'));
				}else{
					$email->sent=0;
					$email->save();
					Yii::app()->user->setFlash('success', __('Your enquiry has been registered correctly'));
				}
				$this->redirect(array('/user/panel'));
			}
		}
		$this->render('create',array(
			'model'=>$model,
		));
	}

	/*
	 * Only the assigned team_member can reformulate an enquiry
	*/
	public function actionReformulate()
	{
		$this->pageTitle=CHtml::encode(__('Reformulate enquiry'));
		$model=new Enquiry;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_GET['related'])){
			$model->related_to=$_GET['related'];
			$related_enquiry=Enquiry::model()->findByPk($model->related_to);
			if ($related_enquiry===null){
				throw new CHttpException(404,'The requested Related_enquiry does not exist.');
			}
			if(Yii::app()->user->getUserID() != $related_enquiry->team_member)
				$this->redirect(array('/site/index'));
				
			if($related_enquiry->budget){
				$model->budget=$related_enquiry->budget;
				$model->type = $related_enquiry->type;
			}
		}else
			$this->redirect(array('/site/index'));

		if(isset($_POST['Enquiry']))
		{
			$model->attributes=$_POST['Enquiry'];
			$model->user = Yii::app()->user->getUserID();
			$model->created = date('Y-m-d');
			$model->state = ENQUIRY_ACCEPTED;
			$model->title = htmLawed::hl($model->title, array('elements'=>'-*', 'keep_bad'=>0));
			$model->body = htmLawed::hl($model->body, array('safe'=>1, 'deny_attribute'=>'script, class, id'));

			$model->team_member=$related_enquiry->team_member;
			$model->assigned=date('Y-m-d');
			$model->modified = date('c');
			$model->state=ENQUIRY_ACCEPTED;

			if($model->save()){
				$description = new EnquiryText;
				$description->enquiry=$model->id;
				$description->title=$model->title;
				$description->body=trim(strip_tags(str_replace("<br />", " ", $model->body)));
				$description->save();
				
				// subscribe users to this new enquiry
				if ($model->relatedTo){
					foreach($model->relatedTo->subscriptions as $old_subscription){
						$subscription=new EnquirySubscribe;
						$subscription->user = $old_subscription->user;
						$subscription->enquiry = $model->id;
						$subscription->save();					
					}
				}
				Yii::app()->user->setFlash('success', __('New reformulated enquiry created OK'));
				Log::model()->write('Enquiry', __('New enquiry').'. id='.$model->id, $model->id);		
				Log::model()->write('Enquiry', __('Enquiry').' id='.$model->id.' '.__('assigned to team member').' '.$model->teamMember->username, $model->id);
				$this->redirect(array('teamView','id'=>$model->id));
			}
		}
		$this->render('reformulate',array(
			'model'=>$model,
		));	
	
	}


	/**
	 * If save is successful, the browser will be redirected to the 'view' page.
	 * team_memeber edits a $model->body and $model->type.
	 */
	public function actionEdit($id)
	{
		$this->pageTitle=CHtml::encode(__('Modify enquiry'));
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$userid = Yii::app()->user->getUserID();
		
		if(!($userid == $model->user || $userid == $model->team_member)){
			$this->redirect(array('/site/index'));
			Yii::app()->end();
		}		
		
		if($model->state > ENQUIRY_PENDING_VALIDATION && $userid != $model->team_member){
			$this->redirect(array('/user/panel'));
			Yii::app()->end();
		}
		
		if(isset($_POST['Enquiry']))
		{
			$model->setScenario('edit');
			$model->attributes=$_POST['Enquiry'];
			
			if($model->addressed_to == OBSERVATORY)
				$model->addressToObservatory();
			
			$model->title = htmLawed::hl($model->title, array('elements'=>'-*', 'keep_bad'=>0));
			$model->body = htmLawed::hl($model->body, array('safe'=>1, 'deny_attribute'=>'script, class, id'));
			if($model->save()){
				$description=EnquiryText::model()->findByPk($model->id);
				$description->title=$model->title;
				$description->body= trim(strip_tags(str_replace("<br />", " ", $model->body)));
				$description->save();
								
				if($userid == $model->team_member){
					$this->redirect(array('teamView','id'=>$model->id));
				}else{
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}
		if($model->team_member == $userid)
			$this->render('teamEdit',array('model'=>$model));
			
		elseif($model->user == $userid){
			$this->layout='//layouts/column1';
			$this->render('edit',array('model'=>$model));
		}	
	}

	/**
	 * Team member can change Generic, Budgetary, budget->id
	 */
	public function actionChangeType($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if($model->team_member != Yii::app()->user->getUserID()){
			$this->redirect(array('index'));
		}

		if(isset($_POST['Enquiry']))
		{
			if ($model->budget){
				$preUpdateBudget = Budget::model()->findByPk($model->budget);
				if ($preUpdateBudget===null){
					throw new CHttpException(404,'The requested preUpdateBudget does not exist.');
				}
			}else
				$preUpdateBudget = Null;
				
			$model->attributes=$_POST['Enquiry'];
			if($model->type == GENERIC)
				$model->budget = Null;
			if($model->save()){
				if($preUpdateBudget)
					$msg = $preUpdateBudget->year.'.'.$preUpdateBudget->csv_id.' > ';
				else
					$msg = __('generic enquiry').' > ';
				$model->refresh();
				if($model->budget0)
					$msg = $msg.$model->budget0->year.'.'.$model->budget0->csv_id;
				else
					$msg = $msg.__('generic enquiry');
				Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('changed budget').' '.$msg);

				$this->redirect(array('teamView','id'=>$model->id));
			}
		}
		$budget=new Budget('changeTypeSearch');
		$budget->unsetAttributes();  // clear any default values

		if(isset($_GET['Budget']))
			$budget->attributes=$_GET['Budget'];

		$this->render('changeType',array(
			'model'=>$model,
			'budgetModel'=>$budget,
		));
	}

	/*
	 * Used by Admim to change the enquiry->budget->id
	 */
	public function actionChangeBudget($id)
	{
		$model=$this->loadModel($id);
		$budget=new Budget('changeTypeSearch');

		$oldBudget_year = Null;
		if($model->budget0){
			$criteria = new CDbCriteria;
			$criteria->condition = 'parent IS NULL AND year = :year';
			$criteria->params[':year'] = $model->budget0->year;
			$oldBudget_year=$budget->find($criteria);
		}
		if(isset($_POST['Enquiry']))
		{
			$preUpdateBudget = Budget::model()->findByPk($model->budget);
			if ($preUpdateBudget===null){
				throw new CHttpException(404,'The requested preUpdateBudget does not exist.');
			}
			$model->attributes=$_POST['Enquiry'];
			if($model->type == 0)
				$model->budget = Null;
			if($model->save()){
				$msg = $preUpdateBudget->year.'.'.$preUpdateBudget->csv_id.' > ';
				$model->refresh();
				if($model->budget0)
					$msg = $msg.$model->budget0->year.'.'.$model->budget0->csv_id;
				else
					$msg = $msg.__('generic enquiry');

				Yii::app()->user->setFlash('success', __('Enquiry').' '.__('changed budget').' '.$msg);
				Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('changed budget').' '.$msg);
				if($oldBudget_year)
					$this->redirect(array('budget/updateYear','id'=>$oldBudget_year->id));
				else
					$this->redirect(array('budget/admin'));
			}
		}

		$budget->unsetAttributes();  // clear any default values
		if(isset($_GET['Budget']))
			$budget->attributes=$_GET['Budget'];

		$this->render('changeBudget',array(
			'model'=>$model,
			'budgetModel'=>$budget,
		));
	}

	/**
	 * team_member submits the enquiry to the administration
	 */
	public function actionSubmit($id)
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Submit enquiry'));
		$model=$this->loadModel($id);
		$model->scenario = 'submitted_to_council';
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if( $model->team_member != Yii::app()->user->getUserID() || $model->state < ENQUIRY_ACCEPTED){
			$this->render('/user/panel');
			Yii::app()->end();
		}

		if(isset($_POST['Enquiry']))
		{
			if($model->state < ENQUIRY_AWAITING_REPLY)
				$msg = __('submitted to administration');
			else
				$msg = __('submit corrected');
			$model->attributes=$_POST['Enquiry'];

			if($model->validate()){
				if($model->state == ENQUIRY_ACCEPTED){
					$model->state = ENQUIRY_AWAITING_REPLY;
					$model->modified = date('c');
				}
			}
			if(Yii::app()->request->isAjaxRequest){
				//http://www.yiiframework.com/forum/index.php/topic/37075-form-validation-with-ajaxsubmitbutton/
				if($model->save()){
					Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.$msg);
					echo CJSON::encode(array('status'=>'success'));
				}else
					echo CActiveForm::validate($model);
				Yii::app()->end();
			}
			if($model->save()){
				Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.$msg);
				if($model->documentation){
					$model->promptEmail();			
					$this->redirect(array('teamView','id'=>$model->id));
				}
			}
		}
		$this->render('submit',array(
			'model'=>$model,
		));
	}

	/**
	 * team_member deletes documentation
	 */
	public function actionDeleteSubmittedDocument($id)
	{
		$model=$this->loadModel($id);

		if ($model->team_member != Yii::app()->user->getUserID()){
			$this->render('/user/panel');
			Yii::app()->end();
		}
		if ($model->documentation){
			$file=$model->documentation0;
			$model->documentation = Null;
			//if(!$model->state > ENQUIRY_AWAITING_REPLY)
			//	$model->state=ENQUIRY_ACCEPTED;
			$model->save();
			if(!$file->delete()){
				$model->documentation = $file->id;
				$model->save();
			}			
		}
		$this->redirect(array('submit','id'=>$model->id));
	}

	/**
	 * View for team_member.
	 */
	public function actionTeamView($id)
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Manage enquiry'));
		$model=$this->loadModel($id);
		if( $model->team_member == Yii::app()->user->getUserID()){
			if($model->state == ENQUIRY_ASSIGNED)
				$this->redirect(array('validate','id'=>$model->id));
			else
				$this->render('teamView',array('model'=>$model));
		}
		else{
			$this->redirect(array('view','id'=>$model->id));
		}
	}

	/**
	 * Updates a model
	 * All attribs except $body
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionAssess($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Enquiry']))
		{
			$model->attributes=$_POST['Enquiry'];
			$model->modified = date('c');
			if($model->save()){
				$model->promptEmail();
				$this->redirect(array('teamView','id'=>$model->id));
			}
		}
		$this->render('assess',array(
			'model'=>$model,
		));
	}

	public function actionAssigned()
	{
		// grid of enquirys by team_member
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Entrusted enquiries'));
		$this->layout='//layouts/column1';

		$model=new Enquiry('search');
		$model->unsetAttributes();  // clear any default values
		//$model->team_member = Yii::app()->user->getUserID();
		if(isset($_GET['Enquiry']))
			$model->attributes=$_GET['Enquiry'];
		$this->render('assigned',array(
			'model'=>$model,
		));
	}

	/**
	 * Validate a model
	 * Team member accepts or rejects enquiry
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionValidate($id)
	{
		$model=$this->loadModel($id);	
		if(isset($_POST['Enquiry']))
		{
			$model->attributes=$_POST['Enquiry'];
			$model->modified = date('c');

			if($model->addressed_to == OBSERVATORY)
				$model->addressToObservatory();

			if($model->save()){
				$model->promptEmail();
				if($model->state == ENQUIRY_REJECTED && $model->team_member == 	Yii::app()->user->getUserID()){
					// somehow send an email to manager
				}
				if($model->state == ENQUIRY_REJECTED)
					Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('rejected by team member'), $model->id);
				else
					Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('accepted by team member'), $model->id);
				
			}			
		}		
		$this->render('validate',array(
			'model'=>$model,
		));		
	}

	public function actionManage($id)
	{
		$model=$this->loadModel($id);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Enquiry']))
		{
			$saveMe=1;
			$model->manager=Yii::app()->user->getUserID();
			$team_member=$model->team_member;
			$model->attributes=$_POST['Enquiry'];

			if( $model->addressed_to == OBSERVATORY)
				$model->addressToObservatory();

			if($model->state == ENQUIRY_REJECTED){
				$model->assigned = Null;
				$model->team_member = Null;
				Log::model()->write('Enquiry',__('Enquiry').' '.$model->id.' '.__('rejected by team manager'), $model->id);
			}
			elseif(!$model->team_member){
				Yii::app()->user->setFlash('notice', __('You must assign a team member'));
				$model->team_member = $team_member;
				$saveMe=Null;
			}
			elseif($team_member != $model->team_member){
				if($model->team_member){
					$model->assigned=date('Y-m-d');
					$model->modified=date('c');
					if($model->state <= ENQUIRY_REJECTED) // maybe enquiry was already accepted and has higher state.
						$model->state=ENQUIRY_ASSIGNED;
					Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('assigned to team member').' '.User::model()->findByPk($model->team_member)->username, $model->id);
				}else{
					Yii::app()->user->setFlash('notice', __('You must assign a team member'));
					$saveMe=Null;
				}
			}
			if($saveMe && $model->save()){
				if($model->team_member){
					if(!EnquirySubscribe::model()->findByAttributes(array('enquiry'=>$model->id, 'user'=>$model->team_member))){
						$subscription=new EnquirySubscribe;
						$subscription->user = $model->team_member;
						$subscription->enquiry = $model->id;
						$subscription->save();
					}
				}
				if($model->state == ENQUIRY_ASSIGNED || $model->state == ENQUIRY_REJECTED)
					$model->promptEmail();
				else{
					Yii::app()->user->setFlash('success', __('New team member assigned'));
					//$this->redirect(array('adminView','id'=>$model->id));
				}
			}//else
			//	$model=$this->loadModel($id);	// render an unchanged model.
		}
		$team_members = User::model()->getTeamMembers();
		$this->render('manage',array(
			'model'=>$model,
			'team_members'=>$team_members,
		));
	}

	public function actionAdminView($id)
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Manage enquiry'));
		$model=$this->loadModel($id);
		if($model->state == ENQUIRY_PENDING_VALIDATION)
			$this->redirect(array('manage','id'=>$model->id));
		if($model->state == ENQUIRY_REJECTED && $model->team_member != Null)
			$this->redirect(array('manage','id'=>$model->id));
		
		$this->render('adminView',array('model'=>$model));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->pageTitle=CHtml::encode(Config::model()->findByPk('siglas')->value.' '.__('Manage enquiries'));
		$this->layout='//layouts/column1';
		$model=new Enquiry('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Enquiry']))
			$model->attributes=$_GET['Enquiry'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/*
	 * Export the enquiry in PDF 
	 */
	public function actionExport($id)
	{
		$model=$this->loadModel($id);
		$tmpDir = createTempDirectory();

		$enquiryFile = File::model()->findByAttributes(array('model'=>'Enquiry','model_id'=>$model->id));
		if ($enquiryFile){
			copy($enquiryFile->getURI(), $tmpDir.__('Enquiry').'.'.$enquiryFile->getExtension());
		}
		if ($model->replys){
			$replyFiles = File::model()->findAllByAttributes(array('model'=>'Reply','model_id'=>$model->replys[0]->id));
			foreach($replyFiles as $file){
				copy($file->getURI(), $tmpDir.'/'.string2ascii($file->name).'.'.$file->getExtension());
			}
		}else{
			$replyFiles = Null;
		}

		// Create PDF
		Yii::import('application.extensions.html2pdf.*');
		require_once('html2pdf.class.php');
		
		ob_start();
		$this->renderPartial('pdf',array('model'=>$model),false,true);
		$content = ob_get_clean();

		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', getDefaultLanguage(), true, 'UTF-8', array(0, 0, 0, 0));
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			
			if(! ($enquiryFile || $replyFiles) ) {
				// Create and download a single pdf file
				$tmp_name = substr(md5(rand(0, 1000000)), 0, 45);
				$html2pdf->Output($tmpDir.$tmp_name.'.pdf', 'F');

				ignore_user_abort(true);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header("Content-type:application/pdf");
				header("Content-Disposition: attachment; filename=\"" . $model->title.'.pdf' . "\"");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: " . filesize($tmpDir.$tmp_name.'.pdf'));
				ob_end_flush();
				@readfile($tmpDir.$tmp_name.'.pdf');

				deleteTempDirectory($tmpDir);
				Yii::app()->end();
			}else{
				$html2pdf->Output($tmpDir.string2ascii($model->title).'.pdf', 'F');
			}
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}

		// Create Zip
		$tmpZipfile = createTempFile();
		$zip = new ZipArchive();

		$zip->open($tmpZipfile, ZIPARCHIVE::CREATE );
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpDir), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file){
			
			$file = str_replace('\\', '/', $file);
			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
				continue;
			$zip->addFromString(str_replace($tmpDir, '', $file), file_get_contents($file));
		}
		$zip->close();
		ignore_user_abort(true);

		$zip_name = $model->title.'.zip';

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=\"" . $zip_name . "\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($tmpZipfile));
		ob_end_flush();
		@readfile($tmpZipfile);

		deleteTempDirectory($tmpDir);
		unlink($tmpZipfile);
		Yii::app()->end();
	}


	public function actionFeed()
	{
		Yii::import('application.vendors.*');
		require_once 'Zend/Loader/Autoloader.php';
		spl_autoload_unregister(array('YiiBase','autoload')); 
		spl_autoload_register(array('Zend_Loader_Autoloader','autoload')); 
		spl_autoload_register(array('YiiBase','autoload'));

		$enquiries = Enquiry::model()->getEnquiriesForRSS();
		// convert to the format needed by Zend_Feed
		$entries=array();
		foreach($enquiries as $enquiry)
		{
			$date = new DateTime($enquiry->created);
			$entries[]=array(
				'title'=>$enquiry->title,
				'link'=>Yii::app()->createAbsoluteUrl('enquiry/view',array('id'=>$enquiry->id)),
				'description'=>$enquiry->body,
				'lastUpdate'=>$date->getTimestamp(),
			);
		}
		// generate and render RSS feed
		$feed=Zend_Feed::importArray(array(
			'title'			=> Config::model()->findByPk('siglas')->value.' '.__('Enquiries'),
			'description'	=> Config::model()-> getObservatoryName(),
			'link'			=> Yii::app()->createUrl('enquiry'),
			'image'			=> Yii::app()->createAbsoluteUrl('files/logo.png'),
			'charset'		=> 'UTF-8',
			'entries'		=> $entries,
			),
			'rss'
		);
		$feed->send();  
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id);
		$user=Yii::app()->user->getUserID();
		if($model->state==ENQUIRY_PENDING_VALIDATION && ($model->user == $user || Yii::app()->user->isManager()) ){
			if($model->user == $user)
				Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('deleted'), $model->id);
			else
				Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('deleted by team manager'), $model->id);
			$model->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax'])){
				Yii::app()->user->setFlash('success', __('Enquiry has been deleted'));				
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		}
		$this->redirect(array('/site/index'));
	}

	/**
	 * Deletes a enquiry and all references.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionGetMegaDelete($id)
	{
		$model = $this->loadModel($id);
		$object_count = $model->countObjects();
		echo $this->renderPartial('_megaDelete',array('model'=>$model,'object_count'=>$object_count),true,true);
	}

	public function actionMegaDelete($id)
	{
		$model=$this->loadModel($id);
		Log::model()->write('Enquiry',__('Enquiry').' id='.$model->id.' '.__('deleted by team manager'), $model->id);
		$model->delete();
		Yii::app()->user->setFlash('success', __('Enquiry has been deleted'));
		echo $id;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Enquiry::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='enquiry-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
