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

/* @var $this BackupController */

$this->menu=array(
	array('label'=>__('Show schedule').'<i class="icon-popup-1"></i>', 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:showSchedule(); return false;')),
	array('label'=>__('Manual backup'), 'url'=>array('backup/manualCreate')),
	array('label'=>__('Create vault'), 'url'=>array('create')),
);
$this->inlineHelp=':manual:vault:admin';
?>

<?php
Yii::import('application.includes.*');
require_once('diskStatus.php');

try {
	$diskStatus = new DiskStatus(Yii::app()->basePath);
	$freeSpace = $diskStatus->freeSpace();
	$totalSpace = $diskStatus->totalSpace();
}
catch (Exception $e) {
	$noDiskStatus = 1;
}
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function showSchedule(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/vault/viewSchedule',
		type: 'GET',
		beforeSend: function(){ /* */ },
		success: function(html){
			if(html != 0){
				$("#schedule_body").html(html);
				$('#schedule_modal').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
					, speed: 10
                });
			}
		},
		error: function() {
			alert("Error on show schedule");
		}
	});
}
</script>
<style>           
	#vaults { font-size: 1.2em; width:100%; }
	#vaults .sub_title { text-align: center }
	.left { float: left; width:49%; border-right: 2px solid grey; }
	.right { float: right; width:49%; border-left: 2px solid grey; }
	#vault_details { display:none; margin-top:20px; }

</style>

<h1><?php echo __('Manage').' '.__('Backups');?></h1>

<?php
if(!isset($noDiskStatus)){
	echo '<p style="margin: 0 0 10px 0;">';
	echo	__('Total disk space').': '.$totalSpace.',&nbsp;&nbsp;&nbsp;Free: '.
			$freeSpace.' ('.round($freeSpace/$totalSpace * 100, 0).'%)';
	echo '</p>';
}
?>

<div id="vaults">
<div class="left">
<div class="sub_title" style="font-size:1.6em"><?php echo __('Local vaults');?></div>
<?php echo __('They save their copies on your server');?>
<?php $this->widget('PGridView', array(
	'id'=>'localvault-grid',
	'dataProvider'=>$localVaults,
	'template' => '{items}',
    'onClick'=>array(
        'type'=>'url',
        'call'=>Yii::app()->request->baseUrl.'/vault/view',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
		'host',
		array(
	        'type'=>'raw',
	        'value'=>function($data,$row){return Vault::getHumanStates($data->state);},
		),
		array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
	),
)); ?>

</div>
<div class="right">
<div class="sub_title" style="font-size:1.6em"><?php echo __('Remote vaults');?></div>

<?php echo __('You save your copies on their server');?>
<?php $this->widget('PGridView', array(
	'id'=>'remotevault-grid',
	'dataProvider'=>$remoteVaults,
	'template' => '{items}',
    'onClick'=>array(
        'type'=>'url',
        'call'=>Yii::app()->request->baseUrl.'/vault/view',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
		'host',
		array(
	        'type'=>'raw',
	        'value'=>function($data,$row){return Vault::getHumanStates($data->state);},
		),
		array('class'=>'PHiddenColumn','value'=>'$data->id'),
	),
)); ?>

</div>
</div>
<div class="clear"></div>

<div class="sub_title"><?php echo __('All Backups');?></div>

<?php $this->widget('PGridView', array(
	'id'=>'backup-grid',
	'dataProvider'=>Backup::model()->search(),
    'onClick'=>array(
        'type'=>'url',
        'call'=>Yii::app()->request->baseUrl.'/vault/view',
    ),
	'columns'=>array(
		array(
			'header'=>__('Vault'),
			'type' => 'raw',
			'value'=>'$data->vault0->host',
		),
		array(
			'header'=>__('Type'),
			'type' => 'raw',
			'value'=>'$data->vault0->getHumanType($data->vault0->type)',
		),
		'initiated',
		'completed',
		array(
			'header'=>__('Filesize'),
			'type' => 'raw',
			'value'=>'$data->fileSizeForHumans()',
		),	
		array(
			'header'=>__('State'),
			'type' => 'raw',
			'value'=>'$data->getHumanState()',
		),
		array('class'=>'PHiddenColumn','value'=>'$data->vault0->id'),
	),
)); ?>


<div id="schedule_modal" class="modal" style="width:800px;">
<i class='icon-cancel-circled modalWindowButton bClose'></i>
<div id="schedule_body"></div>
</div>
