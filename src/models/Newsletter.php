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
 * This is the model class for table "newsletter".
 *
 * The followings are the available columns in table 'newsletter':
 * @property integer $id
 * @property string $created
 * @property string $published
 * @property integer $sent 0 = draft, 1 = failed, 2 = sent
 * @property integer $sender
 * @property string $sent_as
 * @property string $recipients
 * @property string $subject
 * @property string $body
 *
 * The followings are the available model relations:
 * @property User $sender0
 */
class Newsletter extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Newsletter the static model class
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
		return 'newsletter';
	}

	public static function getHumanSentValues($state)
	{
    	$humanValues=array(
						0=>__('Draft'),
						1=>__('Failed to send'),
						2=>__('Sent OK'),
					);
		return $humanValues[$state];
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created, sender, sent_as, subject, body', 'required'),
			array('recipients', 'required', 'on'=>'send'),
			array('sent, sender', 'numerical', 'integerOnly'=>true),
			array('sent_as', 'length', 'max'=>128),
			array('subject', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('created, sent, published, subject', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created' => __('Created'),
			'published' => __('Published'),
			'sent' => __('State'),
			'sender' => 'Sender',
			'sent_as' => 'Sent As',
			'recipients' => 'Recipients',
			'subject' => __('Subject'),
			'body' => __('Body'),
		);
	}

	public function getNewslettersForRSS()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition('published IS NOT NULL');
		$criteria->order = 'published DESC';
		$criteria->limit = '20';
		
		return $this->findAll($criteria);
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

		$criteria->compare('created',$this->created,true);
		$criteria->compare('published',$this->published,true);
		$criteria->compare('sent',$this->sent);
		//$criteria->compare('sender',$this->sender);
		//$criteria->compare('sent_as',$this->sent_as,true);
		//$criteria->compare('recipients',$this->recipients,true);
		$criteria->compare('subject',$this->subject,true);
		//$criteria->compare('body',$this->body,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'created DESC'),
		));
	}
}
