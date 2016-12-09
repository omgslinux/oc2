<?php

/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2014 OCAX Contributors. See AUTHORS.

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
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property integer $id
 * @property string $model		// enquiry, reply, etc. anything we want to comment on
 * @property integer $model_id
 * @property integer $thread_position
 * @property string $created
 * @property integer $user
 * @property string $body
 *
 * The followings are the available model relations:
 * @property User $user0
 */
class Comment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model, model_id, created, user, body', 'required'),
			array('model_id, user, thread_position', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('created, user, body', 'safe', 'on'=>'search'),
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
		);
	}

	public function beforeSave()
	{
		$counterModel = $this->model;
		$counterModel_id = $this->model_id;
		$criteria = new CDbCriteria;
		$criteria->condition = 'model = :model AND model_id = :model_id';
		$criteria->params[':model'] = $this->model;
		$criteria->params[':model_id'] = $this->model_id;
		
		$counter = CommentCount::model()->find($criteria);
		if(!$counter){
			$counter = new CommentCount;
			$counter->model=$this->model;
			$counter->model_id=$this->model_id;
			$counter->thread_count=1;
		}else			
			$counter->thread_count +=1;
		$counter->save();
		
		$this->thread_position = $counter->thread_count;
		
		$this->body = preg_replace(
		 array(
		   '/(^|\s|>)(www.[^<> \n\r]+)/iex',
		   '/(^|\s|>)([_A-Za-z0-9-]+(\\.[A-Za-z]{2,3})?\\.[A-Za-z]{2,4}\\/[^<> \n\r]+)/iex',
		   '/(?(?=<a[^>]*>.+<\/a>)(?:<a[^>]*>.+<\/a>)|([^="\']?)((?:https?):\/\/([^<> \n\r]+)))/iex'
		 ),  
		 array(
		   "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>&nbsp;\\3':'\\0'))",
		   "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>&nbsp;\\4':'\\0'))",
		   "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\" target=\"_blank\">\\3</a>&nbsp;':'\\0'))",
		 ),  
		 $this->body
		);

		
		return parent::beforeSave();
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created' => __('Sent'),
			'user' => __('User'),
			'body' => __('Comment'),
		);
	}

	public function belongsToEnquiry()
	{
		if($this->model == 'Enquiry' || $this->model == 'Reply'){
			if($this->model == 'Reply'){
				$reply = Reply::model()->findByPk($this->model_id);
				if (!$reply){
					throw new CHttpException(404,'The requested Reply does not exist.');
				}
				return $reply->enquiry0;
			}
			if($this->model == 'Enquiry')
				$enquiry = Enquiry::model()->findByPk($this->model_id);
				if (!$enquiry){
					throw new CHttpException(404,'The requested Enquiry does not exist.');
				}
				return $enquiry;
		}
		return Null;
	}

	public function isModerator($user_id)
	{
		if($enquiry = $this->belongsToEnquiry()){
			if($enquiry->teamMember->id == $user_id)
				return 1;
		}
		return 0;
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
		$criteria->compare('user',$this->user);
		$criteria->compare('body',$this->body,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
