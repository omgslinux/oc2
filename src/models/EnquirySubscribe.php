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
 * This is the model class for table "enquiry_subscribe".
 *
 * The followings are the available columns in table 'enquiry_subscribe':
 * @property integer $id
 * @property integer $enquiry
 * @property integer $user
 *
 * The followings are the available model relations:
 * @property User $user0
 * @property Enquiry $enquiry0
 */
class EnquirySubscribe extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EnquirySubscribe the static model class
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
		return 'enquiry_subscribe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('enquiry, user', 'required'),
			array('enquiry, user', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, enquiry, user', 'safe', 'on'=>'search'),
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
			'user0' => array(self::BELONGS_TO, 'User', 'user'),
			'enquiry0' => array(self::BELONGS_TO, 'Enquiry', 'enquiry'),
		);
	}

	public function subscribeUser($enquiry_id, $user_id)
	{
		if(!$subscription=$this->findByAttributes(array('enquiry'=>$enquiry_id, 'user'=>$user_id))){
			$subscription = new EnquirySubscribe;
			$subscription->user=$user_id;
			$subscription->enquiry=$enquiry_id;
			$subscription->save();
			return 1;
		}
		return 0;		
	}

	public function isUserSubscribed($enquiry_id, $user_id)
	{
		if($this->findByAttributes(array('enquiry'=>$enquiry_id, 'user'=>$user_id)))
			return 1;
		return 0;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'enquiry' => 'Enquiry',
			'user' => 'User',
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
		$criteria->compare('enquiry',$this->enquiry);
		$criteria->compare('user',$this->user);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}

