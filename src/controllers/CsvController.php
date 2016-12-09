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


// Read this http://www.php.net/manual/en/function.fgetcsv.php
class CsvController extends Controller
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
				'actions'=>array('importCSV','uploadCSV','checkCSVFormat',
				'addMissingValues','checkCSVTotals', 'importCSVData',
				'export','showYears','regenerateCSV',/*'updateCommonDescriptions',*/
				'downloadUpdatedCSV'/*,'createCommonDescriptions'*/),
				'expression'=>"Yii::app()->user->isAdmin()",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	/* create a cgrid of available years
	 * used to include year.csv in the zip file
	 */
	public function actionShowYears()
	{
		$dataProvider =new CActiveDataProvider('Budget',array(
			'criteria'=>array('condition'=>'parent IS NULL',
			'order'=>'year DESC'),
		));
		echo $this->renderPartial('yearsForZip',array('dataProvider'=>$dataProvider),false,true);
	}

	/*
	 * generate a CSV taking data from the database
	 */
	public function actionRegenerateCSV($id)
	{
		$model = new ImportCSV;
		if($model->createCSV($id))
			echo 1;
		else
			echo 0;
	}

	public function actionImportCSV($id)
	{
		$model = new ImportCSV;
		$model->year=$id;
		$this->render('importCSV', array('model'=>$model));
	}

	public function actionUploadCSV($id)
	{
		$model = new ImportCSV;
		$model->year = $id;
		if(isset($_POST['ImportCSV']))
		{
			$model->attributes=$_POST['ImportCSV'];

			$model->csv=CUploadedFile::getInstance($model,'csv');
			$filename = $model->getTmpCSVFilename();

			$model->csv->saveAs($model->path.$filename);
			$model->csv = $filename;
			$model->step = 2;
		}
		$this->render('importCSV', array('model'=>$model));
	}

	/* 
	 * Make some preliminary checks
	 */
	public function actionCheckCSVFormat()
	{
		if(!isset($_GET['csv_file'])){
			echo CJavaScript::jsonEncode(array('error'=>'CSV file path not defined.'));	
			Yii::app()->end();
		}
		$model = new ImportCSV;
		$model->csv = $model->path.$_GET['csv_file'];
		
		if(!$model->checkEncoding()){
			echo CJavaScript::jsonEncode(array('error'=>'This file has not been saved as UTF-8'));
			Yii::app()->end();
		}
		list($total_registers, $error) = $model->checkCSVFormat();
		if(!$error)
			$error = $model->checkInternalCodeSanity();	// rewrites the csv if needed
			
		if(!$error){
			$model->orderCSV();	
			echo count($total_registers) - 1;	// -1 to remove the first header line
		}else
			echo CJavaScript::jsonEncode(array('error'=>$error));
	}

	/*
	 * Complete the CSV with missing registers, concepts, and totals
	 */
	public function actionAddMissingValues()
	{
		if(!isset($_GET['csv_file'])){
			echo CJavaScript::jsonEncode(array('error'=>'CSV file path not defined.'));
			Yii::app()->end();
		}
		$model = new ImportCSV;
		$model->csv = $model->path.$_GET['csv_file'];
		
		$msg=Null;
		
		$newRegisterCnt = $model->addMissingRegisters();	// rewrites the csv if needed

		if($newRegisterCnt > 0)
			$msg = '<br /><span class="warn">New registers added: '.$newRegisterCnt.'</span>';
			
		$new_concepts = $model->addMissingConcepts();		// rewrites the csv if needed
		if($new_concepts)
			$msg = $msg.'<br /><span class="warn">Codes/concepts added: '.$new_concepts.'</span>';
		
		list($initial,$actual,$t1,$t2,$t3,$t4) = $model->addMissingTotals();	// rewrites the csv if needed
		$total_newTotals = $initial+$actual+$t1+$t2+$t3+$t4;
		if($total_newTotals){
			$msg = $msg.'<br /><span class="warn">Missing totals: '.$total_newTotals.'</span>';
			if($initial)
				$msg = $msg.'<br /><span class="warn">- initial_provision: '.$initial.'</span>';
			if($actual)
				$msg = $msg.'<br /><span class="warn">- actual_provision: '.$actual.'</span>';
			if($t1)
				$msg = $msg.'<br /><span class="warn">- trimester_1: '.$t1.'</span>';
			if($t2)
				$msg = $msg.'<br /><span class="warn">- trimester_2: '.$t2.'</span>';
			if($t3)
				$msg = $msg.'<br /><span class="warn">- trimester_3: '.$t3.'</span>';
			if($t4)
				$msg = $msg.'<br /><span class="warn">- trimester_4: '.$t4.'</span>';
			$msg = $msg.'<br />';
		}
		if(!$msg){
			$msg='No missing values';
		}
		echo CJavaScript::jsonEncode(array(	'updated'=>$newRegisterCnt,
											'new_totals'=>$total_newTotals,
											'new_concepts'=>$new_concepts,
											'msg'=>$msg,
										));		
	}

	public function actionCheckCSVTotals()
	{
		if(!isset($_GET['csv_file'])){
			echo CJavaScript::jsonEncode(array('error'=>'CSV file path not defined.'));	
			Yii::app()->end();
		}
		$model = new ImportCSV;
		$model->csv = $model->path.$_GET['csv_file'];
		$registers = $model->csv2array();
		
		$lines = file($model->csv);
		$ids = array();
		foreach ($lines as $line_num => $line) {
			if($line_num==0)
				continue;
			list($id, $code, $initial_prov, $actual_prov, $t1, $t2, $t3, $t4, $label, $concept) = explode("|", $line);
			$id = trim($id);
			$parent_id=$model->getParentCode($id);
			$ids[$id]=array();

			$initial_prov = str_replace('€', '', $initial_prov);
			$initial_prov = (float)trim(str_replace(',', '', $initial_prov));
			$actual_prov = str_replace('€', '', $actual_prov);
			$actual_prov = (float)trim(str_replace(',', '', $actual_prov));

			$ids[$id]['internal_code']=$id;
			$ids[$id]['initial_total']=$initial_prov;
			$ids[$id]['actual_total']=$actual_prov;
			$ids[$id]['children']=array();
			if(array_key_exists($parent_id, $ids)){
				$ids[$parent_id]['children'][$id]=array();
				$ids[$parent_id]['children'][$id]['id']=$id;
				$ids[$parent_id]['children'][$id]['initial_prov']=$initial_prov;
				$ids[$parent_id]['children'][$id]['actual_prov']=$actual_prov;
			}
		}

		//check initial totals
		$initialSummary='';
		foreach($ids as $id){
			if($id['children']){
				$total = 0;
				foreach($id['children'] as $child)
					$total = $total + $child['initial_prov'];
		
				if( round($total,0) !== round($id['initial_total'],0) ){
				//if(bccomp($total, $id['initial_total'])!=0){	// some servers to have pccomp
					$initialSummary=$initialSummary.'<div style="width:400px;margin-top:15px;">';
					$initialSummary=$initialSummary.'<b>'.$id['internal_code'].' Initial provision is: <span style="float:right;">'.format_number($id['initial_total']).'</span></b>';
					$rowColor='';
					foreach($id['children'] as $child){
						if(!$rowColor){
							$rowColor='style="background-color:#EBEBEB;"';
						}else
							$rowColor='';
						$initialSummary=$initialSummary.'<div '.$rowColor.'>'.$child['id'].'<span style="float:right;">'.format_number($child['initial_prov']).'</span></div>';
					}
					$initialSummary=$initialSummary.'<span style="float:right;text-decoration: underline overline;">Total: '.format_number($total).'</span></div>';
					$initialSummary=$initialSummary.'<div style="clear:both"></div>';
				}
			}
		}

		//check actual totals
		$actualSummary='';
		foreach($ids as $id){
			if($id['children']){
				$total = 0;
				foreach($id['children'] as $child)
					$total = $total + $child['actual_prov'];

				if( round($total,0) !== round($id['actual_total'],0) ){
				//if(bccomp($total, $id['actual_total'])!=0){
					$actualSummary=$actualSummary.'<div style="width:400px;margin-top:15px;">';
					$actualSummary=$actualSummary.'<b>'.$id['internal_code'].' Actual provision is: <span style="float:right;">'.format_number($id['actual_total']).'</span></b>';
					$rowColor='';
					foreach($id['children'] as $child){
						if(!$rowColor){
							$rowColor='style="background-color:#EBEBEB;"';
						}else
							$rowColor='';
						$actualSummary=$actualSummary.'<div '.$rowColor.'>'.$child['id'].'<span style="float:right;">'.format_number($child['actual_prov']).'</span></div>';
					}
					$actualSummary=$actualSummary.'<span style="float:right;text-decoration: underline overline;">Total: '.format_number($total).'</span></div>';
					$actualSummary=$actualSummary.'<div style="clear:both"></div>';
				}
			}
		}

		if($initialSummary || $actualSummary){
			$result = '<div style="margin-top:15px;width:850px;">';

			$result = $result.'<div style="float:left;margin-right:50px;">';
			if($initialSummary)
				$result = $result.$initialSummary;
			else
				$result = $result.'<span style="color:green">Initial provision totals check ok</span>';
			$result = $result.'</div>';

			$result = $result.'<div style="float:right">';
			if($actualSummary)
				$result = $result.$actualSummary;
			else
				$result = $result.'<span style="color:green">Actual provision totals check ok</span>';
			$result = $result.'</div>';

			$result = $result.'<div style="clear:both"></div></div>';
			echo CJavaScript::jsonEncode(array('totals'=>$result));
		}
		else
			echo count($lines) - 1;
	}

	/*
	 * Only after running the checks do we import a CSV into the database
	 */
	public function actionImportCSVData($id)
	{
		if (!$id){
			echo CJavaScript::jsonEncode(array('error'=>'Year not selected'));
			Yii::app()->end();
		}
		if (!isset($_POST['csv_file'])){
			echo CJavaScript::jsonEncode(array('error'=>'CSV file path not defined.'));
			Yii::app()->end();			
		}
		$model = new ImportCSV;
		$model->csv = $model->path.$_POST['csv_file'];
		$model->year = $id;

		$criteria=new CDbCriteria;
		$criteria->condition='parent IS NULL AND year= :year';
		$criteria->params[':year'] = $model->year;

		$yearly_budget=Budget::model()->find($criteria);
		if ($yearly_budget===null){
			echo CJavaScript::jsonEncode(array('error'=>'Selected Year '.$model->year.' does not exist in database.'));
			Yii::app()->end();
		}
		$error = Null;
		//testing new import method
		//list($new_budgets, $updated_budgets, $error) = $model->importCSVData_bigData();
		list($new_budgets, $updated_budgets, $error) = $model->importCSVData_classic($yearly_budget);

		if ($error){
			echo CJavaScript::jsonEncode(array('error'=>$error));
		}else{
			if($yearly_budget->isPublished()){
				Config::model()->isZipFileUpdated(0);
			}
			// default feature budgets here
			if(Config::model()->findByPk('budgetAutoFeature')->value){
				$yearly_budget->autoFeatureBudgets();
			}
			Log::model()->write('Budget', 'Year '.$model->year.'. CSV import. New budgets '.$new_budgets.', Updated budgets '.$updated_budgets);
			echo CJavaScript::jsonEncode(array('new_budgets'=>$new_budgets, 'updated_budgets'=>$updated_budgets));
		}
	}

	/*
	 * Admin can export a csv
	 */
	public function actionExport($id)
	{
		if (!Budget::model()->findByAttributes(array('year'=>$id)) ){
			return false;
		}
		$model = new ImportCSV;
		if(list($file, $budgets) = $model->createCSV($id)){
			$download='<a href="'.$file->getWebPath().'">'.$file->getWebPath().'</a>';
			Yii::app()->user->setFlash('csv_generated', count($budgets).' budgets exported<br />'.$download);

			$criteria=new CDbCriteria;
			$criteria->condition='parent IS NULL AND year=:year';
			$criteria->params[":year"] = $id;
			$budget=Budget::model()->find($criteria);
			if ($budget===null){
				throw new CHttpException(404,'The requested Budget does not exist.');
			}
			$this->redirect(array('/budget/updateYear', 'id'=>$budget->id));
		}
	}

	/*
	 * Part of the import CSV process
	 * The CSV import process may update the CSV with missing values
	 * Admin can download updated csv
	 */
	public function actionDownloadUpdatedCSV($id)
	{
		$year=$id;
		$model = new ImportCSV;
		$path = $model->path;
		$filename = $model->getTmpCSVFilename($year);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($path.$filename));
		ob_end_flush();
		@readfile($path.$filename);
		exit;
	}


	private function strip_single_tag($tag,$string)
	{
		$string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
		$string=preg_replace('/<\/'.$tag.'>/i', '', $string);
		return $string;
	} 

	/**
	 * import new data into common budget descriptions
	 * upload csv to app/files/csv/descriptions.csv and call url csv/updateCommonDescriptions
	 * REMEMBER to add a empty column to the beginning of the csv before importing.!!
	 */
	public function actionUpdateCommonDescriptions()
	{
		$text_delimiter='¬';
		$field_delimiter='|';
		$model = new ImportCSV;

		$mega_array = explode('|', file_get_contents($model->path.'descriptions.csv'));
		array_shift($mega_array);
		$header=1;
		$new_desc=0;
		$updated_desc=0;

		while($mega_array){
			$field_cnt=0;
			$row=array();
			while($field_cnt < 7){
				$row[] = array_shift($mega_array);
				//echo $field_cnt.': '.$row[$field_cnt].'<br />';
				$field_cnt++;
			}
			if($header){
				$header=Null;
				continue;	
			}

			$csv_id = trim(trim($row[0], $text_delimiter));
			$language = trim(trim($row[1], $text_delimiter));

			if(!$budget = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$csv_id, 'language'=>$language))){
				$budget=new BudgetDescCommon;
				$budget->csv_id = $csv_id;
				$budget->language = $language;
				++$new_desc;
			}else
				++$updated_desc;
				
			$budget->code = trim(trim($row[2], $text_delimiter));
			$budget->label = trim(trim($row[3], $text_delimiter));
			$budget->concept = trim(trim($row[4], $text_delimiter));
			$budget->description = trim(trim($row[5], $text_delimiter));
			$budget->description = $this->strip_single_tag('strong', $budget->description);
			
			$budget->text = trim(trim(trim($row[6], $text_delimiter)), $text_delimiter);
			$budget->modified = date('c');
				
			//$budget->validate();
			if(!$budget->save()){
				echo CHtml::errorSummary($budget);
				echo "<p>New: $new_desc, Updated: $updated_desc</p>";
				Yii::app()->end();
			}
		}
		echo "<p>New: $new_desc, Updated: $updated_desc</p>";
	}


	/**
	 * create common budget descriptions. (use for new country).
	 * upload csv to app/files/csv/descriptions.csv and call url csv/createCommonDescriptions
	 * REMEMBER to add a empty column to the beginning of the csv before importing.!!
	 */
	public function actionCreateCommonDescriptions()
	{
		$model = new ImportCSV;
		$mega_array = explode('|', file_get_contents($model->path.'descriptions.csv'));
		array_shift($mega_array);
		
		$header=1;
		$rowCnt=0;
		while($mega_array){
			$field_cnt=0;
			$row=array();
			while($field_cnt < 6){
				$row[] = array_shift($mega_array);
				//echo $field_cnt.': '.$row[$field_cnt].'<br />';
				$field_cnt++;
			}
			$rowCnt++;
			if(!$header){
					$budget=new BudgetDescCommon;
					
					$budget->csv_id = trim(trim($row[0], '"'));
					$budget->language = trim(trim($row[1], '"'));
					$budget->code = trim(trim($row[2], '"'));
					$budget->label = trim(trim($row[3], '"'));
					$budget->concept = trim(trim(trim($row[4], '"')),'.');
					$description=str_replace('"', '', $row[5]);
					$description=trim($description);
					$budget->description = nl2br($description);
					$budget->text = $description;
					
					if(!$budget->validate()){
						echo '<br />row count: '.$rowCnt;
						echo '<p>'.CHtml::errorSummary($budget).'</p>';
						print_r($row);
						Yii::app()->end();
					}
					else
						$budget->save();
			}else
				$header=0;
		}
		Yii::app()->end();
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
