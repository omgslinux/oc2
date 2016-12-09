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

class ImportCSV extends CFormModel
{
	public $year;
	public $path;
	public $csv;
	public $step = 1;

	public function init()
	{
		$this->path = Yii::app()->basePath.'/runtime/tmp/csv/';
		if(!is_dir($this->path))
			createDirectory($this->path);
	}

	/**
	* @return array validation rules for model attributes.
	*/
	public function rules()
	{
		return array(
			// ... more rules here
			//array('picture', 'length', 'max' => 255, 'tooLong' => '{attribute} is too long (max {max} chars).', 'on' => 'upload'),
			array('csv', 'file', 'types' => 'csv', 'maxSize' => 1024 * 1024 * 2, 'tooLarge' => 'Size should be less then 2MB !!!',),
			// ... more rules here
		);
	}

	public function attributeLabels()
	{
		return array(
			'csv' => __('CSV file'),
		);
	}

	public function getTmpCSVFilename($year=Null)
	{
		if(!$year)
			$year=$this->year;
		return $year.'-'.Yii::app()->user->id.'.csv';
	}

	public function getHeader()
	{
		return 'internal code|code|initial provision|actual provision|trimester 1|trimester 2|trimester 3|trimester 4|label|concept'.PHP_EOL;
	}

	public function getParentCode($internal_code)
	{
		if($isChild = strrpos($internal_code, "-"))
			return substr($internal_code, 0, $isChild);
		else
			return Null;
	}

	public function register2array($register)
	{
		list($id, $code, $initial_prov, $actual_prov, $t1, $t2, $t3, $t4, $label, $concept) = explode("|", $register);
		return array(
					'csv_id'=>$id,
					'code'=>$code,
					'initial_prov'=>$initial_prov,
					'actual_prov'=>$actual_prov,
					't1'=>$t1,
					't2'=>$t2,
					't3'=>$t3,
					't4'=>$t4,
					'label'=>$label,
					'concept'=>trim($concept),
				);
	}

	public function createEmptyBudgetArray()
	{
		return array(
					'internal_code'=>Null,
					'code'=>Null,
					'initial_prov' => 0,
					'actual_prov' => 0,
					't1' => 0,
					't2' => 0,
					't3' => 0,
					't4' => 0,
					'label' => Null,
					'concept' => Null,
				);
	}

	public function array2register($b)
	{
		return 	$b['csv_id'].'|'.$b['code'].'|'.$b['initial_prov'].'|'.$b['actual_prov'].
				'|'.$b['t1'].'|'.$b['t2'].'|'.$b['t3'].'|'.$b['t4'].
				'|'.$b['label'].'|'.$b['concept'];
	}

	public function csv2array()
	{
		$result= array();
		$lines = file($this->csv);

		foreach ($lines as $line_num => $line) {
			if($line_num==0)
				continue;
			list($csv_id, $code, $initial_prov, $actual_prov, $t1, $t2, $t3, $t4, $label, $concept) = explode("|", $line);
			$result[$csv_id]=$line;
		}
		return $result;
	}

	public function orderCSV(){
		$ordered = $this->csv2array($this->csv);
		ksort($ordered);

		$fh = fopen($this->csv, 'w');
		fwrite($fh, $this->getHeader());
		foreach($ordered as $line){
			$line = preg_replace("/\s*[|]\s*/", "|", $line);
			$line = trim($line).PHP_EOL;
			fwrite($fh, $line);
		}
		fclose($fh);
	}

	// check csv is UTF-8
	// this needs improviment
	public function checkEncoding()
	{
		$content = file_get_contents($this->csv);
		$original_encoding = mb_detect_encoding($content, 'UTF-8', true);
		if($original_encoding != 'UTF-8')
			return 0;
		else
			return 1;
	}

