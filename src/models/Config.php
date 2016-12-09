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
 * This is the model class for table "config".
 *
 * The followings are the available columns in table 'config':
 * @property string $parameter
 * @property string $value
 * @property string $description
 */
class Config extends CActiveRecord
{
	/* If any of these return 0, then
	 * $this->findByPk('siteConfigStatus') is set to 1 and
	 * config/pendingConfiguraton (aka Admin Tasks) is rendered on user/panel
	 */
	private $autoCheckParams = array(
						'siteConfigStatusLanguage',
						'siteConfigStatusEmail',
						'siteConfigStatusInitials',
						'siteConfigStatusObservatoryName',
						'siteConfigStatusAdministrationName',
						'siteConfigStatusBudgetDescriptionsImport',
						'siteConfigStatusEmailTemplates',
						'siteConfigStatusUptodate',
					);
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Config the static model class
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
		return 'config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value', 'required', 'on'=>'cannotBeEmpty'),
			array('parameter, description', 'required'),
			array('value', 'length', 'max'=>255),
			array('value','validateLanguage', 'on'=>'language'),
			array('value', 'url', 'on'=>'URL', 'allowEmpty'=>true),
			array('value','validateCurrenyCollocation', 'on'=>'currenyCollocation'),
			array('value','validateSiteColor', 'on'=>'siteColor'),
			array('value', 'email', 'on'=>'email', 'allowEmpty'=>false),
			array('value', 'numerical', 'on'=>'positiveNumber', 'allowEmpty'=>false, 'min'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('parameter, value, description', 'safe', 'on'=>'search'),
		);
	}

	public function validateLanguage($attribute,$params)
	{
		if($this->$attribute === ""){
				$this->addError($attribute, __('Please define a language'));
				return;
		}
		$available_langs = Yiibase::getPathOfAlias('application.messages');
		$languages = explode(',', $this->$attribute);
		foreach($languages as $language){
			if(!is_dir($available_langs.'/'.$language)){
				$this->addError($attribute, $language.' '.__('is not a valid language.'));
			}
		}
	}

	public function validateCurrenyCollocation($attribute,$params)
	{
		if($this->$attribute === ""){
				$this->addError($attribute, __('Missing value'));
				return;
		}
		$this->$attribute = trim($this->$attribute);
		if(stristr($this->$attribute, 'n') === FALSE) {
			$this->addError($attribute, __("Character 'n' is missing."));
		}
	}

	public function validateSiteColor($attribute,$params)
	{
		$color = $this->$attribute;
		if(!(preg_match('/^[a-f0-9]{6}$/i', $color) && strlen($color)==6))
			$this->addError($attribute, __("Expecting a 6 digit web color"));
		return;
	}

	/*
	 * These are initial configuration parameters.
	 * When OCAx is installed, they are set to 0
	 * Admin user is alerted views/user/panel
	 * Alert is removed when these params have been set.
	 */ 
	protected function afterSave()
	{
		if($this->parameter == 'languages'){
			$record = $this->findByPk('siteConfigStatusLanguage');
			$record->value = 1;
			$record->save();
			return $this->_updateSiteConfigurationStatus();
		}
		elseif($this->parameter == 'siglas'){
			$record = $this->findByPk('siteConfigStatusInitials');
			$record->value = 1;
			$record->save();
			return $this->_updateSiteConfigurationStatus();
		}
		elseif($this->parameter == 'observatoryName1' || $this->parameter == 'observatoryName2'){
			$record = $this->findByPk('siteConfigStatusObservatoryName');
			$record->value = 1;
			$record->save();
			return $this->_updateSiteConfigurationStatus();
		}
		elseif($this->parameter == 'administrationName'){
			$record = $this->findByPk('siteConfigStatusAdministrationName');
			$record->value = 1;
			$record->save();
			return $this->_updateSiteConfigurationStatus();
		}
	}

	private function _updateSiteConfigurationStatus()
	{
		$siteConfigStatus = $this->findByPk('siteConfigStatus');

		$sql = "SELECT COUNT(*) FROM budget_desc_common";
		if(intval(Yii::app()->db->createCommand($sql)->queryScalar()) != 0){
			$param = $this->findByPk('siteConfigStatusBudgetDescriptionsImport');
			if($param->value != 1){
				$param->value =1;
				$param->save();
			}
		}
		foreach($this->autoCheckParams as $p){
			if($this->findByPk($p)->value == 0){
				$siteConfigStatus->value=0;
				$siteConfigStatus->save();
				return 0;
			}
		}
		$siteConfigStatus->value=1;
		$siteConfigStatus->save();
		return 1;
	}

	public function updateSiteConfigurationStatus($param=Null, $value=Null)
	{
		if($param !==Null && $value !== NUll){
			$record = $this->findByPk($param);
			$record->value = $value;
			$record->save();
		}
		return $this->_updateSiteConfigurationStatus();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'parameter' => __('Parameter'),
			'value' => __('Value'),
			'description' => __('Description'),
		);
	}

	public function getSiteTitle()
	{
		$title=str_replace('%s', '<span id="observatoryName2">'.$this->findByPk('observatoryName2')->value.'</span>', $this->findByPk('observatoryName1')->value);
		return str_replace('#', '<br />', $title);
	}

	public function getObservatoryName()
	{
		$title = str_replace('%s', $this->findByPk('observatoryName2')->value, $this->findByPk('observatoryName1')->value);
		$title = str_replace('#', ' ', $title);
		return strip_tags($title);
	}

	public function getObservatoryInitials()
	{
		return $this->findByPk('siglas')->value;
	}


	public function getSiteColor()
	{
		if($color = Config::model()->findByPk('siteColor'))
			return $color->value;
		return 'a1a150';
	}

	public function isSiteMultilingual()
	{
		$languages=explode(',', $this->findByPk('languages')->value);
		if(isset($languages[1]))
			return 1;
		return 0;
	}

	public function isSocialNonFree()
	{
		if(Config::model()->findByPk('schemaVersion')->value == 0)
			return 0;
		return Config::model()->findByPk('socialActivateNonFree')->value;
	}

	public function updateVersionInfo(){
		$context = stream_context_create(array(
			'http' => array(
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'method' => 'GET',
			'timeout' => 1,
		)));
		if($result = @file_get_contents('http://network.ocax.net/current/version', 0, $context)){
			$new_version = json_decode($result);
			if(isset($new_version->ocax)){
				$this->setLatestOCAXVersion($new_version->ocax);
				return $new_version->ocax;
			}
		}
		return $this->getOCAXVersion();
	}

	public function getOCAXVersion(){
		$path = Yii::app()->basePath.'/data/ocax.version';
		$handle = @fopen($path, "r");
		$version = rtrim(fgets($handle),"\n");
		fclose($handle);
		return $version;
	}

	public function getLatestOCAXVersion(){
		$path = Yii::app()->basePath.'/runtime/latest.ocax.version';
		if (file_exists($path)) {
			$handle = @fopen($path, "r");
			$version = rtrim(fgets($handle),"\n");
			fclose($handle);
			return $version;
		}else{
			$context = stream_context_create(array(
				'http' => array(
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'method' => 'GET',
				'timeout' => 1,
			)));
			if($result = @file_get_contents('http://network.ocax.net/current/version', 0, $context)){
				$new_version = json_decode($result);
				if(isset($new_version->ocax)){
					$this->setLatestOCAXVersion($new_version->ocax);
					return $new_version->ocax;
				}
			}
		}
		return $this->getOCAXVersion();
	}

	public function setLatestOCAXVersion($version){
		$version = trim($version);
		file_put_contents(Yii::app()->basePath.'/runtime/latest.ocax.version', $version);
	}

	public function isOCAXUptodate(){
		$installed_version = $this->getOCAXVersion();
		$installed_version = str_replace('.','',$installed_version );
		$installed_version = str_pad($installed_version, 10 , '0');

		$latest_version = $this->getLatestOCAXVersion();
		$latest_version = str_replace('.','',$latest_version );
		$latest_version = str_pad($latest_version, 10 , '0');
		if($latest_version > $installed_version)
			return 0;
		return 1;
	}
	
	public function isZipFileUpdated($state = Null){
		$zipStatus = $this->findByPk('siteConfigStatusZipFileUpdated');
		if($state !== Null){
			$zipStatus->value = $state;
			$zipStatus->save();
			return $zipStatus->value;
		}
		return $zipStatus->value;
	}
}
