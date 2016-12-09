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


/**
 * Localization
 * Wrapper function for Yii::t()
 */
//http://www.yiiframework.com/forum/index.php/topic/14542-gettext-and-yii/page__p__147482#entry147482
function __($string, $params = array(), $category = "") {
        return Yii::t($category, $string, $params);
}

function percentage($val1, $val2){
	if($val2 == 0)
		return 100;
	$percent = ($val1 / $val2) * 100;
	//$decimals = strlen($percent) - strrpos($percent, '.') - 1;

	return number_format(round($percent,2), 2, ',', '.');
}

function format_number($number){
	$number = number_format(CHtml::encode($number), 2, ',', '.');
	return str_replace('n', $number, Config::model()->findByPk('currencySymbol')->value);
}

function getLanguagesArray($available=Null){
	if($available){
		// get array from reading translation directories
	}else// enabled
		$languages=explode(',', Config::model()->findByPk('languages')->value);
	$listData = array();
	if(isset($languages[1])){
		foreach($languages as $language){
			if($language == 'es')
				$listData['es'] = 'Castellano';
			if($language == 'ca')
				$listData['ca'] = 'Català';
			if($language == 'gl')
				$listData['gl'] = 'Gallego';
			if($language == 'en')
				$listData['en'] = 'English';
			if($language == 'fr')
				$listData['fr'] = 'Français';
			if($language == 'it')
				$listData['it'] = 'Italiano';
			if($language == 'pt')
				$listData['pt'] = 'Português';
			if($language == 'el')
				$listData['el'] = 'ελληνικά';
		}
	}
	return $listData;
}

function getDefaultLanguage(){
	$languages=explode(',', Config::model()->findByPk('languages')->value);
	return $languages[0];
}

function format_date($date, $hours=Null){
	$date = new DateTime($date);
	if($hours)
		return $date->format('d-m-Y H:i:s');
	else
		return $date->format('d-m-Y');
}

// moved this to model/config Remember to remove it from here.
function getOCAXVersion(){
	$path = Yii::app()->basePath.'/data/ocax.version';
	$handle = @fopen($path, "r");
	$version = rtrim(fgets($handle),"\n");
	fclose($handle);
	return $version;
}

function getInlineHelpURL($path){
	$availableTranslations = array('ca', 'es', 'en');
	$lang = Yii::app()->user->getState('applicationLanguage');
	if(!in_array($lang, $availableTranslations))
		$lang = 'es';
	return 'http://wiki.ocax.net/'.$lang.$path;
}

function getMySqlParams()
{
	$result=array();
	$connectionString = Yii::app()->db->connectionString;
	// this expects $connectionString to be 'mysql:host=host;dbname=name'
	$connectionString = preg_replace('/^mysql:/', '', $connectionString);
	$params = explode(';', $connectionString);
	list($param, $result['host']) = explode('=', $params[0]);
	list($param, $result['dbname']) = explode('=', $params[1]);
	$result['user'] = Yii::app()->db->username;
	$result['pass'] = Yii::app()->db->password;
	return $result;
}

function createDirectory($path)
{
	if(!file_exists($path)){
		$oldmask = umask(0);
		if (mkdir($path, 0777, true)){	// some servers have strange permision setups.
			umask($oldmask);
			return 1;	// success
		}
		umask($oldmask);
	}
	return 0; // fail
}

function createTempDirectory()
{
	$basePath = Yii::app()->basePath.'/runtime/tmp/';
	$tempDir = uniqid('',false);
	
	while(1){
		if(!file_exists($basePath.$tempDir)){
			createDirectory($basePath.$tempDir);
			return $basePath.$tempDir.'/';
		}
		$tempDir = uniqid('',false);
	}
}

function deleteTempDirectory($dir)
{
	$basePath = Yii::app()->basePath.'/runtime/tmp/';
	if(strpos($dir, $basePath) === false){
			return; // we only delete dirs in ../protected/runtime/tmp/
	}
	$files = array_diff(scandir($dir), array('.', '..')); 
	foreach ($files as $file) { 
		(is_dir("$dir/$file")) ? deleteTempDirectory("$dir/$file") : unlink("$dir/$file"); 
	}
	return rmdir($dir); 
} 


function createTempFile()
{
	$basePath = Yii::app()->basePath.'/runtime/tmp/';
	$fn = uniqid('',false);
	while(1){
		if(!file_exists($basePath.$fn)){
			touch($basePath.$fn);
			return $basePath.$fn;
		}
		$fn = uniqid('',false);
	}
}

function string2ascii($string)
{
	$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
	$string = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $string);
	$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
	$string = preg_replace(array('~[^0-9.a-z]~i', '~[ -]+~'), ' ', $string);

	return trim($string, ' ');
}

function bytesForHumans($bytes, $precision = 2)
{
	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

	$bytes = max($bytes, 0); 
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	$pow = min($pow, count($units) - 1); 

	// Uncomment one of the following alternatives
	//$bytes /= pow(1024, $pow);
	$bytes /= (1 << (10 * $pow)); 

	return round($bytes, $precision) . ' ' . $units[$pow]; 
}

function svgDir(){
	return dirname(Yii::app()->request->scriptFile).'/images/svg/';
}

function resizeLogo($fn){
	$ext = pathinfo($fn, PATHINFO_EXTENSION);
	if(!($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg'))		
		return Null;
	if(!extension_loaded('gd'))
		return Null;

	$gdInfo=gd_info();
	if(!isset($gdInfo))
		return Null;
	if($ext == 'png' && !$gdInfo['PNG Support'])
		return Null;
	if(($ext == 'jpg' || $ext == 'jpeg') && !$gdInfo['JPEG Support'])
		return Null;

	// create logo
	list($width, $height) = getimagesize($fn);
	$new_width = 90;
	$new_height = 90;

	$image_p = imagecreatetruecolor($new_width, $new_height);
	if($ext == 'png')
		$image = imagecreatefrompng($fn);
	else
		$image = imagecreatefromjpeg($fn);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	if($ext == 'png')
		imagepng($image_p, $fn, 9);
	else
		imagejpeg($image_p, $fn, 100);
		
	// create favicon
	Yii::import('application.includes.*');
	require_once('php-ico.php');
	$source = $fn;
	$destination = dirname(Yii::app()->request->scriptFile).'/files/favicon.ico';
	$ico_lib = new PHP_ICO( $source, array(array( 16, 16 )) );
	$ico_lib->save_ico( $destination );
}


// http://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
function isExecAvailable() {
	static $available;

	if (!isset($available)) {
		$available = true;
		if (ini_get('safe_mode')) {
			$available = false;
		} else {
			$d = ini_get('disable_functions');
			$s = ini_get('suhosin.executor.func.blacklist');
			if ("$d$s") {
				$array = preg_split('/,\s*/', "$d,$s");
				if (in_array('exec', $array)) {
					$available = false;
				}
			}
		}
	}
	return $available;
}

?>
