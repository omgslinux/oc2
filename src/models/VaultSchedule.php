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
 * This is the model class for table "vault_schedule".
 *
 * The followings are the available columns in table 'vault_schedule':
 * @property integer $id
 * @property integer $vault
 * @property integer $day
 *
 * The followings are the available model relations:
 * @property Vault $vault0
 */

class VaultSchedule extends CActiveRecord
{
	/**
	 * $backupHour = 18
	 * $backupWindow = 3
	 * We will run VaultSchedule between 18:00 and 21:00hrs
	 * 
	 * Warning! $backupHour+$backupWindow cannot be equal to or greater than 24
	 * 
	 */
	public $backupHour = 1;	// 24hr When to start backup proceedure
	public $backupWindow = 22;	// period (in hours) to respond to backup proceedure.

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return VaultSchedule the static model class
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
		return 'vault_schedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vault, day', 'required'),
			array('vault, day', 'numerical', 'integerOnly'=>true),
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
			'vault0' => array(self::BELONGS_TO, 'Vault', 'vault'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'	=> 'ID',
			'vault'	=> 'Vault',
			'day'	=> 'Day',
		);
	}

	/**
	 * LOCAL vault initiates the backup process
	 */
	public function runVaultSchedule()
	{
		$hour = date('G');
		if($hour < $this->backupHour || $hour > $this->backupHour+$this->backupWindow )
			return;
		if(!Config::model()->findByPk('siteAutoBackup')->value)
			return;

		$today = date('N')-1;
		if($schedule = $this->findByAttributes(array('day'=>$today))){
			if($schedule->vault0->state != READY)
				$schedule->vault0->makeReady();
				
			if($schedule->vault0->state == READY){
		
				if($schedule->vault0->isVaultFull()){
					$schedule->vault0->deleteOldestBackup();
					return;
				}
				
				// only backup each vault once per day
				if(Backup::model()->findByDay(date('Y-m-d'), $schedule->vault0->id ))
					return;

				$vaultName = $schedule->vault0->host2VaultName(Yii::app()->getBaseUrl(true), 0);
				@file_get_contents($schedule->vault0->host.'/vault/localWaitingToStartCopyingBackup'.
														'?key='.$schedule->vault0->key.
														'&vault='.$vaultName,
														false,
														$schedule->vault0->getStreamContext(1)
									);
			}
		}
		return;
	}
}
