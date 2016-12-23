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
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */

	public $username;
	public $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }


	public function authenticate()
	{
		$user = User::model()->findByAttributes(array('username'=>$this->username));
		if(!$user)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else{
			if( $user->is_disabled )
				$this->errorCode=self::ERROR_USERNAME_INVALID;
				
			elseif( $user->hashPassword($this->password,$user->salt) === $user->password )
				$this->errorCode=self::ERROR_NONE;
				
			else
				$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
		return !$this->errorCode;
	}

	public static function createAuthenticatedIdentity($username) {
		$identity=new self($username,'');
		$identity->username=$username;
		$identity->errorCode=self::ERROR_NONE;
		return $identity;
	}

}
