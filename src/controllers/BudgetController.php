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

class BudgetController extends Controller
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
			'postOnly + delete, restoreBudgets, delTree, deleteYearsBudgets', // we only allow deletion via POST request
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
				'actions'=>array('index','graph','view','getPieData','getChildBars','getBudgetDetailsForBar',
									'getBudget','getAnualComparison'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('getBudgetDetails'),
				'expression'=>"Yii::app()->user->isTeamMember()",
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(	'getTotalYearlyBudgets','admin',
									'createYear','updateYear',
									'featured','feature','changeWeight',
									'deleteYearsBudgets',
									'deleteTree','delTree',
									'delete','dumpBudgets','restoreBudgets',
									'noDescriptions'),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionGetTotalYearlyBudgets($id)
	{
		$model=$this->loadModel($id);
		echo $model()->getYearsBudgetCount();
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->layout='//layouts/column1';

		$model=$this->loadModel($id);
		$this->pageTitle=CHtml::encode(__('Budget').': '.$model->title);
		$this->render('view',array(
			'model'=>$model,
		));
	}

	public function actionGetBudget($id)
	{
		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->end();

		$model=$this->loadModel($id);
		if($model)
			echo $this->renderPartial('view',array('model'=>$model),true,true);
		else
			echo 0;
	}

	public function actionGetBudgetDetailsForBar($id)
	{
		//if(!Yii::app()->request->isAjaxRequest)
		//	Yii::app()->end();

		$model=$this->loadModel($id);
		if($model){
			echo '<div class="budget_details" budget_id='.$model->id.' style="width:543px;padding:0px">';
			echo $this->renderPartial('_enquiryView',array(	'model'=>$model,
															'showCreateEnquiry'=>1,
															'showLinks'=>1),false,true);
			echo '</div>';
		}else
			echo 0;
	}

	public function actionGetChildBars($id)
	{
		$model=$this->loadModel($id);
		$this->renderPartial('childBars', array('model'=>$model,
												'indent'=>(int)$_GET['indent'],
												'globals'=>$_GET['globals']),
												false,true);
	}

	public function actionGetPieData($id)
	{
		if(isset($_GET['rootBudget_id']))
			$rootBudget_id = (int)$_GET['rootBudget_id'];
		else
			$rootBudget_id = $id;

		if( !(is_numeric($id) && is_numeric($rootBudget_id)) ){
				echo 0;
				Yii::app()->end();
		}

		$model=$this->loadModel($id);
		$graphThisModel=$model;
		$goBackID=$model->parent0->id;
		$isParent=1;
		$hideConcept=1;
		if(!$model->budgets){
			$isParent=0;
			$graphThisModel=$model->parent0;
			$goBackID=$model->parent0->parent0->id;
			$hideConcept=Null;
		}//else
			//$model->orderChildBudgets();
			
		if(!$model->parent0->parent)
			$goBackID = Null;

		if($model->id == $rootBudget_id)
			$goBackID = Null;
		if(($model->parent0->id == $rootBudget_id) && !$isParent)
			$goBackID = Null;

		$params=array(	'parent_id'=>$model->parent,
						'title'=>$graphThisModel->getConcept(),
						'budget_details'=>	'<div class="budget_details" style="padding:0px">'.
											$this->renderPartial('_budgetDetails',array('model'=>$model,
																						//'showCreateEnquiry'=>1,
																						'showLinks'=>1,
																						'hideConcept'=>$hideConcept),
																						true,false).
											'</div>',
						'is_parent'=>$isParent,
						'go_back_id'=>$goBackID,
						'actual_provision'=>(int)$graphThisModel->actual_provision,
					);
		$data=array();
		$childBudgets=$graphThisModel->getChildBudgets();
		foreach($childBudgets as $budget){
			$data[] = array(
							'<span class="link legend_item" budget_id="'.$budget->id.'">'.$budget->code.'. '.$budget->getConcept().'</span>',
							(int)$budget->actual_provision,
							$budget->id,
							format_number($budget->actual_provision),
						);
		}
		$result=array('data'=>$data, 'params'=>$params,);

		if(isset($_GET['callback']))
		//if(Yii::app()->request->isAjaxRequest)
			echo $_GET['callback'] . '('. CJavaScript::jsonEncode($result) .')';
		else
			return CJavaScript::jsonEncode($result);
	}

	/**
	 * team_member uses this to change enquiry type.
	 */
	public function actionGetBudgetDetails($id)
	{
		if(!Yii::app()->request->isAjaxRequest)
		  Yii::app()->end();

 		$model=$this->loadModel($id);
		if($model){
			echo CJavaScript::jsonEncode($this->renderPartial('//enquiry/_budgetDetails',array('model'=>$model),true,true));
		}else
			echo 0;
	}

	public function actionGetAnualComparison($id)
	{
		$model=$this->loadModel($id);
		if($model){
			echo CJavaScript::jsonEncode($this->renderPartial(	'_compareYears',
																array(	'model'=>$model,
																		'budgets'=>$model->getAllBudgetsWithCSV_ID()),
																		true,true)
															);
		}else
			echo 0;
	}

	public function actionCreateYear()
	{
		$model=new Budget;
		$model->scenario = 'newYear';
		$model->initial_provision = '';	// we use this to story the city's population
		$model->actual_provision = 0;
		$model->trimester_1 = 0;
		$model->trimester_2 = 0;
		$model->trimester_3 = 0;
		$model->trimester_4 = 0;
		$model->concept = 'Root budget';
		$model->code = 0;	// 0 = not published, 1 = published

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Budget']))
		{
			$model->attributes=$_POST['Budget'];
			if($model->save()){
				Log::model()->write('Budget','Year '.$model->year.' created');
				$this->redirect(array('admin'));
			}
		}
		$this->render('createYear',array(
			'model'=>$model,
		));
	}

	/**
	 * List budgets without corresponding budgetDescription.
	 */
	public function actionNoDescriptions()
	{
		$model=new Budget('budgetsWithoutDescription');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Budget']))
			$model->attributes=$_GET['Budget'];

		$this->render('noDescriptions',array(
			'model'=>$model,
		));
	}

	public function actionUpdateYear($id)
	{
		$model=$this->loadModel($id);
		Yii::app()->request->cookies['year'] = new CHttpCookie('year', $model->year);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Budget']))
		{
			$wasPublished = $model->code;
			$model->attributes=$_POST['Budget'];
			if ($model->save()){
				if($wasPublished != $model->code){
					Config::model()->isZipFileUpdated(0);
					if($model->code == 0)
						$word = __('unpublished');
					else
						$word = __('published');
					Log::model()->write('Budget', __('Year').' '.$model->year.' '.$word);

					// update default year
					$defaultYear = Config::model()->findByPk('year');
					if ($years = $model->getPublicYears()){
						if ($defaultYear->value != $years[0]->year){
							$defaultYear->value = $years[0]->year;	// most recent published year
							$defaultYear->save();
						}
					}
				}
				$this->redirect(array('admin'));
			}
		}

		$criteria = array(
			'with'=>array('budget0'),
			'condition'=>' budget0.year = :year',
			'together'=>true,
			'params'=>array(':year'=>$model->year)
		);
		$enquirys = new CActiveDataProvider(Enquiry::model(), array('criteria'=>$criteria,));

		$this->render('updateYear',array(
			'model'=>$model,'enquirys'=>$enquirys,));
	}

	public function actionFeatured($id)
	{
		$model=new Budget('featuredSearch');
		$model->unsetAttributes();  // clear any default values
		if (isset($id)){
			if (!Budget::model()->findByAttributes(array('year'=>(int)$id), array('condition'=>'parent IS NOT NULL'))){
				throw new CHttpException(404,'The requested page does not exist.');
			}else{
				$model->year=(int)$id;
			}
		}else{
			$model->year=Config::model()->findByPk('year')->value;
		}
		if(isset($_GET['Budget']))
			$model->attributes=$_GET['Budget'];

		$this->render('featured',array(
			'model'=>$model,
		));
	}

	public function actionFeature($id)
	{
		$model = $this->loadModel($id);
		// we don't show graphs of budgets that don't have children
		if(!$model->budgets){
			echo 1;
			return;
		}
		if($model->featured){
			$model->featured=0;
			$model->weight=0;
			$model->save();
		}
		else{
			$model->featured=1;
			$model->weight=0;
			$model->save();
		}
		$model->refreshFeaturedWeights();
		echo 1;
	}

	public function actionChangeWeight($id)
	{
		$model = $this->loadModel($id);
		$featuredBudgets = $model->getFeatured();

		$highest = $featuredBudgets[0]->weight;
		$lowest = $featuredBudgets[count($featuredBudgets)-1]->weight;
		if($model->weight == $highest && (int)$_GET['increment'] == 1){
			echo 1;
			Yii::app()->end();
		}
		if($model->weight == $lowest && (int)$_GET['increment'] == -1){
			echo 1;
			Yii::app()->end();
		}
		$newWeight = $model->weight + (int)$_GET['increment'];
		if($swap = $model->findByAttributes(array('year'=>$model->year, 'featured'=>1, 'weight'=>$newWeight))){
				$swap->weight = $swap->weight + ((int)$_GET['increment'] * -1);
				$swap->save();
		}
		$model->weight = $newWeight;
		$model->save();
		echo 1;
	}

	/*
	 * Show a grid of budgets
	 * 
	 */
	public function actionDeleteTree($id)
	{
		$model=new Budget('deleteTreeSearch');
		$model->unsetAttributes();  // clear any default values
		if(isset($id))
			$model->year=$id;
		else
			$model->year=Config::model()->findByPk('year')->value;
		if(isset($_GET['Budget']))
			$model->attributes=$_GET['Budget'];

		$this->render('deleteTree',array(
			'model'=>$model,
		));
	}

	/* 
	 * delete selected budget
	 */
	public function actionDelTree($id)
	{
		$model = $this->loadModel($id);
		
		$year = $model->year;
		$csv_id = $model->csv_id;
		$budgetCount = 0;

		$sql_params = array(
			':csv_id'=>"$csv_id%",
			':year'=>$year,
		);

		$sql = "SELECT budget.id, budget.year, budget.csv_id, enquiry.budget
				FROM budget
				INNER JOIN enquiry
				ON budget.id=enquiry.budget
				WHERE budget.year = :year
				AND budget.csv_id LIKE :csv_id";

		$cnt = "SELECT COUNT(*) FROM ($sql) subq";
		$enquiryCount = Yii::app()->db->createCommand($cnt)->bindValues($sql_params)->queryScalar();

		if(!$enquiryCount){
			$sql = "SELECT budget.id
					FROM budget  WHERE year = :year
					AND parent IS NOT NULL
					AND budget.csv_id LIKE :csv_id";

			$cnt = "SELECT COUNT(*) FROM ($sql) subq";				
			$budgetCount = Yii::app()->db->createCommand($cnt)->bindValues($sql_params)->queryScalar();

			$sql = "DELETE FROM budget  WHERE year = :year
										AND parent IS NOT NULL
										AND budget.csv_id LIKE :csv_id
										ORDER BY id DESC;";
										
			Yii::app()->db->createCommand($sql)->bindValues($sql_params)->execute();

			if ($model->isPublished()){
				Config::model()->isZipFileUpdated(0);
			}
			Log::model()->write('Budget', 'Year '.$model->year.'. Budget "'.$model->csv_id.'" deleted');
		}
		echo CJavaScript::jsonEncode(array('totalBudgets'=>$budgetCount, 'totalEnquiries'=>$enquiryCount));
	}

	public function actionAdmin()
	{
		$years=new CActiveDataProvider('Budget',array('criteria'=>array('condition'=>'parent IS NULL', 'order'=>'year DESC')));
		$this->render('admin',array('years'=>$years,));
	}

	/*
	 * Delete all the budgets in the year
	 */
	public function actionDeleteYearsBudgets($id)
	{
		$model = $this->loadModel($id);

		if ($model->getYearsTotalEnquiries() == 0){
			$year = $model->year;
			$sql = "DELETE FROM budget WHERE year = '$year' AND parent IS NOT NULL ORDER BY id DESC;";
			Yii::app()->db->createCommand($sql)	->execute();
			
			Log::model()->write('Budget', 'Year '.$year.'. '.__('All budgets deleted'));
		}
		$this->redirect(array('updateYear','id'=>$model->id));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model=$this->loadModel($id);
		$root_budget=0;
		if(!$model->parent)
			$root_budget=1;

		if(!($model->findByPk($model->parent) || Enquiry::model()->findByAttributes(array('budget'=>$model->id)))){
			$model->delete();
			if($root_budget){
				Yii::app()->user->setFlash('success',__('Year deleted'));
				Log::model()->write('Budget', 'Year '.$model->year.' deleted');
				$this->redirect(array('admin'));
			}
		}
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/column1';
		$this->pageTitle=__('Budgets').' '.Config::model()->findByPk('administrationName')->value;
		$model = new Budget('search');

		if (isset($_GET['display'])){
			$display=$_GET['display'];
			if ($display == 'pie' || $display == 'bar'){
				Yii::app()->request->cookies['display'] = new CHttpCookie('display', $display);
			}
		}
		elseif (isset(Yii::app()->request->cookies['display'])){
			$display=Yii::app()->request->cookies['display']->value;
		}else{
			$display='pie';
		}
		
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Budget'])) {
			$model->setScenario('search');
			$model->attributes = $_GET['Budget'];
		}

		if (isset($_GET['year'])){
			if (strtotime($_GET['year']) !== false){
				$model->year = (int)$_GET['year'];
			}else{
				$model->year = Config::model()->findByPk('year')->value;
			}
			Yii::app()->request->cookies['year'] = new CHttpCookie('year', $model->year);
		}
		elseif (isset(Yii::app()->request->cookies['year']) && strtotime(Yii::app()->request->cookies['year']->value) !== false){
			$model->year = Yii::app()->request->cookies['year']->value;
		}else{
			$model->year = Config::model()->findByPk('year')->value;
		}
		if (isset($_GET['featuredFilter'])){
			$model->featuredFilter = $_GET['featuredFilter'];
		}

		if ($display == 'modified'){
			$modifiedDataProvider = $model->modifiedSearch();
		}else{
			$modifiedDataProvider = Null;
		}

		$this->render('index', array(
			'model' => $model,
			'display' => $display,
			'modifiedDataProvider' => $modifiedDataProvider,
		));
	}

	/**
	 * Show the graph for one particular budget.
	 */
	public function actionGraph($id)
	{
		$this->layout='//layouts/column1';
		$this->pageTitle=__('Budgets').' '.Config::model()->findByPk('administrationName')->value;
		$root_budget=$this->loadModel($id);

		$model = new Budget('publicSearch');
		$model->year = $root_budget->year;

		if (isset($_GET['display'])){
			$display=$_GET['display'];
			Yii::app()->request->cookies['display'] = new CHttpCookie('display', $display);
		}
		elseif (isset(Yii::app()->request->cookies['display'])){
			$display=Yii::app()->request->cookies['display']->value;
		}else{
			$display='pie';
		}
		if (!($display == 'pie' || $display == 'bar')){
			$display = 'pie';
		}
		$this->render('index', array(
			'model' => $model,
			'root_budget' => $root_budget,
			'display' => $display,
			'showModifiedAlert' => false,
		));
	}

	/**
	 * Dump the budget table
	 */
	public function actionDumpBudgets()
	{
		echo Budget::model()->dumpBudgets();
	}

	/**
	 * Restore the budget table
	 */
	public function actionRestoreBudgets($id)
	{
		$result = Budget::model()->restoreBudgets($id);
		if($result === true){
			Yii::app()->user->setFlash('success',__('Database restored correctly'));
			Log::model()->write('Budget', 'Budget table restored');
		}
		return $result;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Budget the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Budget::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Budget $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='budget-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
