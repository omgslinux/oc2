<?php
/**
 * OCAX -- Citizen driven Municipal Observatory software
 * Copyright (C) 2015 OCAX Contributors. See AUTHORS.

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

function runSQLFile($file){
	$pdo = Yii::app()->db->pdoInstance;
	try 
	{ 
		if (file_exists($file)) {
			$sqlStream = file_get_contents($file);
			$sqlStream = rtrim($sqlStream);
			$newStream = preg_replace_callback("/\((.*)\)/", create_function('$matches', 'return str_replace(";"," $$$ ",$matches[0]);'), $sqlStream); 
			$sqlArray = explode(";", $newStream); 
			foreach ($sqlArray as $value) { 
				if (!empty($value)){
					$sql = str_replace(" $$$ ", ";", $value) . ";";
					$pdo->exec($sql);
				} 
			} 
			return true;
		} 
	} 
	catch (PDOException $e) { 
		echo $e->getMessage();
		exit; 
	}
}
