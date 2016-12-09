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
 * This is the model class for table "reply".
 *
 * The followings are the available columns in table 'reply':
 * @property integer $id
 * @property integer $enquiry
 * @property string $created
 * @property integer $team_member
 * @property string $body
 *
 * The followings are the available model relations:
 * @property Enquiry $enquiry0
 * @property User $teamMember
 * @property Vote[] $votes
 */
class Reply extends CActiveRecord
{

	public $state;	// used to get the Enquiry state from reply/create form

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Reply the static model class
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
		return 'reply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('enquiry, created, team_member, body', 'required'),
			array('enquiry, team_member', 'numerical', 'integerOnly'=>true),
			array('created', 'date', 'allowEmpty'=>false, 'format'=>'yyyy-M-d'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('enquiry, created, team_member, body', 'safe', 'on'=>'search'),
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
			'enquiry0' => array(self::BELONGS_TO, 'Enquiry', 'enquiry'),
			'teamMember' => array(self::BELONGS_TO, 'User', 'team_member'),
			'votes' => array(self::HAS_MANY, 'Vote', 'reply'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'enquiry' => __('Enquiry'),
			'created' => __('Date of reply'),
			'team_member' => 'Team Member',
			'body' => __('Body'),
		);
	}

	protected function beforeDelete()
	{
		foreach($this->votes as $vote)
			$vote->delete();
		$comments = Comment::model()->findAllByAttributes(array('model'=>'Reply','model_id'=>$this->id));
		foreach($comments as $comment)
			$comment->delete();
		$files = File::model()->findAllByAttributes(array('model'=>'Reply','model_id'=>$this->id));
		foreach($files as $file)
			$file->delete();
		return parent::beforeDelete();
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

		$criteria->compare('enquiry',$this->enquiry);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('team_member',$this->team_member);
		$criteria->compare('body',$this->body,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
		
	}
}
