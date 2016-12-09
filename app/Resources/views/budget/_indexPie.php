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

/* @var $this BudgetController */
/* @var $model Budget */
?>

<style>

</style>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/piegraph.css" />
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jqplot/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/graph/ocaxpiegraph"></script>
			

<div class="ocaxpiegraph">
<?php
foreach($featured as $budget){
		echo '<div id="anchor_'.$budget->id.'" class="graph_pie_group"></div>';
}
?>
</div>

<script>
$(window).bind("load", function() {
	<?php 
		foreach($featured as $budget){	// Yii::app()->getBaseUrl(true)
			echo '$("#anchor_'.$budget->id.'").ocaxpiegraph({	source: "'.Yii::app()->request->baseUrl.'",	
																rootBudget: '.$budget->id.',
																rootBudgetData: '.$this->actionGetPieData($budget->id).',
																graphTitle: "'.$budget->getCategory().'"
															});';
	}
	?>
});
</script>
