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


/* @var $this VaultController */

$this->menu=array(
	array('label'=>'Create Vault', 'url'=>array('create')),
	array('label'=>'Manage Vault', 'url'=>array('admin')),
);
?>

<?php
$model = new Vault;

function orderVaultsIntoDays($vaults){
	$days = array();
	$day=0;
	while($day < 7){
		$days[$day] = array();
		foreach($vaults as $vault){
			if($vault->schedule[$day] == 1)
				array_push($days[$day], $vault);
		}
		$day++;
	}
	return $days;
}
?>

<div class="modalTitle"><?php echo __('Schedule');?></div>

<div style="width:100%;margin-top: 10px;">
<div style="float: left; width:45%;">
	<div class="sub_title"><?php echo __('Local vaults');?></div>
	<?php
	$calendar = orderVaultsIntoDays($localVaults);
	foreach($calendar as $day => $vaults){
		echo '<div style="float:left; width:20%">';
		echo $model->getHumanDays($day);
		echo '</div><div  style="float:right; width:78%">';
		foreach($vaults as $vault){
			echo $vault->host.' ';	
		}
		
		echo '</div><br />';
	}
	?>
</div>
<div style="float: right; width:49%; padding-left:20px; border-left: 2px solid grey;">
	<div class="sub_title"><?php echo __('Remote vaults');?></div>
	<?php
	$calendar = orderVaultsIntoDays($remoteVaults);
	foreach($calendar as $day => $vaults){
		echo '<div style="float:left; width:20%">';
		echo $model->getHumanDays($day);
		echo '</div><div style="float:right; width:78%">';
		foreach($vaults as $vault){
			echo $vault->host.' ';	
		}
		echo '</div><br />';
	}
	?>
</div>
</div>
<div style="clear:both; margin-bottom:5px;"></div>
