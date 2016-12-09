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


// email validation, password, salt, from: http://www.ghazalitajuddin.com/general/understand-yii-authentication-intemediat/

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $fullname
 * @property string $password
 * @property string $salt
 * @property string $email
 * @property string $language
 * @property string $joined
 * @property integer $activationcode
 * @property integer $is_active
 * @property integer $is_disabled
 * @property integer $is_socio
 * @property integer $is_description_editor
 * @property integer $is_team_member
 * @property integer $is_editor
 * @property integer $is_manager
 * @property integer $is_admin
 */
class User extends CActiveRecord
{
	public $new_password='';
	public $password_repeat='';
	public $smudge='░░░░░░░░░';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array ('username, activationcode, joined, password, salt,
					is_description_editor, is_team_member, is_editor, activationcode, joined,
					is_manager, is_admin, is_active, is_disabled', 'unsafe', 'on'=>'update_user'),
			array ('username, activationcode, joined, salt,
					is_description_editor, is_team_member, is_editor,
					is_manager, is_admin, is_active, is_disabled', 'unsafe', 'on'=>'change_password'),
			array ('username, activationcode, joined, password, salt,
					fullname, email, language', 'unsafe', 'on'=>'update_roles'),

			array('username, fullname, password, salt, email, activationcode', 'required'),
			array('is_socio, is_description_editor, is_team_member, is_editor,
					is_manager, is_admin, is_active, is_disabled', 'numerical',
					'integerOnly'=>true),
					
			array('username, fullname, password, salt, email', 'length', 'max'=>128),
			array('email', 'email', 'except' => 'opt_out', 'allowEmpty'=>false),
			array('email', 'unique', 'except' => 'opt_out', 'className' => 'User'),

			array('new_password, password_repeat', 'required', 'on'=>'change_password'),
			array('new_password', 'length', 'min' => 6,
				    'tooShort'=>Yii::t("translation", "{attribute} es muy corta (6 carácteres min)."),
				    'tooLong'=>Yii::t("translation", "{attribute} is too long."),
					'on'=>'change_password'),
			array('password_repeat', 'compare', 'on'=>'change_password', 'compareAttribute'=>'new_password'),
			array('password_repeat', 'safe'),
			
			array('language', 'length', 'max'=>2),

			array('username, email', 'safe', 'on'=>'search'),
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
			//'blockUsers' => array(self::HAS_MANY, 'BlockUser', 'user'),
			//'blockUsers1' => array(self::HAS_MANY, 'BlockUser', 'blocked_user'),
			//'newsletters' => array(self::HAS_MANY, 'Newsletter', 'sender'),
			'comments' => array(self::HAS_MANY, 'Comment', 'user'),
			'enquirys' => array(self::HAS_MANY, 'Enquiry', 'user'),
			'assignedEnquiries' => array(self::HAS_MANY, 'Enquiry', 'team_member'),
			'managedEnquiries' => array(self::HAS_MANY, 'Enquiry', 'manager'),
			'enquirySubscribes' => array(self::HAS_MANY, 'EnquirySubscribe', 'user'),
			'emails' => array(self::HAS_MANY, 'Email', 'sender'),
			'replys' => array(self::HAS_MANY, 'Reply', 'team_member'),
			'votes' => array(self::HAS_MANY, 'Vote', 'user'),
			'resetPasswords' => array(self::HAS_MANY, 'ResetPassword', 'user'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'username' => __('Username'),
			'fullname' => __('Full name'),
			'password' => __('Password'),
			'new_password' => __('New password'),
			'password_repeat' => __('Repeat password'),
			'email' => __('Email'),
			'language' => __('Language'),
			'joined' => __('Joined'),
			'is_active' => __('Active'),
			'is_socio' => __('Is partner'),
			'is_description_editor' => __('Budget description editor'),
			'is_team_member' => 'Team Member',
			'is_editor' => __('Page editor'),
			'is_manager' => 'Team Manager',
			'is_admin' => 'Admin',
		);
	}

	public function getTeamMembers()
	{
		return $this->findAll(array("condition"=>"is_team_member =  1 AND is_active = 1",
									"order"=>"username")
							);
	}
	
	/**
	 * Create activation code.
	 * @param string email
	 */
	public function generateActivationCode()
	{
		$code = substr(md5(rand(0, 1000000)), 0, 45);
		while ($this->findByAttributes(array('activationcode'=>$code)))
			$code = substr(md5(rand(0, 1000000)), 0, 45);
		$this->activationcode = $code;
		return $code;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @param string salt
	 * @return string hash
	 */
	public function hashPassword($password,$salt)
	{
		return md5($salt.$password);
	}

	/**
	 * Generates a salt that can be used to generate a password hash.
	 * @return string the salt
	 */
	public function generateSalt()
	{
		return uniqid('',true);
	}

	public function enableUser()
	{
		$this->is_active = 1;
		$this->is_disabled = 0;
		$this->save();
		Log::model()->write('User', __('User').' id='.$this->id.' "'.$this->username.'" '.__('enabled'), $this->id);
	}

	public function disableUser()
	{
		$this->is_active = 0;
		$this->is_disabled = 1;
		$this->save();
		Log::model()->write('User', __('User').' id='.$this->id.' "'.$this->username.'" '.__('disabled'), $this->id);
	}

	public function smudgeUser()
	{
		$this->is_active = 0;
		$this->is_disabled = 1;
		$this->username = $this->smudge;
		$this->fullname = $this->smudge;
		$this->email = $this->smudge;
		$this->scenario = 'opt_out';
		$this->save();
		Log::model()->write('User',__('User').' '.$this->username.' id='.$this->id.' '.__('deleted account'),$this->id);
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
		$criteria->addCondition('username != "'.$this->smudge.'"');

		$criteria->compare('username',$this->username,true);
		$criteria->compare('fullname',$this->fullname,true);
		$criteria->compare('email',$this->email,true);
		//$criteria->compare('joined',$this->joined,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('is_disabled',$this->is_disabled);
		$criteria->compare('is_socio',$this->is_socio);
		$criteria->compare('is_team_member',$this->is_team_member);
		$criteria->compare('is_editor',$this->is_editor);
		$criteria->compare('is_manager',$this->is_manager);
		$criteria->compare('is_admin',$this->is_admin);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
