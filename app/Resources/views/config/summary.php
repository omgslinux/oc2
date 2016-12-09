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

/* @var $this ConfigController */
/* @var $model Config */

Yii::import('application.includes.*');
require_once('diskStatus.php');

try {
	$diskStatus = new DiskStatus(Yii::app()->basePath);
 
	$freeSpace = $diskStatus->freeSpace();
	$totalSpace = $diskStatus->totalSpace();
	$usedSpace = $totalSpace - $freeSpace;
}
catch (Exception $e) {
	//echo 'Error ('.$e->getMessage().')';
	//exit();
	$noDiskStatus = 1;
}
?>

<style>
.sub_title { text-decoration: underline; margin-bottom: 8px; }
</style>

<div>
<h1 style="float:left"><?php echo __('Site summary');?></h1>
<p style="float:right">OCAx version <?php echo getOCAXVersion();?></p>
</div>
<div class="clear"></div>

<div style="float:left; width:400px;">
<?php
	$criteria=new CDbCriteria;
	$criteria->addCondition('is_team_member = 1 OR is_editor = 1 OR is_manager = 1 OR is_admin =1');
	$ocmMembers = count(User::model()->findAll($criteria));
	echo '<div class="sub_title">'.__('Users').'</div>';
	echo '<p>';
	echo __('Total users').': '.count(User::model()->findAll()).'<br />';
	echo __('OCM members').': '.$ocmMembers.'<br />';	
	echo '</p>';
?>
</div>

<div style="float:left;">
	<?php
	echo '<div class="sub_title">'.__('OCAx version').'</div>';
	$this->renderPartial('//config/versionSummary');
	?>
</div>

<?php
	if(!isset($noDiskStatus)){
		echo '<div style="float:left;width:400px;">';
		echo '<div class="sub_title">'.__('Disk usage (approx)').'</div>';
		echo '<p>';
		echo __('Free').': '.$freeSpace.' ('.round($freeSpace/$totalSpace * 100, 0).'%)<br />';
		echo __('Total').': '.$totalSpace.'<br />';	
		echo '</p>';
		echo '</div><div class="clear"></div>';
	}
?>

<div style="float:left; width:400px;">
<?php
	$criteria=new CDbCriteria;
	$criteria->condition = 'parent is NULL';
	$criteria->order = 'year DESC';
	$root_budgets = Budget::model()->findAll($criteria);
	echo '<div class="sub_title">'.__('Budgets').'</div>';
	echo '<p>';
	foreach($root_budgets as $root_budget){
		echo $root_budget->year.': '.$root_budget->getYearsBudgetCount().' '.__('budgets').'<br />';
	}
	echo '</p>';
?>
</div>
<div class="clear"></div>
