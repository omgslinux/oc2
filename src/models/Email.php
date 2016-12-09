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
 * This is the model class for table "email".
 *
 * The followings are the available columns in table 'email':
 * @property integer $id
 * @property integer $type
 * @property string $created
 * @property integer $sent
 * @property string $title
 * @property integer $sender
 * @property string $sent_as
 * @property string $recipients
 * @property integer $enquiry
 * @property string $body
 *
 * The followings are the available model relations:
 * @property User $sender0
 * @property Enquiry $enquiry0
 */
class Email extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Email the static model class
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
		return 'email';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created, title, sent_as, recipients, enquiry, body', 'required'),
			array('sent, enquiry', 'numerical', 'integerOnly'=>true),
			array('sender, type', 'safe'),
			array('title', 'length', 'max'=>255),
			array('sent_as', 'email'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('created, sent, title, sender, sent_as, enquiry, body', 'safe', 'on'=>'search'),
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
			'sender0' => array(self::BELONGS_TO, 'User', 'sender'),
			'enquiry0' => array(self::BELONGS_TO, 'Enquiry', 'enquiry'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => __('Type'),
			'created' => __('Created'),
			'sent' => __('Sent'),
			'title' => __('Title'),
			'sender' => __('Sender'),
			'sent_as' => __('Sent as'),
			'recipients' => __('Recipients'),
			'enquiry' => __('Enquiry'),
			'body' => __('Body'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	/*
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('sent',$this->sent);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('sender',$this->sender);
		$criteria->compare('sent_as',$this->sent_as,true);
		$criteria->compare('recipients',$this->recipients,true);
		$criteria->compare('enquiry',$this->enquiry);
		$criteria->compare('body',$this->body,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	*/
}
