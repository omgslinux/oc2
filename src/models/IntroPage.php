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
 * This is the model class for table "intro_page".
 *
 * The followings are the available columns in table 'intro_page':
 * @property integer $id
 * @property integer $weight
 * @property integer $toppos
 * @property integer $leftpos
 * @property string  $color
 * @property string  $bgcolor
 * @property integer $opacity
 * @property integer $width
 * @property integer $published
 *
 * The followings are the available model relations:
 * @property IntroPageContent[] $introPageContents
 */

class IntroPage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return IntroPage the static model class
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
		return 'intro_page';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('weight, toppos, leftpos, width, published, opacity', 'required'),
			array('weight, toppos, leftpos, width, published', 'numerical', 'integerOnly'=>true),
			array('weight', 'unique', 'className' => 'IntroPage'),
			array('color', 'default', 'setOnEmpty' => true, 'value' => '333333'),
			array('bgcolor', 'default', 'setOnEmpty' => true, 'value' => 'FFFFFF'),
			array('color, bgcolor', 'isColorHex'),
			array('color, bgcolor', 'length', 'allowEmpty'=>true, 'is' => 6),
			array('opacity', 'numerical', 'integerOnly'=>true, 'min'=>0, 'max'=>10),
    
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('weight, toppos, leftpos, width, published', 'safe', 'on'=>'search'),
		);
	}

	public function isColorHex($attribute,$params)
	{
		if(! ctype_xdigit ( $this->$attribute ))
			$this->addError($attribute, 'Not a vaild html color like "FFFFFF"');
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'introPageContents' => array(self::HAS_MANY, 'IntroPageContent', 'page'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'weight' => __('Order'),
			'toppos' => __('Top'),
			'leftpos' => __('Left'),
			'color' => __('Font color'),
			'bgcolor' => __('Background color'),
			'opacity' => __('Opacity'),
			'width' => __('Width'),
			'published' => __('Published'),
		);
	}

	/**
	 * Return the Title of the first related content object
	 */
	public function getTitleForModel($id, $lang=null)
	{
		if(!$lang){
			$content=IntroPageContent::model()->findByAttributes(array('page' => $id));
			if (!$content){
				return Null;
			}
		}
		else{
			$content=IntroPageContent::model()->findByAttributes(array('page'=>$id, 'language'=>$lang));
			if (!$content){
				return Null;
			}
		}
		return $content->title;
	}

	public function getContent($lang)
	{
		return IntroPageContent::model()->findByAttributes(array('page'=>$this->id, 'language'=>$lang));
	}

	public function getNextPage()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition('weight > :weight and published = 1');
		$criteria->params[':weight'] = $this->weight;
		
		$criteria->order = 'weight ASC';
		if($pages = $this->findAll($criteria))
			return $pages[0];

		$criteria=new CDbCriteria;
		$criteria->addCondition('id != :id and published = 1');
		$criteria->params[':id'] = $this->id;
		
		if($page = $this->find($criteria))
			return $page;
		
		return Null;
	}

	/* Convert hexdec color string to rgb(a) string */
	public function hex2rgba($color, $opacity = false) {
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
			return $default; 

		//Sanitize $color if "#" is provided 
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

        //Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}

		//Return rgb(a) color string
		return $output;
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
		$criteria->compare('weight',$this->weight);
		$criteria->compare('toppos',$this->toppos);
		$criteria->compare('leftpos',$this->leftpos);
		$criteria->compare('width',$this->width);
		$criteria->compare('published',$this->published);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
