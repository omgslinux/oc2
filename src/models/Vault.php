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
 * This is the model class for table "vault".
 *
 * The followings are the available columns in table 'vault':
 * @property integer $id
 * @property string $host
 * @property string $name
 * @property integer $type
 * @property string $schedule
 * @property string $created
 * @property integer $count
 * @property integer $capacity
 * @property integer $state
 *
 * The followings are the available model relations:
 * @property Backup[] $backups
 * @property VaultSchedule[] $vaultSchedules
 */

//Look into FILTER_SANITIZE_URL and CUrlValidator

class Vault extends CActiveRecord
{
	public $vaultDir;
	public $key='';

	public function init()
	{
		$this->vaultDir = Yii::app()->basePath.'/runtime/vaults/';
		if(!is_dir($this->vaultDir))
			createDirectory($this->vaultDir);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Vault the static model class
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
		return 'vault';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('host, type, schedule, created, state, capacity', 'required'),
			//array('host', 'url'),
			array('type, state, count', 'numerical', 'integerOnly'=>true),
			array('capacity', 'numerical', 'integerOnly'=>true, 'min'=>1),
			array('host', 'url'),
			array('key', 'length', 'max'=>32),
			array('schedule', 'length', 'is'=>7),
			array('key', 'validateKey', 'on'=>'newKey'),
			array('schedule', 'validateSchedule'),
		);
	}

	public function validateKey($attribute,$params)
	{
		if (!ctype_alnum($this->key) || strlen($this->key)!=32)
			$this->addError($attribute, __('Not a valid key'));
	}

	public function validateSchedule($attribute,$params)
	{
		if ($this->type == LOCAL && $this->schedule == '0000000')
			$this->addError($attribute, __('Please choose at least one day'));
	}

	public function beforeSave()
	{
		if($this->isNewRecord){
			$this->name = $this->host2VaultName($this->host);
			if(!is_dir($this->vaultDir.$this->name)){
				createDirectory($this->vaultDir.$this->name);
				if($this->type == LOCAL){
					$length = 32;
					$this->key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
					file_put_contents($this->vaultDir.$this->name.'/key.txt', $this->key);
				}
			}
		}
		// create vaultSchedules
		if($this->state >= READY && $this->type == LOCAL){
			if(!$this->vaultSchedules){
				$day = 0;
				while($day < 7){
					if($this->schedule[$day] == 1){
						$schedule = new VaultSchedule;
						$schedule->vault = $this->id;
						$schedule->day = $day;
						$schedule->save();
					}
					$day++;
				}
			}
		}
		return parent::beforeSave();
    }

	protected function beforeDelete()
	{
		if(file_exists($this->getVaultDir().'key.txt'))
			unlink($this->getVaultDir().'key.txt');

		rmdir($this->getVaultDir());
		return parent::beforeDelete();
	}


	public function host2VaultName($host, $appendType = 1)
	{
		$name = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $host);
		if($appendType){
			if($this->type == LOCAL)
				return $name.'local';
			else
				return $name.'remote';
		}else
			return $name;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'backups' => array(self::HAS_MANY, 'Backup', 'vault'),
			'vaultSchedules' => array(self::HAS_MANY, 'VaultSchedule', 'vault'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'host' => __('Host'),
			'type' => __('Type'),
			'schedule' => __('Schedule'),
			'created' => __('Created'),
			'count' => __('Count'),
			'state' => __('State'),
			'key' => __('Key'),
		);
	}

	public static function getHumanStates($state)
	{
		$humanStateValues=array(
				0		=>__('Created'),
				1		=>__('Verified'),
				2		=>__('Ready'),
				3		=>__('Loaded'),
				4		=>__('Busy'),
		);
		return $humanStateValues[$state];
	}

	public static function getHumanDays($day = Null)
	{
		$humanDayValues=array(
				0		=>__('Monday'),
				1		=>__('Tuesday'),
				2		=>__('Wednesday'),
				3		=>__('Thursday'),
				4		=>__('Friday'),
				5		=>__('Saturday'),
				6		=>__('Sunday'),
		);
		if($day !== Null)
			return $humanDayValues[$day];
		return $humanDayValues;
	}

	public static function getHumanType($type = Null)
	{
		if(!$type && isset($this) && is_object($this))
			$type = $this->type;
		if($type == 0)
			return __('Local');
		if($type == 1)
			return __('Remote');

		return __('Not defined');
	}

	public function afterFind()	// load key into newly found model
	{
		if(file_exists($this->vaultDir.$this->name.'/key.txt'))
			$this->key = file_get_contents($this->vaultDir.$this->name.'/key.txt');
	}

	public function saveKey()
	{
		file_put_contents($this->vaultDir.$this->name.'/key.txt', $this->key);
	}

	public function getAvailableSchedule()
	{
		$schedule = '0000000';
		foreach($this->findAll() as $vault){
			$day = 0;	// Monday
			while($day < 7){
				if($vault->schedule[$day] == 1)
					$schedule[$day] = 1;
				$day++;
			}
		}
		return $schedule;
	}

	public function getHumanSchedule()
	{
		$result='';
		$day=0;
		while($day < 7){
			if($this->schedule[$day] === '1')
				$result = $result.' '.$this->getHumanDays($day).',';
			$day++;
		}
		return rtrim($result,',');
	}

	public function getVaultDir()
	{
		return $this->vaultDir.$this->name.'/';
	}

	public function findByIncomingCreds($vaultType = LOCAL)
	{
		if(isset($_GET['vault']) && isset($_GET['key'])){
			$name = $_GET['vault'];
			$key = $_GET['key'];

			if($vaultType == LOCAL)
				$vaultName = $name.'local';
			if($vaultType == REMOTE)
				$vaultName = $name.'remote';
			if($model = Vault::model()->findByAttributes(array('name'=>$vaultName))){
				if($model->key && $model->key == $key)
					return $model;
			}
		}
		return Null;
	}

	public function isVaultFull()
	{
		$totalBackups = count(Backup::model()->findAllByAttributes(array('vault'=>$this->id)));
		if($totalBackups > $this->capacity)
			return 1;
		else
			return 0;
	}

	/* The most recent backup may have failed.
	 * Make the vault READY.
	 */
	public function makeReady()
	{
		$today = date('N')-1;
		if($mostRecentBackup = $this->getMostRecentBackup()){
			$datetime = new DateTime($mostRecentBackup->created);
			if($datetime->format('N')-1 != $today){
				$this->state = READY;
				$this->save();
			}
		}
	}

	public function getMostRecentBackup()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition('vault =:vault_id');
		$criteria->params[':vault_id'] = $this->id;
		$criteria->order = 'created DESC';
		$backups = Backup::model()->findAll($criteria);
		if($backups)
			return $backups[0];
		else
			return Null;
	}

	public function getOldestBackup()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition('vault =:vault_id');
		$criteria->params[':vault_id'] = $this->id;
		
		$criteria->order = 'created ASC';
		$backups = Backup::model()->findAll($criteria);
		if($backups)
			return $backups[0];
		else
			return Null;
	}
	
	public function deleteOldestBackup()
	{
		if(!$this->isVaultFull() || $this->type == REMOTE)
			return;
		if($oldestBackup = $this->getOldestBackup()){
			$confirmation=Null;
			$vaultName = $this->host2VaultName(Yii::app()->getBaseUrl(true), 0);
			$confirmation = @file_get_contents($this->host.'/vault/deleteBackup'.
								'?key='.$this->key.
								'&vault='.$vaultName.
								'&filename='.$oldestBackup->filename,
								false,
								$this->getStreamContext(3)
							);
			if($confirmation == 1)
				$oldestBackup->delete();
		}
	}

	public function getStreamContext($timeout = 1)
	{
		$opts = array('http' => array(
								'method'  => 'GET',
								'header'  => 'Content-type: application/x-www-form-urlencoded',
								'ignore_errors' => '1',
								'timeout' => $timeout,
								'user_agent' => 'ocax-'.getOCAXVersion(),
							));
		return stream_context_create($opts);
	}
}
