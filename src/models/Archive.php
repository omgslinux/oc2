<?php

/**
 * This is the model class for table "archive".
 *
 * The followings are the available columns in table 'archive':
 * @property integer $id
 * @property integer $is_container
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property integer $author
 * @property string $description
 * @property integer $container
 * @property string $created
 *
 * The followings are the available model relations:
 * @property User $author0
 */
class Archive extends CActiveRecord
{
	
	public $baseDir;
	public $archiveRoot = '/files/archive/';
	public $file;
	public $searchText=Null;

	public function init()
	{
		$this->baseDir = dirname(Yii::app()->request->scriptFile);
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Archive the static model class
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
		return 'archive';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, path, author, description, created', 'required', 'on'=>'uploadFile'),
			array('name, path, author, description, created', 'required', 'on'=>'createContainer'),
			array('name, description', 'required', 'on'=>'update'),
			array('author, container', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>2000),
			array('name, path', 'length', 'max'=>255),
			array('extension', 'length', 'max'=>5),
			// The following rule is used by search().
			array('searchText', 'safe', 'on'=>'search'),
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
			'author0' => array(self::BELONGS_TO, 'User', 'author'),
			'container0' => array(self::BELONGS_TO, 'Archive', 'container'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'file' => __('File'),
			'name' => __('Name'),
			'path' => __('Path'),
			'extension' => 'Extension',
			'author' => __('Author'),
			'description' => __('Description'),
			'created' => __('Created'),
			'searchText' => __('Search'),
		);
	}

/*
	protected function beforeSave()
	{
		if (! file_exists($this->getURI()) ){	// let's make sure the file/dir actually exists
			return 0;
		}
		return parent::beforeSave();
	}
*/

	protected function beforeDelete()
	{
		if (file_exists($this->getURI())){
			unlink($this->getURI());
		}
		return parent::beforeDelete();
	}

	public function doesContainerExist()
	{
		if ($this->findAllByAttributes(array('is_container'=>1, 'container'=>$this->container, 'path'=>$this->path))){
			return 1;
		}
		return 0;
	}

	public function getFullname()
	{
		$name = $this->name;
		if ($this->extension){
			$name .= '.'.$this->extension;
		}
		return $name;
	}

	public function getParentContainerName()
	{
		if ($this->container0){
			return $this->container0->name;
		}else{
			return __('index');
		}
	}


	public function getURI()
	{
		if ($this->is_container){
			return Null;
		}
		return $this->baseDir.$this->path;
	}

	public function getURL()
	{
		if ($this->is_container){
			return Yii::app()->createAbsoluteUrl('').$this->getContainerWebPath();
		}
		return Yii::app()->createAbsoluteUrl('').'/archive/'.$this->id;
	}

	public function getParentContainerURL()
	{
		if ($this->container){
			return Yii::app()->createAbsoluteUrl('').$this->container0->getContainerWebPath();
		}else{
			return Yii::app()->createAbsoluteUrl('').'/archive';
		}
	}

	public function getContainerWebPath()
	{
		if (!$this->is_container){
			return '/archive/index';
		}
		$path = ':'.$this->path;
		$model = $this;
		while ($model->container){
			$path = ':'.$model->container0->path.$path;
			$model = $model->container0;
		}
		$path= ltrim ($path, ':');
		return '/archive/d/'.$path;
	}

	public function getParentContainerWebPath()
 	{
		if (!$this->container){
			return 'archive/index';
		}
		return $this->container0->getContainerWebPath();
	}

	public function getContainerFromPath($containerPath)
	{
		$pathComponents = explode(':', $containerPath);
		$container = Null;
		$containerID = Null;
		while($pathComponents){	
			$path = array_shift($pathComponents);
			$container = $this->findByAttributes(array('is_container'=>1, 'path'=>$path, 'container'=>$containerID));
			if (!$container){
				break;
			}
			$containerID = $container->id;
		}
		return $container;
	}

	public function isChildOf($parent)
	{
		$loop = true;
		$model = $this;
		while ($loop){
			if (!$model->container){
				$loop = false;
			}else{
				if($model->container0->id == $parent->id){
					return 1;
				}
				$model = $model->container0;
			}
		}
		return 0;
	}

	public function canEdit($user_id, $is_admin){	
		if ($this->author == $user_id || $is_admin){
			return true;
		}
		return false;
	}

	public function getExtension($file_name){
		return pathinfo($file_name, PATHINFO_EXTENSION);
	}

 	public function setFilePath()
	{
		$path = substr(md5(rand(0, 1000000)), 0, 45);
		while($this->findAllByAttributes(array('path'=>$this->archiveRoot.$path))){
			$path = substr(md5(rand(0, 1000000)), 0, 45);
		}
		$this->path = $this->archiveRoot.$path;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($container = Null)
	{
		$criteria=new CDbCriteria;
		$text = $this->searchText;
		
		$criteria->addCondition("name LIKE :match OR description LIKE :match");
		$criteria->params[':match'] = "%$text%";

		if (!$text){
			if ($container){
				$criteria->compare('container', $container->id);
			}else{
				$criteria->addCondition('container is NULL');
			}
		}
		$criteria->order = 'is_container DESC, name ASC';
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>20),
		));
	}
	
	/* I want to use a webfont in the <td> element and I don't know how to do it without using the label
	 * This gets called from the CGrid
	 */
	public function getShareColumnItem()
	{
		return '<span style="position:relative"><i class="icon-share" onClick="js:loadSocialWidgets(\''.$this->id.'\', this)"></i></span>';
	} 
}