	/*
	 * Check delimiter
	 * Check column count
	 * Check that fields that should be numbers, are numbers
	 */
	public function checkCSVFormat()
	{
		$error=array();
		$ids = array();
		$lines = file($this->csv);
		array_shift($lines);
		foreach ($lines as $line_num => $line) {
			$delimiterCnt = substr_count($line, '|');
			if ($delimiterCnt == 0){
				$error[]='Delimiter | not found on row '.$line_num;
				break;
			}
			if ($delimiterCnt != 9){
				$error[]=($delimiterCnt+1).' columns found but expected 10 on row '.$line_num;
				break;
			}

			list($id, $code, $initial_prov, $actual_prov, $t1, $t2, $t3, $t4, $label, $concept) = explode("|", $line);
			$id = trim($id);
			if(in_array($id, $ids)) {
				$error[]='<br />Register '. ($line_num+1) .': Internal code "'.$id.'" is not unique';
			}
			$initial_prov = trim($initial_prov);
			$actual_prov = trim($actual_prov);
			$t1 = trim($t1);
			$t2 = trim($t2);
			$t3 = trim($t3);
			$t4 = trim($t4);
			if(!is_numeric($initial_prov)){
				$error[]='<br />Register '. ($line_num+1) .': Initial provision is not numeric';
			}
			if(!is_numeric($actual_prov)){
				$error[]='<br />Register '. ($line_num+1) .': Actual provision is not numeric';
			}
			if(!is_numeric($t1)){
				$error[]='<br />Register '. ($line_num+1) .': Trimester 1 is not numeric';
			}
			if(!is_numeric($t2)){
				$error[]='<br />Register '. ($line_num+1) .': Trimester 2 is not numeric';
			}
			if(!is_numeric($t3)){
				$error[]='<br />Register '. ($line_num+1) .': Trimester 3 is not numeric';
			}
			if(!is_numeric($t4)){
				$error[]='<br />Register '. ($line_num+1) .': Trimester 4 is not numeric';
			}
			if(!$error){
				if( ($initial_prov+$actual_prov+$t1+$t2+$t3+$t4 ) == 0)
					$error[]='<br />Register '. ($line_num+1) .': All numeric columns are empty';
			}
			$ids[]=$id;
		}
		return array($lines, $error);
	}

	protected function __addMissignRegisters(& $registers)
	{
		$cnt = 0;
		foreach($registers as $internal_code => $register){
			if($parent_id = $this->getParentCode($internal_code)){

				if(!array_key_exists($parent_id, $registers)){
					$cnt +=1;
					$newRegister = $this->createEmptyBudgetArray();
					$newRegister['internal_code'] = $parent_id;
					$reg = implode ( '|' , $newRegister );
					$registers[$parent_id]=$reg.PHP_EOL;
				}
			}
		}
		return $cnt;
	}

	public function addMissingRegisters()
	{
		$registers = $this->csv2array();
		$newRegisterCnt = 0;
		$wild_loop = 0;
		$cnt=0;

		while($cnt = $this->__addMissignRegisters($registers)){
			$newRegisterCnt += $cnt;
			$wild_loop += 1;
			if($wild_loop == 20000)
				break;
			reset($registers);
		}
		if($newRegisterCnt){
			ksort($registers);
			$fh = fopen($this->csv, 'w');
			fwrite($fh, $this->getHeader());
			foreach($registers as $line)
				fwrite($fh, $line);
			fclose($fh);
		}
		return $newRegisterCnt;
	}

	public function checkInternalCodeSanity()
	{
		$msg=array();;
		$registers = $this->csv2array();
		$budgets = array();
		foreach($registers as $csv_id => $register){
			$budget = $this->register2array($register);
			
			$budget['csv_id'] = trim($budget['csv_id']);
			$budget['code'] = trim($budget['code']);
			$budget['initial_prov'] = trim($budget['initial_prov']);
			$budget['actual_prov'] = trim($budget['actual_prov']);
			$budget['t1'] = trim($budget['t1']);
			$budget['t2'] = trim($budget['t2']);
			$budget['t3'] = trim($budget['t3']);
			$budget['t4'] = trim($budget['t4']);
			$budgets[]=$budget;		
			
			if(strlen($budget['csv_id']) > 3){	// because 'S-E' == 3
				$codes = explode("-", $budget['csv_id']);
				$pos=2;	// start at first code number S-E- =>1<= -11-111
				while($pos <= count($codes)){
					if(!isset($codes[$pos+1]))	// we're at the end of the array
						break;
					if(strpos($codes[$pos+1], $codes[$pos], 0) !== 0){
						$msg[] = '<br />Check internal_code '.$budget['csv_id'];
						break;
					}
					$pos += 1;
				}
			}
		}
		if(!$msg){
			// saved trimmed values to csv
			$fh = fopen($this->csv, 'w');
			fwrite($fh, $this->getHeader());
			foreach($budgets as $budget){
				$line = $this->array2register($budget);
				fwrite($fh, $line.PHP_EOL);
			}
			fclose($fh);		
		}
		return $msg;
	}

