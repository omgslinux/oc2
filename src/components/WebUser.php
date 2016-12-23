<?php

/**
OCAX -- Citizen driven Observatory software
Copyright (C) 2014 OCAX Contributors. See AUTHORS.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// http://www.yiiframework.com/wiki/60/


class WebUser extends CWebUser {

	// Store model to not repeat query.
	private $_model;

	function getUserID()
	{
		if(Yii::app()->user->isGuest)
			return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		return intval($user->id);
	}

	function getFullname()
	{
		if(Yii::app()->user->isGuest)
			return '';
		$user = $this->loadUser(Yii::app()->user->id);
		return $user->fullname;
	}

	// access it by Yii::app()->user->isAdmin()
	function isPrivileged()
	{
		if(Yii::app()->user->isGuest)
			return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		if($user->is_team_member)
			return 1;
		if($user->is_editor)
			return 1;
		if($user->is_manager)
			return 1;
		if($user->is_admin)
			return 1;
		return 0;
	}

	function canEditBudgetDescriptions()
	{
		if(Yii::app()->user->isGuest)
			return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		if($user->is_admin)
			return 1;
		return intval($user->is_description_editor);
	}

	function isTeamMember()
	{
		if(Yii::app()->user->isGuest)
			return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		return intval($user->is_team_member);
	}

	function isEditor()
	{
		if(Yii::app()->user->isGuest)
			return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		return intval($user->is_editor);
	}

	function isManager()
	{
		if(Yii::app()->user->isGuest)
		return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		return intval($user->is_manager);
	}

	// access it by Yii::app()->user->isAdmin()
	function isAdmin()
	{
		if(Yii::app()->user->isGuest)
			return 0;
		$user = $this->loadUser(Yii::app()->user->id);
		return intval($user->is_admin);
	}

	// Load user model.
	protected function loadUser($id=null)
	{
		if($this->_model===null){
			if($id!==null)
				$this->_model = User::model()->findByAttributes(array('username'=>$id));
		}
		return $this->_model;
	}
}
?>
