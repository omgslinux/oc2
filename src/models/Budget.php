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

/**
 * This is the model class for table "budget".
 *
 * The followings are the available columns in table 'budget':
 * @property integer $id
 * @property integer $parent
 * @property integer $year
 * @property string $csv_id
 * @property string $csv_parent_id
 * @property string $code
 * @property string $label
 * @property string $concept
 * @property string $initial_provision
 * @property string $actual_provision
 * @property string $trimester_1
 * @property string $trimester_2
 * @property string $trimester_3
 * @property string $trimester_4
 * @property integer $featured
 * @property integer $weight
 *
 * The followings are the available model relations:
 * @property Budget $parent0
 * @property Budget[] $budgets
 * @property Enquiry[] $enquiries
 */
class Budget extends CActiveRecord
{
	
	public $featuredFilter = '';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Budget the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'budget';
	}

	/**
	 * @return string this models log prefix
	 */
	public function logPrefix()
	{
		return 'budget';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('year, concept, initial_provision, actual_provision, trimester_1, trimester_2, trimester_3, trimester_4, featured', 'required'),
			array('parent, featured, weight', 'numerical', 'integerOnly'=>true),
			array('year', 'date', 'format'=>'yyyy'),
			array('initial_provision, actual_provision, trimester_1, trimester_2, trimester_3, trimester_4', 'type', 'type'=>'float'),
			array('code', 'length', 'max'=>20),
			array('csv_id, csv_parent_id', 'length', 'max'=>255),
			//array('csv_id', 'unique', 'className' => 'Budget'),	// this is a good idea but need to check against year
			array('label, concept', 'length', 'max'=>255),
			array('year', 'unique', 'className'=>'Budget', 'on'=>'newYear'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('year, code, concept', 'safe', 'on'=>'search'),

		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'parent0' => array(self::BELONGS_TO, 'Budget', 'parent'),
			'budgets'=>array(self::HAS_MANY, 'Budget', 'parent', 'order'=>'budgets.csv_id ASC'),
			'enquirys' => array(self::HAS_MANY, 'Enquiry', 'budget'),
		);
	}


	// is this used?
	public function orderChildBudgets()
	{
		$budgtes = $this->with(array(
			'budget'=>array(
					'order'=>'budget.id',
			),
		));
		$this->budgets = $budgets;
	}

	public function behaviors()  {
		// http://www.yiiframework.com/forum/index.php/topic/10285-how-to-compare-two-active-record-models/
		return array('PCompare'); 
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent' => __('Parent'),
			'csv_id' => 'internal code',
			'csv_parent_id' => 'internal parent code',
			'year' => __('Year'),
			'code' => __('Code'),
			'label' => __('Label'),
			'concept' => __('Concept'),
			'initial_provision' => __('Initial provision'),
			'actual_provision' => __('Actual provision'),
			'trimester_1' => __('Trimester 1'),
			'trimester_2' => __('Trimester 2'),
			'trimester_3' => __('Trimester 3'),
			'trimester_4' => __('Trimester 4'),
			'featured' => __('Featured'),
			'weight' => __('Weight'),
		);
	}

	public function getYearString()
	{
		return CHtml::encode($this->year);
	}

	public function getLabel()
	{
		if($description = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>Yii::app()->language)))
			return $description->label;
			
		if($description = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>Yii::app()->language)))
			return $description->label;

		if(!$this->label)
			return $this->label;

		return __('Budget');
	}

	public function getConcept()
	{
		if($description = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>Yii::app()->language)))
			return $description->concept;
			
		if($description = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>Yii::app()->language)))
			return $description->concept;

		return $this->concept;
	}

	public function getTitle()
	{
		$label='';
		$concept='';
		$lang=Yii::app()->language;
		if($description = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>$lang))){
			if($description->label)
				$label = $description->label;
			if($description->concept)
				$concept = $description->concept;
			if($label && $concept)
				return $label.': '.$concept;
		}
		if($description = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>$lang))){
			if($description->label && !$label)
				$label = $description->label;
			if($description->concept && !$concept)
				$concept = $description->concept;			
		}
		if($label && $concept)
			return $label.': '.$concept;
		if($concept)
			return $concept;
		return $this->concept;	// data imported with csv		
	}

	public function getDescription()
	{
		$lang=Yii::app()->language;
		if($description = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>$lang))){
			if($description->description)
				return $description;
		}
		if($description = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>$lang))){
			if($description->description)
				return $description;
		}
		return Null;
	}

	public function getExplication()
	{
		$lang=Yii::app()->language;
		if($description = BudgetDescLocal::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>$lang))){
			if($description->description)
				return $description->description;
		}
		if($description = BudgetDescCommon::model()->findByAttributes(array('csv_id'=>$this->csv_id, 'language'=>$lang))){
			if($description->description)
				return $description->description;
		}
		return Null;
	}

	// return the percentage of this budget from total
	public function getPercentage()
	{
		if($rootBudget = $this->findByAttributes(array('csv_id'=>substr($this->csv_id, 0, 1), 'year'=>$this->year)))
			return percentage($this->actual_provision, $rootBudget->actual_provision);
		return '--';
	}
	
	public function getExecuted()
	{
		return $this->trimester_1 + $this->trimester_2 + $this->trimester_3 + $this->trimester_4;
	}

	public function getCategory()
	{
		if($budget = $this->findByAttributes(array('csv_id'=>substr($this->csv_id, 0, 3))))
			return $budget->getConcept();
		return '<span style="color:red">getCategory('.$this->csv_id.')</span>';
	}	

	public function getPopulation($year=Null)
	{
		if(!$year)
			$year=$this->year;
		return $this->findByAttributes(array('year'=>$year,'parent'=>Null))->initial_provision;
	}
	
	public function getChildBudgets()
	{
		if(!$this->budgets)
			return null;
		
		$criteria=new CDbCriteria;
		$criteria->addCondition('parent = :id and actual_provision != 0');
		$criteria->params[':id'] = $this->id;
		$criteria->order = "csv_id ASC";
		
		return $this->findAll($criteria);
	}
	
	public function isPublished()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition("parent is null and code = 1 and year= :year");
		$criteria->params[":year"] = $this->year;
		return $this->find($criteria);
	}
	
	public function getPublicYears()
	{
		return $this->findAll(array('condition'=>'parent IS NULL AND code = 1','order'=>'year DESC'));
	}

	public function getFeatured()
	{
		if(!$this->year)
			return Null;
		$criteria=new CDbCriteria;
		$criteria->addCondition('featured = 1');
		$criteria->addCondition('year = :year');
		$criteria->params[":year"] = $this->year;
		$criteria->order = 'weight DESC';
		return $this->findAll($criteria);
	}

	public function refreshFeaturedWeights()
	{
		$featuredBudgets = $this->getFeatured();
		$weight = count($featuredBudgets);
		foreach($featuredBudgets as $budget){
			$budget->weight = $weight;
			$budget->save();
			$weight--;
		}
	}

	/**
	 * Dump the budget table
	 */
	public function dumpBudgets()
	{
		$timestamp = time();

		$file = new File();
		$file->baseDir = Yii::app()->basePath;
		$file->model = get_class($this);
		$file->path = '/runtime/'.$file->model;
		
		if(!is_dir($file->getURI()))
			createDirectory($file->getURI());		

		$file->path = $file->path.'/budget-dump-'.date('Y-m-d-H-i-s',$timestamp).'.sql';
		$file->name = __('Budget table saved on the').' '.date('d-m-Y H:i:s',$timestamp);

		$backup = new Backup();
		if($error = $backup->dumpDatabase($file->getURI(), 'budget')){
			if(file_exists($file->getURI()))
				unlink($file->getURI());
			echo $error;
		}else{
			$file->save();
			echo 0;
		}
	}

	/**
	 * Restore the budget table
	 */
	public function restoreBudgets($file_id)
	{
		Yii::import('application.includes.*');
		require_once('runSQL.php');

		$file = File::model()->findByPk($file_id);
		if(!$file)
			Yii::app()->end();
		
		return runSQLFile($file->getURI());
/*
		$params = getMySqlParams();
		$output = NULL;
		$return_var = NULL;
		$command =	'mysql --user='.$params['user'].' --password='.$params['pass'].
					' --host='.$params['host'].' --default_character_set utf8 '.$params['dbname'].' < '.$file->getURI();
		exec($command, $output, $return_var);
		echo $return_var;
*/
	}

	/*
	 * After importing a CSV we can find base budgets and feature them
	 */
	public function autoFeatureBudgets()
	{
		$criteria=new CDbCriteria;
		$criteria->condition = 'year = :year AND char_length(csv_id) = 3';
		$criteria->params[":year"] = $this->year;
		
		$budgets= $this->findAll($criteria);
		foreach($budgets as $budget){
			if($budget->featured == 1)
				continue;
			$budget->featured=1;
			$budget->save();
			$this->refreshFeaturedWeights();
		}
	}

	/*
	 * Has the provision been changed by the Administration?
	 */
	public function hasModifications()
	{
		$criteria=new CDbCriteria;
		$criteria->condition = 'year = :year AND parent IS NOT NULL';
		$criteria->addCondition('initial_provision != actual_provision');
		$criteria->addCondition('csv_id IS NOT NULL');
		$criteria->params[":year"] = $this->year;	
		
		if ($this->find($criteria)){
			return true;
		}
		return false;
	}
	
	public function budgetsWithoutDescription()
	{
		($this->csv_id)? $csv_id='AND b.csv_id LIKE "%'.$this->csv_id.'%"' : $csv_id='';
		($this->code)? $code='AND b.code = "'.$this->code.'"' : $code='';
		($this->year)? $year='AND b.year = "'.$this->year.'"' : $year='';
		
		$sql =" SELECT
				b.csv_id AS csv_id,
				b.id AS id,
				b.year AS year,
				b.code AS code
				FROM budget AS b
				LEFT JOIN (
					SELECT dc.csv_id AS common_csv_id, dl.csv_id AS local_csv_id
					from budget_desc_common dc
					LEFT OUTER JOIN budget_desc_local dl ON dc.csv_id = dl.csv_id
					UNION
					SELECT dc.csv_id AS common_csv_id, dl.csv_id AS local_csv_id
					from budget_desc_common dc
					RIGHT OUTER JOIN budget_desc_local dl ON dc.csv_id = dl.csv_id
				) AS description ON b.csv_id = description.common_csv_id OR b.csv_id = description.local_csv_id
				WHERE description.common_csv_id IS NULL AND description.local_csv_id IS NULL AND parent IS NOT NULL
				$code $year $csv_id 
				ORDER BY b.csv_id, b.year";

		$cnt = "SELECT COUNT(*) FROM ($sql) subq";
		$count = Yii::app()->db->createCommand($cnt)->queryScalar();

		return new CSqlDataProvider($sql,array(	'totalItemCount'=>$count,
												'pagination'=>array('pageSize'=>10),
											));
	}

	public function getYearsBudgetCount()
	{
		$sql = 'SELECT COUNT(*) FROM budget where year = :year AND parent IS NOT NULL';
		return Yii::app()->db->createCommand($sql)->bindValue(":year", $this->year)->queryScalar();
	}

	public function getYearsTotalEnquiries()
	{
		$year = $this->year;
		$sql ="SELECT budget.id, budget.year, enquiry.budget
				FROM budget
				INNER JOIN enquiry
				ON budget.id=enquiry.budget
				WHERE budget.year = '$year'"; 

		$cnt = "SELECT COUNT(*) FROM ($sql) subq";
		return Yii::app()->db->createCommand($cnt)->queryScalar();
	}


	public function publicSearch()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition("parent is null and year= :year");
		$criteria->params[":year"] = $this->year;
		
		$yearly_budget=$this->find($criteria);
		if (!$yearly_budget){
			return new CActiveDataProvider($this,array('data'=>array()));	
		}
		if (!Yii::app()->user->isAdmin()){
			if($yearly_budget->code != 1)	//not published
				return new CActiveDataProvider($this,array('data'=>array()));
		}
		if (!$this->code && !$this->concept){
			return new CActiveDataProvider($this,array('data'=>array()));
		}
		$sql_params=array(
			":search_year" => $this->year,
			":lang" => Yii::app()->language
		);
		if($this->code){
			$sql_params[":search_code"]=$this->code;
			$sql = "SELECT	`b`.`csv_id` AS `csv_id`,
				`b`.`id` AS `id`,
				`b`.`year` AS `year`,
				`b`.`code` AS `code`,
				`b`.`initial_provision` AS `initial_provision`,
				`b`.`actual_provision` AS `actual_provision`,
				`description`.`common_text` AS `common_text`,
				`description`.`common_concept` AS `common_concept`,
				`description`.`local_text` AS `local_text`,
				`description`.`local_concept` AS `local_concept`,
				`b`.`year` AS `common_score`,
				`b`.`year` AS `local_score`,
				`b`.`year` AS score
				
				FROM `budget` AS `b`
				LEFT JOIN (
					SELECT	`dc`.`csv_id` AS `common_csv_id`,
							`dc`.`language` AS `common_language`,
							`dc`.`concept` AS `common_concept`,
							`dc`.`text` AS `common_text`,
							`dl`.`csv_id` AS `local_csv_id`,
							`dl`.`language` AS `local_language`,
							`dl`.`concept` AS `local_concept`,
							`dl`.`text` AS `local_text`
					FROM `budget_desc_common` `dc`
					LEFT OUTER JOIN `budget_desc_local` `dl` ON `dc`.`csv_id` = `dl`.`csv_id` AND `dc`.`language` = `dl`.`language`
					UNION
					SELECT	`dc`.`csv_id` AS `common_csv_id`,
							`dc`.`language` AS `common_language`,
							`dc`.`concept` AS `common_concept`,
							`dc`.`text` AS `common_text`,
							`dl`.`csv_id` AS `local_csv_id`,
							`dl`.`language` AS `local_language`,
							`dl`.`concept` AS `local_concept`,
							`dl`.`text` AS `local_text`
					FROM budget_desc_common dc
					RIGHT OUTER JOIN `budget_desc_local` `dl` ON `dc`.`csv_id` = `dl`.`csv_id` AND `dc`.`language` = `dl`.`language`
				) AS `description` ON `b`.`csv_id` = `description`.`common_csv_id` OR `b`.`csv_id` = `description`.`local_csv_id`
				WHERE
					year = :search_year AND code = :search_code
					AND (`description`.`common_language` = :lang OR description.local_language = :lang)
					AND `b`.`parent` IS NOT NULL";

			$cnt = "SELECT COUNT(*) FROM ($sql) subq";
			$count= Yii::app()->db->createCommand($cnt)->bindValues($sql_params)->queryScalar();

			return new CSqlDataProvider($sql, array(
												'params' => $sql_params,
												'totalItemCount'=>$count,
												'pagination'=>array('pageSize'=>10),
										));
		}
        $sql_params[":search_text"] = $this->concept;
		$sql = "SELECT	`b`.`csv_id` AS `csv_id`,
				`b`.`id` AS `id`,
				`b`.`year` AS `year`,
				`b`.`code` AS `code`,
				`b`.`initial_provision` AS `initial_provision`,
				`b`.`actual_provision` AS `actual_provision`,
				`description`.`common_text` AS `common_text`,
				`description`.`common_concept` AS `common_concept`,
				`description`.`local_text` AS `local_text`,
				`description`.`local_concept` AS `local_concept`,				
				`description`.`common_score` AS common_score,
				`description`.`local_score` AS local_score,
				(`description`.`common_score` + (`description`.`local_score`+6 * LOG(`description`.`local_score`+1))) AS score

				FROM `budget` AS `b`
				LEFT JOIN (
					SELECT	`dc`.`csv_id` AS `common_csv_id`,
							`dc`.`language` AS `common_language`,
							`dc`.`concept` AS `common_concept`,
							`dc`.`text` AS `common_text`,
							MATCH (`dl`.`concept`, `dl`.`text`) AGAINST (:search_text) AS local_score,
							MATCH (`dc`.`concept`, `dc`.`text`) AGAINST (:search_text) AS common_score,
							`dl`.`csv_id` AS `local_csv_id`,
							`dl`.`language` AS `local_language`,
							`dl`.`concept` AS `local_concept`,
							`dl`.`text` AS `local_text`
					from budget_desc_common dc
					LEFT OUTER JOIN `budget_desc_local` `dl` ON 
									`dc`.`csv_id` = `dl`.`csv_id` AND
									`dc`.`language` = `dl`.`language`
					UNION
					SELECT	`dc`.`csv_id` AS `common_csv_id`,
							`dc`.`language` AS `common_language`,
							`dc`.`concept` AS `common_concept`,
							`dc`.`text` AS `common_text`,
							MATCH (`dl`.`concept`, `dl`.`text`) AGAINST (:search_text) AS local_score,
							MATCH (`dc`.`concept`, `dc`.`text`) AGAINST (:search_text) AS common_score,
							`dl`.`csv_id` AS `local_csv_id`,
							`dl`.`language` AS `local_language`,
							`dl`.`concept` AS `local_concept`,
							`dl`.`text` AS `local_text`
					FROM budget_desc_common dc
					RIGHT OUTER JOIN `budget_desc_local` `dl` ON `dc`.`csv_id` = `dl`.`csv_id` AND `dc`.`language` = `dl`.`language`
				) AS `description` ON 
						`b`.`csv_id` = `description`.`common_csv_id` OR
						`b`.`csv_id` = `description`.`local_csv_id`

				WHERE
					year = :search_year AND
					(`description`.`common_language` = :lang OR description.local_language = :lang) AND
					(common_score > 0 OR local_score > 0)
				ORDER BY score DESC";

			$cnt = "SELECT COUNT(*) FROM ($sql) subq";
			$count= Yii::app()->db->createCommand($cnt)->bindValues($sql_params)->queryScalar();

			return new CSqlDataProvider($sql, array(
												'params' => $sql_params,
												'totalItemCount'=>$count,
												'pagination'=>array('pageSize'=>10),
										));
	}

	public function getAllBudgetsWithCSV_ID()
	{
		$criteria=new CDbCriteria;
		$root_budgets=$this->findAllByAttributes(array('parent'=>Null, 'code'=>0));	// code means published

		foreach($root_budgets as $budget){
			//if($budget->code == 0)	// this year not published
			$criteria->addCondition('year != :year');
			$criteria->params[":year"] = $budget->year;
		}
		$criteria->addCondition('csv_id = :csv_id');
		$criteria->params[":csv_id"] = $this->csv_id;
		$criteria->order = 'year DESC';
		return $this->findAll($criteria);	
	}

	public function changeTypeSearch()
	{
		$criteria=new CDbCriteria;

		$root_budgets=$this->findAllByAttributes(array('parent'=>Null));
		foreach($root_budgets as $budget){
			if($budget->code == 0){	// this year not published
				$criteria->addCondition('year != :year');
				$criteria->params[":year"] = $budget->year;	
			}
		}
		$criteria->addCondition('parent is not null');	// dont show year budget
		$criteria->addCondition('CHAR_LENGTH(t.csv_id) > 1');	// don't show 'S' or 'I', etc
		
		$criteria->compare('csv_id',$this->csv_id, true);
		$criteria->compare('year',$this->year);
		$criteria->compare('code',$this->code);
		$criteria->compare('concept',$this->concept,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function featuredSearch()
	{
		/*
		SELECT * FROM `budget` `t` LEFT JOIN budget as child
		ON t.id = child.parent AND t.year = child.year 
		WHERE (CHAR_LENGTH(t.csv_id) > 1) AND child.id IS NOT NULL group by t.id
		*/
		
		$criteria=new CDbCriteria;
		// we only show budgets that have children. otherwise the graphs don't make sense.
		$criteria->select = 't.*';
		$criteria->together = true;
		$criteria->join = 'LEFT JOIN budget as child ON t.id = child.parent AND t.year = child.year';
		$criteria->addCondition('child.id IS NOT NULL');
		$criteria->group = 't.id';

		$criteria->addCondition('CHAR_LENGTH(t.csv_id) > 1');	// don't show 'S' o 'I', etc
		
		$criteria->compare('t.featured', $this->featured);
		$criteria->compare('t.weight', $this->weight);
		$criteria->compare('t.code', $this->code);
		$criteria->compare('t.concept', $this->concept, true);
		$criteria->compare('t.csv_id', $this->csv_id, true);
		$criteria->compare('t.year',$this->year);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'t.featured DESC, t.weight DESC, t.csv_id ASC'),
		));
	}

	/*
	 * budgets displayed in modified grid at budget/index
	 */
	public function modifiedSearch()
	{
		$criteria=new CDbCriteria;
		$criteria->condition = 'year = :year AND parent IS NOT NULL';
		$criteria->addCondition('initial_provision != actual_provision');	// modified true
		$criteria->addCondition('CHAR_LENGTH(csv_id) > 1');	// don't show 'S' o 'I', etc
		if ($this->featuredFilter != ''){
			$criteria->addCondition('csv_id LIKE :featured');
			$criteria->params[":featured"] = "$this->featuredFilter%";
		}
		$criteria->params[":year"] = $this->year;
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>60),
			'sort'=>array('defaultOrder'=>'csv_id ASC'),
		));	
	}

	/*
	 * budgets displayed in the grid for deletetion
	 */
	public function deleteTreeSearch()
	{
		$criteria=new CDbCriteria;
		$criteria->condition = 'year = :year AND parent IS NOT NULL';
		$criteria->params[":year"] = $this->year;
		
		$criteria->compare('code', $this->code);
		$criteria->compare('concept', $this->concept, true);
		$criteria->compare('csv_id', $this->csv_id, true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'csv_id ASC'),
		));		
	}
}