	public function addMissingConcepts()
	{
		$registers = $this->csv2array();
		$budgets = array();
		$lang=getDefaultLanguage();
		$updated=0;
		
		foreach($registers as $internal_code => $register){
			$budgets[$internal_code] = $this->register2array($register);

			if($budgets[$internal_code]['code'] === '' || $budgets[$internal_code]['concept'] === '' || $budgets[$internal_code]['concept'] === 'UNKNOWN'){
				
				$description = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$internal_code, 'language'=>$lang));
				if(!$description)
					$description = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$internal_code, 'language'=>$lang));

				if($description){
					if(($budgets[$internal_code]['code'] === '') && strlen($budgets[$internal_code]['csv_id']) > 3){
						$budgets[$internal_code]['code'] = $description->code;
						if($budgets[$internal_code]['code'] !== '')
							$updated++;
					}
					if($budgets[$internal_code]['concept'] === '' || $budgets[$internal_code]['concept'] === 'UNKNOWN'){
						$budgets[$internal_code]['concept'] = trim(preg_replace('/\s+/', ' ', $description->concept));
						if($budgets[$internal_code]['concept'] !== '')
							$updated++;
					}
				}
				if($budgets[$internal_code]['concept'] === ''){
					$budgets[$internal_code]['concept'] = 'UNKNOWN';
					$updated++;
				}
				if(($budgets[$internal_code]['code'] === '') && strlen($budgets[$internal_code]['csv_id']) > 3){
					if($isChild = strrpos($internal_code, "-"))
						$budgets[$internal_code]['code'] = substr($internal_code, $isChild+1);
					else
						$budgets[$internal_code]['code'] = 'PLEASE FIX ME';
					$updated++;
				}
			}
		}
		if($updated){
			$fh = fopen($this->csv, 'w');
			fwrite($fh, $this->getHeader());
			foreach($budgets as $budget){
				$line = $this->array2register($budget);
				fwrite($fh, $line.PHP_EOL);
			}
			fclose($fh);
		}
		return $updated;
	}

	public function addMissingTotals()
	{
		$registers = $this->csv2array();
		$registers = array_reverse($registers, true);
		$budgets = array();
		foreach($registers as $internal_code => $register){
			$budgets[$internal_code] = $this->register2array($register);
		}

		$total=0;
		$parentID_placeholder=Null;
		$budgetID_parent=Null;
		$totals = array();
		$updated=0;
		
		$updated_initial_prov = 0;
		$updated_actual_prov = 0;
		$updated_t1 = 0;
		$updated_t2 = 0;
		$updated_t3 = 0;
		$updated_t4 = 0;

		foreach($budgets as $internal_code => & $budget){
			if (isset($totals[$internal_code])){	
				$initial_prov = $budget['initial_prov'];
				if ($initial_prov == 0){
					$budget['initial_prov'] = $totals[$internal_code]['initial_prov'];
					if($totals[$internal_code]['initial_prov'] != $initial_prov)
						$updated_initial_prov += 1;
				}
				$actual_prov = $budget['actual_prov'];
				if ($actual_prov == 0){
					$budget['actual_prov'] = $totals[$internal_code]['actual_prov'];
					if($totals[$internal_code]['actual_prov'] != $actual_prov)
						$updated_actual_prov += 1;
				}
				$t1 = $budget['t1'];
				if ($t1 == 0){
					$budget['t1'] = $totals[$internal_code]['t1'];
					if($budget['t1'] != $t1)
						$updated_t1 += 1;
				}
				$t2 = $budget['t2'];
				if ($t2 == 0){
					$budget['t2'] = $totals[$internal_code]['t2'];
					if($budget['t2'] != $t2)
						$updated_t2 += 1;
				}
				$t3 = $budget['t3'];
				if ($t3 == 0){
					$budget['t3'] = $totals[$internal_code]['t3'];
					if($budget['t3'] != $t3)
						$updated_t3 += 1;
				}
				$t4 = $budget['t4'];
				if ($t4 == 0){
					$budget['t4'] = $totals[$internal_code]['t4'];
					if($budget['t4'] != $t4)
						$updated_t4 += 1;
				}
			}
			$budgetID_parent = $this->getParentCode($internal_code);

			if($budgetID_parent != $parentID_placeholder){
					if (!isset($totals[$budgetID_parent])){
						$totals[$budgetID_parent]=$this->createEmptyBudgetArray();
					}
					$parentID_placeholder=$budgetID_parent;
			}

			$totals[$budgetID_parent]['initial_prov'] += $budget['initial_prov'];
			$totals[$budgetID_parent]['actual_prov'] += $budget['actual_prov'];
			$totals[$budgetID_parent]['t1'] += $budget['t1'];
			$totals[$budgetID_parent]['t2'] += $budget['t2'];
			$totals[$budgetID_parent]['t3'] += $budget['t3'];
			$totals[$budgetID_parent]['t4'] += $budget['t4'];
		}

		$budgets = array_reverse($budgets, true);
		$updated = $updated_initial_prov + $updated_actual_prov + $updated_t1 + $updated_t2 + $updated_t3 + $updated_t4;
		
		if ($updated){
			$fh = fopen($this->csv, 'w');
			fwrite($fh, $this->getHeader());
			foreach($budgets as $budget){
				$line = $this->array2register($budget);
				fwrite($fh, $line.PHP_EOL);
			}
			fclose($fh);
		}
		return array($updated_initial_prov, $updated_actual_prov, $updated_t1, $updated_t2, $updated_t3, $updated_t4);
	}


	public function importCSVData_classic($rootBudget)
	{
		$errors=Null;	// this doesn't seen to be reporting anything
		$new_budgets = 0;
		$updated_budgets = 0;
		$lines = file($this->csv);

	//try {
		//Yii::app()->db->createCommand("LOCK TABLES budget WRITE, budget AS t WRITE")->execute();
		$transaction = Yii::app()->db->beginTransaction();
	
		foreach ($lines as $line_num => $line) {
			if ($line_num==0){
				continue;
			}
			list($csv_id, $code, $initial_prov, $actual_prov, $t1, $t2, $t3, $t4, $label, $concept) = explode("|", $line);

			$new_budget=new Budget;
			$new_budget->csv_id = trim($csv_id);
			$new_budget->csv_parent_id = $this->getParentCode($csv_id);
			$new_budget->year = $rootBudget->year;
			$new_budget->code = trim($code);
			$new_budget->label = trim($label);
			$new_budget->concept = trim($concept);

			$new_budget->initial_provision = trim($initial_prov);
			$new_budget->actual_provision = trim($actual_prov);
			$new_budget->trimester_1 = trim($t1);
			$new_budget->trimester_2 = trim($t2);
			$new_budget->trimester_3 = trim($t3);
			$new_budget->trimester_4 = trim($t4);
			
			$new_budget->featured=0;

			$criteria=new CDbCriteria;
			$criteria->condition='csv_id = :csv_id AND year = :year';
			$criteria->params[':csv_id'] = $new_budget->csv_parent_id;
			$criteria->params[':year'] = $this->year;
			
			$parent=Budget::model()->find($criteria);
			if ($parent){
				$new_budget->parent = $parent->id;
			}else{
				$new_budget->parent = $rootBudget->id;
			}
			$criteria=new CDbCriteria;
			$criteria->condition='csv_id = :csv_id AND year = :year';
			$criteria->params[':csv_id'] = $new_budget->csv_id;
			$criteria->params[':year'] = $this->year;
			$budget=Budget::model()->find($criteria);
			if (!$budget){
				//$new_budget->validate();
				//echo CHtml::errorSummary($new_budget);
				//Yii::app()->end();
				$new_budget->save(false);
				$new_budgets = $new_budgets+1;
				continue;
			}
			$new_budget->featured=$budget->featured;
			$differences = $budget->compare($new_budget);
			if (count($differences) == 1){	// only difference is the id
				continue;
			}
			foreach($differences as $attribute=>$values){
				if ($attribute == 'id'){
					continue;
				}
				$budget->owner->$attribute=$values['new'];
			}
			$budget->save(false);
			$updated_budgets = $updated_budgets+1;
		}
		$transaction->commit();
		//Yii::app()->db->createCommand("UNLOCK TABLES")->execute();
	//} catch (Exception $e) {
	//	$transaction->rollBack();
	//	return array($new_budgets, $updated_budgets, $e);
	//} 	
		return array($new_budgets, $updated_budgets, $errors);
	}

	/*
	 * This function is meant to be faster than importCSVData_classic()
	 * Creates two sql files
	 * 1. import new budgets with insert
	 * 2. update budgets with update (not complete)
	 * The executes them and deletes files.
	 */
	public function importCSVData_bigData()
	{
		$registers = $this->csv2array();

		$query = 'SELECT id FROM budget WHERE 1 ORDER BY id DESC LIMIT 1;';
		$budget_table_id = Yii::app()->db->createCommand($query)->queryScalar();

		$insert_fn = $this->path.$this->year.'-insert.sql';
		if( file_exists($insert_fn)){
			unlink($insert_fn);
		}
		$insert_fh = fopen($insert_fn, 'w');
		fwrite($insert_fh,'LOCK TABLES `budget` WRITE;'.PHP_EOL);
		fwrite($insert_fh, 'START TRANSACTION;'.PHP_EOL);
		fwrite($insert_fh,'INSERT INTO `budget` VALUES'.PHP_EOL);
		
		$update_fn = $this->path.$this->year.'-update.sql';
		if( file_exists($update_fn)){
			unlink($update_fn);
		}
		$update_fh = fopen($update_fn, 'w');		
			
		$tableIndex = array();
		$rootBudget = Budget::model()->findByAttributes(array('year'=>$this->year, 'parent'=>Null));
		if (!$rootBudget){
			throw new CHttpException(404,'The requested rootBudget does not exist.');
		}
		$tableIndex['root'] = Array();
		$tableIndex['root']['table_id'] = $rootBudget->id;
		$tableIndex['root']['parent_table_id'] = NULL;
		
		$rowsToInsert=0;
		$rowsToUpdate=0;
		foreach($registers as $csv_id => $register){
			
			if ($rowsToInsert){
				fwrite($insert_fh, $str.','.PHP_EOL);
			}
			
			$tableIndex[$csv_id] = array();
				
			if ($budget = $rootBudget->findByAttributes(array('year'=>$this->year, 'csv_id'=>$csv_id))){
				// update
				$rowsToUpdate++;
				$tableIndex[$csv_id]['table_id'] = $budget->id;
				$tableIndex[$csv_id]['parent_table_id'] = $budget->parent;
				
				$values = $this->register2array($register);
				
				$values['id'] = $tableIndex[$csv_id]['table_id'];
				$values['parent'] = $tableIndex[$csv_id]['parent_table_id'];
				$values['year'] = $budget->year;
				$values['csv_id'] = "'".$budget->csv_id."'";
				$values['csv_parent_id'] = "'".$budget->csv_parent_id."'";
				$values['code'] =  "'".$values['code']."'";
				$values['label'] = "'".str_replace("'", "\\'", $values['label'])."'";
				$values['concept'] = "'".str_replace("'", "\\'", $values['concept'])."'";
				$values['featured'] = $budget->featured;
				$values['weight'] = $budget->weight;
				
				//this needs to be done.
				fwrite($update_fh, $budget->id.','.$budget->parent.','.$budget->csv_id.PHP_EOL);
				continue;
			}

			// insert
			$rowsToInsert++;
			$budget_table_id++;
			$parent_csv_id = $this->getParentCode($csv_id);
			if (!$parent_csv_id){
				// this is S or I category
				$parent_csv_id = 'root';
			}
			$tableIndex[$csv_id]['parent_table_id'] = $tableIndex[$parent_csv_id]['table_id'];
			$tableIndex[$csv_id]['table_id'] = $budget_table_id;
			
			$values = $this->register2array($register);

			$values['id'] = $tableIndex[$csv_id]['table_id'];
			$values['parent'] = $tableIndex[$csv_id]['parent_table_id'];
			$values['year'] = $this->year;
			$values['csv_id'] = "'".$values['csv_id']."'";
			if ($parent_csv_id == 'root' || $parent_csv_id == ''){
				$values['csv_parent_id'] = "''";
			}else{
				$values['csv_parent_id'] = "'$parent_csv_id'";
			}			
			$values['code'] =  "'".$values['code']."'";
			$values['label'] = "'".str_replace("'", "\\'", $values['label'])."'";
			$values['concept'] = "'".str_replace("'", "\\'", $values['concept'])."'";
			$values['featured'] = 0;
			$values['weight'] = 0;
			
			$str = '('.$values['id'].','.$values['parent'].','.$values['year'].','.$values['csv_id'].','.$values['csv_parent_id'].','.$values['code'].','.$values['label'].','.$values['concept'].','.$values['initial_prov'].','.$values['actual_prov'].','.$values['t1'].','.$values['t2'].','.$values['t3'].','.$values['t4'].','.$values['featured'].','.$values['weight'].')';
			$writeLine = true;		
		}
		if ($rowsToInsert){
			fwrite($insert_fh, $str.';'.PHP_EOL);
			fwrite($insert_fh, 'COMMIT;'.PHP_EOL);
			fwrite($insert_fh, 'UNLOCK TABLES;'.PHP_EOL);
		}
		fclose($insert_fh);
		fclose($update_fh);

		$errors = false;
		if ($rowsToInsert || $rowsToUpdate){
			Yii::import('application.includes.*');
			require_once('runSQL.php');

			if ($rowsToInsert){
				$result = runSQLFile($insert_fn);
				if ($result !== true){
					$errors = $result;
				}
			}
			/*
			if (!$errors && $rowsToUpdate){
				$result = runSQLFile($update_fn);
				if ($result !== true){
					$errors = $result;
				}
			}
			*/
		}
		unlink($insert_fn);
		unlink($update_fn);
		return array($rowsToInsert, $rowsToUpdate, $errors);
	}

	public function createCSV($year)
	{
		$file = new File;
		$file->name = $year.'.csv';
		$file->model = 'DatabaseDownload/data';
		$file->path='/files/'.$file->model.'/'.$file->name;
		if($existing_file = File::model()->findByAttributes(array('path'=>$file->path)))
			$file = $existing_file;

		$budgets = Budget::model()->findAllBySql('	SELECT csv_id, code, label, concept, initial_provision, actual_provision,
													trimester_1, trimester_2, trimester_3, trimester_4
													FROM budget
													WHERE year = :year AND parent IS NOT NULL',
													array(':year'=>$year));

		if ($this->_writeCSV($file->getURI(), $budgets)){
			$file->name = $year.'.csv'.' ('.__('generated on the').' '.date('d-m-Y H:i:s').')';
			$file->save();
			return array($file, $budgets);
		}
		return false;
	}

	private function _writeCSV($filename, $budgets)
	{
		$csv = array();
		$lang = getDefaultLanguage();
		$description = new BudgetDescLocal;
		foreach($budgets as $b){
			// we add new localDescription data to the csv
			$desc = $description->getDescriptionFields($b->csv_id, $lang);
			
			$label = $desc['label'] ? $desc['label'] : $b->label;
			$concept = $desc['concept'] ? $desc['concept'] : $b->concept;
			
			$label = preg_replace('/\s+/', ' ', trim($label));	// remove new lines
			$concept = preg_replace('/\s+/', ' ', trim($concept));
			
			$csv[$b->csv_id] = $b->csv_id.'|'.$b->code.'|'.$b->initial_provision.'|'.$b->actual_provision.
						'|'.$b->trimester_1.'|'.$b->trimester_2.'|'.$b->trimester_3.'|'.$b->trimester_4.
						'|'.$label.
						'|'.$concept.
						PHP_EOL;
		}
		ksort($csv);
		$tmpDir = Yii::app()->basePath.'/runtime/tmp/';
		$tmp_fn = $tmpDir.'csv-'.mt_rand(10000,99999);
		$fh = fopen($tmp_fn, 'w');
		fwrite($fh, $this->getHeader());
		foreach($csv as $line)
			fwrite($fh, $line);
		fclose($fh);

		$content = file_get_contents($tmp_fn);
		//file_put_contents($tmp_fn, "\xEF\xBB\xBF".  $content);

		$fh = fopen($tmp_fn, 'w');
        # Now UTF-8 - Add byte order mark
        fwrite($fh, pack("CCC",0xef,0xbb,0xbf));
        fwrite($fh,$content);
        fclose($fh);

		if (copy($tmp_fn, $filename)) {
			unlink($tmp_fn);
			return true;
		}else{
			return false;		
		}
	}
}
