<?php

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'class.phpmailer.php');

class Mailer
{

	private $_myMailer;

	public function init() {}

	public function __construct()
	{
		$this->_myMailer = new PHPMailer();
	    $this->_myMailer->CharSet = "UTF-8";

		if(Config::model()->findByPk('smtpMethod')->value == 0){
			$this->_myMailer->IsSMTP();
			$this->_myMailer->SMTPAuth = Config::model()->findByPk('smtpAuth')->value;
			$this->_myMailer->SMTPSecure = Config::model()->findByPk('smtpSecure')->value;

			$this->_myMailer->Host = Config::model()->findByPk('smtpHost')->value;
			$this->_myMailer->Port = Config::model()->findByPk('smtpPort')->value;
			$this->_myMailer->Username = Config::model()->findByPk('smtpUsername')->value;
			$this->_myMailer->Password = Config::model()->findByPk('smtpPassword')->value;
		}else
			$this->_myMailer->IsSendmail();
	}

	public function __call($method, $params)
	{
		if (is_object($this->_myMailer) && get_class($this->_myMailer)==='PHPMailer'){
			if($method == 'send'){
			    //$this->_myMailer->Body = preg_replace('/[\]/','', $this->_myMailer->Body);
			    $this->_myMailer->MsgHTML($this->_myMailer->Body); 
			}
			return call_user_func_array(array($this->_myMailer, $method), $params);
		}
		else throw new CException(Yii::t('Mailer', 'Can not call a method of a non existent object'));
	}

	public function __set($name, $value)
	{
		if (is_object($this->_myMailer) && get_class($this->_myMailer)==='PHPMailer') $this->_myMailer->$name = $value;
		else throw new CException(Yii::t('Mailer', 'Can not set a property of a non existent object'));
	}

	public function __get($name)
	{
		if (is_object($this->_myMailer) && get_class($this->_myMailer)==='PHPMailer') return $this->_myMailer->$name;
		else throw new CException(Yii::t('Mailer', 'Can not access a property of a non existent object'));
	}

	public function sendBatches($addresses)
	{
		$batches = array_chunk($addresses, 30);	// batches of 30 BCCs
				
		foreach($batches as $batch){
			foreach($batch as $address)
				$this->AddBCC(trim($address));
			if(!$this->send())
				return 0;
				
			$this->ClearBCCs();
		}		
		return 1;
	}

	/**
	 * Cleanup work before serializing.
	 * This is a PHP defined magic method.
	 * @return array the names of instance-variables to serialize.
	 */
	public function __sleep()
	{
	}

	/**
	 * This method will be automatically called when unserialization happens.
	 * This is a PHP defined magic method.
	 */
	public function __wakeup()
	{
	}

}

