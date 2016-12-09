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
 * RegisterForm class.
 * RegisterForm is the data structure for keeping
 * user login form data. It is used by the 'register' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
	public $username;
	public $fullname;
	public $password;
	public $password_repeat;
	public $joined;
	public $activationcode;
	public $activationstatus;
	public $salt;
	public $email;
	public $verifyCode;
	public $is_socio;
	
	private $_identity;
 
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
		// username, password, email are required
		array('username, fullname, password , password_repeat, email, verifyCode', 'required'),
		// username and email should be unique
		array('username, email', 'unique', 'className' => 'User'),
		array('username', 'validateUsername'),
		// email should be in email format
		array('email', 'email'),
		array('is_socio', 'boolean'),
		array('password', 'length', 'min' => 6, 
			    'tooShort'=>Yii::t("translation", "{attribute} es muy corta (6 carácteres min)."),
			    'tooLong'=>Yii::t("translation", "{attribute} is too long.")),
		array('password_repeat', 'compare', 'compareAttribute'=>'password'),
		array('password_repeat', 'safe'),
		array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}
 
    public function validateUsername()
    {
		if (strlen($this->username) < 4){
			return;
		}
		if (strlen($this->username) > 32){
			$this->addError('username','Username too long. Max 32 characters');
			return;
		}
        if (!preg_match('/^[A-Za-z0-9_]+$/', $this->username))
            $this->addError('username','Only characters a-z A-Z and 0-9 are allowed.');
    }

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		'username'=> __('Username'),
		'fullname'=> __('Full name'),
		'password'=> __('Password'),
		'password_repeat'=> __('Repeat password'),
		'email'=> __('Email'),
		'verifyCode'=>'Captcha',
		);
	}
 
	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	// what?? no it isn't !!!???? Look into this.
/*
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Usuario o constraseña incorrecta.');
		}
	}
*/
}
