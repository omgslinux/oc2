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
 * This is the model class for table "emailTemplate".
 *
 * The followings are the available columns in table 'emailTemplate':
 * @property integer $state
 * @property string $body
 */
class EmailTemplate extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EmailTemplate the static model class
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
		return 'email_template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('state, body', 'required'),
			array('state', 'numerical', 'integerOnly'=>true),
			array('body', 'validateBody'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('state, body', 'safe', 'on'=>'search'),
		);
	}

	public function validateBody($attribute,$params)
	{
		if($attribute == 'body'){
			if( strpos($this->body, '%link%') === false)
				$this->addError('body','Text must include %link%');
		}
	}

	public function getBody($enquiry=Null)
	{
		if($enquiry){
			if($enquiry->state == ENQUIRY_ASSIGNED)	// send a link to the assigned team_member
				$enquiry_link = Yii::app()->createAbsoluteUrl('enquiry/teamView', array('id' => $enquiry->id));
			else
				$enquiry_link = Yii::app()->createAbsoluteUrl('enquiry/view', array('id' => $enquiry->id));
			$enquiry_link = '<a href="'.$enquiry_link.'">'.$enquiry_link.'</a>';
		}else
			$enquiry_link = '<a href="/link/to/the/enquiry">/link/to/the/enquiry</a>';

		$body = str_replace('%link%', $enquiry_link, $this->body);
		if( strpos($body, '%name%') !== false ){
			if($enquiry && $enquiry->state==ENQUIRY_PENDING_VALIDATION)
				$body = str_replace('%name%', $enquiry->user0->fullname, $body);
			elseif($this->state == ENQUIRY_PENDING_VALIDATION)
				$body = str_replace('%name%', '&lt;'.__('User fullname will go here').'&gt;', $body);
			else
				$body = str_replace('%name%', '', $body);
		}
		return $body;
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'state' => __('State'),
			'body' => __('Body'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('state',$this->state);
		$criteria->compare('body',$this->body,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
