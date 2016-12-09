<?php
/**
 * OCAX -- Citizen driven Observatory software
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


// AUTHOR: http://www.yiiframework.com/wiki/175/how-to-create-a-rest-api/

class ApiController extends Controller
{

	// Members
	/**
	 * Key which has to be in HTTP USERNAME and PASSWORD headers
	 */
	Const APPLICATION_ID = 'ASCCPE';

	/**
	* Default response format
	 * either 'json' or 'xml'
	 */
	private $format = 'json';
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array();
	}

	// Actions
	public function actionList()
	{
		// Get the respective model instance
		switch($_GET['model'])
		{
			case 'version':
				$result = array(
						'ocax'=>getOCAXVersion(),
						'schema'=>Config::model()->findByPk('schemaVersion')->value,
						'yii'=>Yii::getVersion(),
						);
				$this->_sendResponse(200, CJSON::encode($result));
				Yii::app()->end();

			case 'profile':
				$result = array(
							'administration_name'=>Config::model()->findByPk('administrationName')->value,
							'observatory_name'=>Config::model()->getObservatoryName(),
							'url'=>Yii::app()->getBaseUrl(true),
							'email'=>Config::model()->findByPk('emailContactAddress')->value,
							'telephone'=>Config::model()->findByPk('telephone')->value,
							'language'=>Config::model()->findByPk('languages')->value,
							'currenySymbol'=>Config::model()->findByPk('currencySymbol')->value,
							'latitude'=>Config::model()->findByPk('administrationLatitude')->value,
							'longitude'=>Config::model()->findByPk('administrationLongitude')->value,
							);
				$this->_sendResponse(200, CJSON::encode($result));
				Yii::app()->end();

			case 'geoLocation':
				$result = array(
							'latitude'=>Config::model()->findByPk('administrationLatitude')->value,
							'longitude'=>Config::model()->findByPk('administrationLongitude')->value,
							);
				$this->_sendResponse(200, CJSON::encode($result));
				Yii::app()->end();

			case 'status':
				$result = array();
				$years = Budget::model()->getPublicYears();

				foreach($years as $year){
					$sql = "SELECT actual_provision FROM budget WHERE year = '".$year->year."' AND csv_id = 'I-E'";
					$budget = Yii::app()->db->createCommand($sql)->queryScalar();

					$sql = "SELECT COUNT(*) FROM enquiry WHERE YEAR(created) = ".$year->year;
					$enquiries = Yii::app()->db->createCommand($sql)->queryScalar();

					$sql = "SELECT COUNT(*) FROM enquiry WHERE YEAR(created) = '".$year->year."' AND state = ".ENQUIRY_REPLY_INSATISFACTORY;
					$enquiries_failed = Yii::app()->db->createCommand($sql)->queryScalar();

					$result[$year->year]=array(
						'population'=>(string)(int)Budget::model()->getPopulation($year->year),
						'budget'=>$budget,
						'enquiries'=>$enquiries,
						'enquiries_failed'=>$enquiries_failed,
					);
				}
				$this->_sendResponse(200, CJSON::encode($result));
				Yii::app()->end();
		}
		$this->_sendResponse(501, sprintf('Error: %s not implemented', $_GET['model']));
		Yii::app()->end();
	}

	public function actionView()
	{
		// Check if id was submitted via GET
		if(!isset($_GET['id']))
			$this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );

		switch($_GET['model'])
		{
			// Find respective model
			default:
				$this->_sendResponse(501, sprintf(
					'Mode <b>view</b> is not implemented for model <b>%s</b>',
					$_GET['model']) );
				Yii::app()->end();
		}
		// Did we find the requested model? If not, raise an error
		if(is_null($model))
			$this->_sendResponse(404, 'No Item found with id '.$_GET['id']);
		else
			$this->_sendResponse(200, CJSON::encode($model));
	}

	private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
		if(isset($_GET['callback'])){	//jsonp
			echo $_GET['callback'] . '('. $body .')';
			Yii::app()->end();
		}

		// set the status
		$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
		header($status_header);
		// and the content type
		header('Content-type: ' . $content_type);

		// pages with body are easy
		if($body != '')
		{
			// send the body
			echo $body;
		}
		// we need to create the body if none is passed
		else
		{
			// create some body messages
			$message = '';

			// this is purely optional, but makes the pages a little nicer to read
			// for your users.  Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch($status)
			{
				case 401:
					$message = 'You must be authorized to view this page.';
					break;
				case 404:
					$message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
					break;
				case 500:
					$message = 'The server encountered an error processing your request.';
					break;
				case 501:
					$message = 'The requested method is not implemented.';
					break;
			}

			// servers don't always have a signature turned on
			// (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

			// this should be templated in a real-world solution
			$body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
    <hr />
    <address>' . $signature . '</address>
</body>
</html>';

			echo $body;
		}
		Yii::app()->end();
	}



	private function _getStatusCodeMessage($status)
	{
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}

	/**
	 * Also, in all REST methods where an authentication is required, we need to put
	 * $this->_checkAuth();
	 * at the beginning of each method.
	 * The API user then needs to set the X_USERNAME and X_PASSWORD headers in his request.
	 */
	private function _checkAuth()
	{
		// Check if we have the USERNAME and PASSWORD HTTP headers set?
		if(!(isset($_SERVER['HTTP_X_USERNAME']) and isset($_SERVER['HTTP_X_PASSWORD']))) {
			// Error: Unauthorized
			$this->_sendResponse(401);
		}
		$username = $_SERVER['HTTP_X_USERNAME'];
		$password = $_SERVER['HTTP_X_PASSWORD'];
		// Find the user
		$user=User::model()->find('LOWER(username)=?',array(strtolower($username)));
		if($user===null) {
			// Error: Unauthorized
			$this->_sendResponse(401, 'Error: User Name is invalid');
		} else if(!$user->validatePassword($password)) {
			// Error: Unauthorized
			$this->_sendResponse(401, 'Error: User Password is invalid');
		}
	}


}
