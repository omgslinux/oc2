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
 * This is the model class for table "backup".
 *
 * The followings are the available columns in table 'backup':
 * @property integer $id
 * @property integer $vault
 * @property string $filename
 * @property string $created
 * @property string $initiated
 * @property string $completed
 * @property string $filesize
 * @property integer $state
 *
 * The followings are the available model relations:
 * @property Vault $vault0
 */

use Clouddueling\Mysqldump\Mysqldump;

class Backup extends CActiveRecord
{
	public $filenamePrefix = 'backup-';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Backup the static model class
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
		return 'backup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vault, created', 'required'),
			array('vault, state', 'numerical', 'integerOnly'=>true),
			array('filename, filesize', 'length', 'max'=>255),
			array('initiated, completed', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, vault, filename, initiated, completed, filesize', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'vault' => __('Vault'),
			'filename' => __('Filename'),
			'created' => __('Created'),
			'initiated' => __('Initiated'),
			'completed' => __('Completed'),
			'filesize' => __('Filesize'),
			'state' => __('State'),
		);
	}

	public function getHumanState()
	{
		if($this->state === 0)
			return '<span style="color:red">'.__('Failed').'</span>';
		if($this->state == 1)
			return __('Success');
		return __('Not finished');
	}

	public function fileSizeForHumans($precision = 2) {
		$bytes= $this->filesize;
		return bytesForHumans($this->filesize);
	}

	public function getDataproviderByVault($vault_id)
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition('vault = :vault_id');
		$criteria->params[':vault_id'] = $vault_id;
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			//'sort'=>array('defaultOrder'=>'created DESC'),
		));
	}

	public function buildBackupFile()
	{
		list($path, $file, $dump_error) = $this->siteBackup($this->vault0->getVaultDir());
		if($dump_error)
			return Null;
		$this->filename = $file;
		return 1;
	}

	public function siteBackup($backupDir)
	{
		$error='';
		$backupFileName = $this->filenamePrefix.date('d-m-Y-H-i-s').'.zip';

		$zip = new ZipArchive();
		if (!$zip->open($backupDir.$backupFileName, ZIPARCHIVE::CREATE))
			return array(null, null, __('Cannot create zip file'));

		$dump_file = $backupDir.date('d-m-Y-H-i-s').'-backup.sql';
		$error = $this->dumpDatabase($dump_file);

		$filesDir = dirname(Yii::app()->request->scriptFile).'/files';

		$this->Zip($filesDir, $zip);
		$zip->addFile($dump_file, 'database.sql');
		$zip->addFile(Yii::app()->basePath.'/data/RESTORE','RESTORE');
		$zip->addFile(Yii::app()->basePath.'/data/ocax.version','VERSION');
		$zip->close();

		if(file_exists($dump_file))
			unlink($dump_file);

		return array($backupDir, $backupFileName, $error);
	}

	public function dumpDatabase($filePath, $table=Null)
	{
		$params = getMySqlParams();
		$method = Config::model()->findByPk('databaseDumpMethod')->value;
		switch ($method) {
			case 'native':
				$output = NULL;
				$return_var = NULL;
				//$command =	'mysqldump --user='.$params['user'].' --password='.$params['pass'].
				//			' --host='.$params['host'].' '.$params['dbname'].' '.$table.' > '.$filePath;
				$command =  'mysqldump --user='.$params['user'].' --password=\''.$params['pass'].'\''.
							' --host='.$params['host'].' '.$params['dbname'].' '.$table.' > '.$filePath;
				exec($command, $output, $return_var);
				if($return_var)
					return 'exec(mysqldump) returned:'.$return_var;
				break;
			case 'alternative':
				Yii::import('application.extensions.Clouddueling.Mysqldump.*');
				require_once('Mysqldump.php');
				$dumpSettings = array();
				if($table)
					$dumpSettings = array('include-tables' => array($table));
				$dump =  new Mysqldump($params['dbname'], $params['user'], $params['pass'], $params['host'], 'mysql', $dumpSettings);
				$dump->start($filePath);
				// need to find a way to return errors here
				break;
			default:
				return '\''.$method.'\' is not a valid database dump method';
		}
		return 0;	// dumped ok.
	}
	
	
	// http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php/1334949#1334949
	private function Zip($source, $zip)
	{
		if (is_dir($source) === true){
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			$zip->addEmptyDir('files/');
			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);
				// Ignore "." and ".." folders
				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
					continue;

				$file = realpath($file);

				if (is_dir($file) === true)
					$zip->addEmptyDir(str_replace($source . '/', 'files/', $file . '/'));
				else if (is_file($file) === true)
					$zip->addFromString(str_replace($source . '/', 'files/', $file), file_get_contents($file));
			}
		}
		else if (is_file($source) === true)
			$zip->addFromString(basename($source), file_get_contents($source));
	}

	public function download()
	{
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$this->filename."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$this->filesize);
		ob_end_flush();
		@readfile($this->vault0->getVaultDir().$this->filename);
		exit;
	}


	public function findByDay($day, $vault)
	{
		//$day = YYY-MM-DD
		//$sql = "SELECT * FROM tablename WHERE columname BETWEEN '".$day." 00:00:00' AND '".$day." 23:59:59'";
		$criteria=new CDbCriteria;
		$criteria->compare('vault', $vault);
		// http://www.yiiframework.com/forum/index.php/topic/25588-cdbcriteria-addbetweencondition-with-param-binding/
		$criteria->addBetweenCondition('created', $day.' 00:00:00', $day.' 23:59:59');
		return $this->find($criteria);
	}

	protected function beforeDelete()
	{
		if(file_exists($this->vault0->getVaultDir().$this->filename))
			unlink($this->vault0->getVaultDir().$this->filename);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('vault',$this->vault);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('initiated',$this->initiated,true);
		$criteria->compare('completed',$this->completed,true);
		$criteria->compare('filesize',$this->filesize,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
