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

class GraphController extends Controller
{
	public function filters()
	{
		return array();
	}

	// Actions
	public function actionScript()
	{
		$fileName = Yii::getPathOfAlias('application.views.graph').'/'.$_GET['script'].'.js';
		if(!file_exists($fileName))
			Yii::app()->end();

		header("Content-type: text/javascript; charset: UTF-8");
		header("Pragma: cache");
		header("Cache-Control: must-revalidate");
		$offset = strtotime('+42 hours'); // same as time() + 42 * 60 * 60
		$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", $offset) . " GMT";
		header($ExpStr);
		$content = file_get_contents($fileName);
		//include(svgDir().'newenquiry.svg');
		$content = str_replace('$baseURL', Yii::app()->getBaseUrl(true), $content);
		$arrow =  file_get_contents(svgDir().'prev_budget.svg');
		//$arrow =  include(svgDir().'prev_budget.svg');
		//$content = str_replace('$prev_budget_svg', $arrow, $content);
		echo $content;
	}
}
