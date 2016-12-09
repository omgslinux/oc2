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

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $model
 * @property integer $model_id
 */
class File extends CActiveRecord
{

	public $baseDir;
	public $file;

	public function init()
	{
		$this->baseDir = dirname(Yii::app()->request->scriptFile);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return File the static model class
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
		return 'file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('path, model', 'required'),
			array('model_id', 'numerical', 'integerOnly'=>true),
			array('name, path', 'length', 'max'=>255),
			array('model', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, path, model, model_id', 'safe', 'on'=>'search'),
		);
	}

	public function afterFind()
	{
		if(strpos($this->path, '/runtime') === 0)
			$this->baseDir = Yii::app()->basePath;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	protected function beforeDelete()
	{
		if(file_exists($this->getURI()))
			unlink($this->getURI());
		return parent::beforeDelete();
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => __('Name'),
			'path' => __('Path'),
			'model' => 'Model',
			'model_id' => 'Model ID',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('model',$this->model);
		$criteria->compare('model_id',$this->model_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getURI()
	{
		return $this->baseDir.$this->path;
	}
	
	public function getWebPath()
	{
		return Yii::app()->request->baseUrl.$this->path;
	}
	
	public function normalize($string)
	{
		$string = string2ascii($string);
		$string = str_replace(' ', '-', $string);
		return trim($string, ' -');
	}

	public function checkExtension($ext)
	{
		$path_parts = pathinfo($this->path);
		if($path_parts['extension'] == $ext)
			return 1;
		return 0;
	}

	public function getExtension()
	{
		$path_parts = pathinfo($this->path);
		return $path_parts['extension'];
	}

	public function getYearFromCSVFilename()
	{
		if($this->checkExtension('csv')){
			$year = basename($this->path, ".csv");
			if(strtotime($year) !== false)
				return $year;
		}
		return Null;
	}
}
