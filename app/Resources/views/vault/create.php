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
/* @var $model Vault */

$this->menu=array(
	array('label'=>__('Show schedule').'<i class="icon-popup-1"></i>', 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:showSchedule(); return false;')),
	array('label'=>__('Manage backups'), 'url'=>array('backup/admin')),
);
$this->inlineHelp=':manual:vault:create';
?>

<style>
	/* .step { display:none } */
	h2 { margin-top:10px; margin-bottom:5px; }
	.step p { margin-bottom: 0px }
</style>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
$(function() {
	/*
	if( $("#vault-form input[type='radio']:checked").val() == 0)
		$("#schedule").show();	
	*/
	if($('#Vault_type_0').is(':checked'))
		$('#schedule_select').show();
});

function toggleSchedule(){
	if( $("#vault-form input[type='radio']:checked").val() == 0)
		$("#schedule_select").show();
	else
		$("#schedule_select").hide();
}
function updateSchedule(el, day){
	schedule = $('#Vault_schedule').val();
	if($(el).is(':checked'))
		checked = '1';
	else
		checked = '0';
	schedule = schedule.substring(0, day) + checked + schedule.substring(day+1);
	$('#Vault_schedule').val(schedule);
}
function showSchedule(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/vault/viewSchedule',
		type: 'GET',
		beforeSend: function(){ /* */ },
		success: function(html){
			if(html != 0){
				$("#schedule_body").html(html);
				$('#schedule').bPopup({
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

<h1><?php echo __('Create a vault');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'vault-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="row step">
<h2>Every vault has two observatories</h2>
<p>
1.	<?php echo Yii::app()->getBaseUrl(true);?><br />
2.	<?php echo $form->textField($model,'host',array('size'=>30,'maxlength'=>255)); ?>
	<?php echo $form->error($model,'host'); ?>
</p>
</div>

<div class="row step" style="display:block">
<h2><?php echo __('What type of vault are you creating?');?></h2>
<p>
<?php
	$vaultType = array(
				0=>__('I want to allow someone to save their copies on my server'),
				1=>__('I want to save my copies on another server'),
	);
	echo $form->radioButtonList($model,'type',
								$vaultType,
								array(	'labelOptions'=>array('style'=>'display:inline'),
										'separator'=>'<br />',
										'onchange'=>'js:toggleSchedule();return false;',
									)
							);
	echo $form->error($model,'type');
?>
</p>
</div>

<div id="schedule_select" class="row" style="display:none;">
<h2><?php echo __('When can they make backups?');?></h2>
<p>
<?php
	echo $form->hiddenField($model, 'schedule');
	$schedule = $model->getAvailableSchedule();
	$day = 0;
	while($day < 7){
		if($schedule[$day] == 1){
			echo '<span style="color:grey">';
			echo '<input type="checkbox" disabled="disabled" style="margin-right:10px">';
			echo $model->getHumanDays($day);
		}else{
			echo '<span>';
			echo '<input type="checkbox" style="margin-right:10px" onclick="js:updateSchedule(this, '.$day.')">';
			echo $model->getHumanDays($day);
		}
		echo '</span><br />';
		$day++;
	}
	echo $form->error($model,'schedule');
?>
</p>
</div>

<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>

<?php $this->endWidget(); ?>
</div>

<div id="schedule" class="modal" style="width:800px;">
<i class='icon-cancel-circled modalWindowButton bClose'></i>
<div id="schedule_body"></div>
</div>
