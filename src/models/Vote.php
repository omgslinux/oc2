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
 * This is the model class for table "vote".
 *
 * The followings are the available columns in table 'vote':
 * @property integer $id
 * @property integer $reply
 * @property integer $user
 * @property integer $vote
 *
 * The followings are the available model relations:
 * @property User $user0
 * @property Reply $reply0
 */
class Vote extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Vote the static model class
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
		return 'vote';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reply, user, vote', 'required'),
			array('reply, user, vote', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('reply, user, vote', 'safe', 'on'=>'search'),
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
			'reply0' => array(self::BELONGS_TO, 'Reply', 'reply'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'reply' => __('Reply'),
			'user' => __('User'),
			'vote' => __('Vote'),
		);
	}

	public function getTotal($reply, $vote)
	{
		$criteria = new CDbCriteria;
		$criteria->condition = 'reply = :reply AND vote = :vote';
		$criteria->params[":reply"] = $reply;
		$criteria->params[":vote"] = $vote;		
		return count($this->findAll($criteria));
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
		$criteria->compare('reply',$this->reply);
		$criteria->compare('user',$this->user);
		$criteria->compare('vote',$this->vote);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
*/

}
