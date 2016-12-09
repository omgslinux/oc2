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
 * This is the model class for table "site_page".
 *
 * The followings are the available columns in table 'cms_page':
 * @property integer $id
 * @property integer $block
 * @property integer $weight
 * @property integer $published
 * @property integer advancedHTML
 * @property integer showTitle
 * 
 * The followings are the available model relations:
 * @property SitePageContent[] $sitePageContents
 */
class SitePage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SitePage the static model class
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
		return 'site_page';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('block', 'required'),
			array('block, weight, advancedHTML, showTitle, published', 'numerical', 'integerOnly'=>true),
			array('block, weight', 'validCombination', 'on'=>'create'),
			array('block, weight', 'validCombination', 'on'=>'update'),
			//array('block, weight, advancedHTML, showTitle, published', 'safe', 'on'=>'update'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('block, weight, advancedHTML, showTitle, published', 'safe', 'on'=>'search'),
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
			'sitePageContents' => array(self::HAS_MANY, 'SitePageContent', 'page'),
		);
	}


	public function validCombination($attribute,$params)
	{
		if($this->weight === Null || $this->weight === '')
			return;
		if($existing = $this->findByAttributes(array('block'=>$this->block,'weight'=>$this->weight))){
			if(!$this->isNewRecord && $this->id == $existing->id)
				return;
			$this->addError($attribute, __('Combination already exists.'));
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'block' => __('Block'),
			'weight' => __('Weight'),
			'published' => __('Published'),
			'advancedHTML' => __('Advanced HTML'),
			'showTitle' => __('Include title'),
		);
	}

	public function isMenuItemHighlighted()
	{
		if(strcasecmp(Yii::app()->controller->id, 'sitePage')!==0)
			return 0;
		$arr = explode('/',Yii::app()->request->getPathInfo());
		
		if(isset($arr[1]) && $requestedPage = $this->findByURL($arr[1])){
			if($requestedPage->block == $this->block)
				return 1;
		}
		return 0;
	}

	public function findByURL($contentURL)
	{
		if ($pageContent = SitePageContent::model()->findByAttributes(array('pageURL'=>$contentURL))){
			return $pageContent->page0;
		}
		return null;
	}


	/**
	 * Return the Title of the first related content object
	 */
	public function getTitleForModel($id)
	{
		$content=SitePageContent::model()->findByAttributes(array('page' =>$id), array('condition'=>'pageTitle IS NOT NULL'));
		if (!$content){
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $content->pageTitle;
	}

	public function getContentForModel($lang)
	{
		if($content=SitePageContent::model()->findByAttributes(array('page'=>$this->id,'language'=>Yii::app()->language)))
			return $content;
		$content = SitePageContent::model()->findByAttributes(array('page'=>$this->id), array('condition'=>'pageTitle IS NOT NULL'));
		if (!$content){
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $content;
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

		$criteria->compare('block',$this->block);
		$criteria->compare('weight',$this->weight);
		$criteria->compare('published',$this->published);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'block ASC'),
		));
	}
}
