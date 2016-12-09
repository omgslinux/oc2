<?php
/**
 * OCAX -- Citizen driven Municipal Observatory software
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

/* @var $this UserController */
/* @var $model User */


$column=0;
function changeColumn()
{
	global $column;
	if($column==0)
	{
		echo '<div class="clear"></div>';
		echo '<div class="left">';
		$column=1;
	}
	else
	{
		echo '<div class="right">';
		$column=0;
	}
}

$this->menu=array(
	array('label'=>__('View User'), 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>__('Manage Users'), 'url'=>array('admin')),
);

if(Yii::app()->user->getUserID() != $model->id){
	if(!$model->enquirys){
		$item= array(	array(	'label'=>__('Delete user'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>__('Are you sure you want to delete this item?'))
						));
		array_splice( $this->menu, 1, 0, $item );
	}
	if($model->is_disabled){
		$item = array( array('label'=>__('Enable user'), 'url'=>array('enable', 'id'=>$model->id)));
		array_splice( $this->menu, 1, 0, $item );
	}else{
		$item = array( array(	'label'=>__('Disable user'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('disable', 'id'=>$model->id))));
		array_splice( $this->menu, 1, 0, $item );	
	}
}

$this->inlineHelp=':manual:user:updateroles';
$this->viewLog='User|'.$model->id;
?>

<style>
	.left{width: 48%; float: left;  margin: 0px;}
	.right{width: 48%; float: left; margin: 0px;}
	.clear{clear:both;}
</style>

<script>
$(document).on('change', 'input[type="checkbox"]', function(e) {
	if( $(this).prop('checked') ){
		if($(this).attr('id') != 'User_is_description_editor')
			$('#User_is_description_editor').prop('checked', true);
	}
});
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="title"><?php echo __('Change roles')?></div>

	<?php $this->widget('zii.widgets.CDetailView', array(
		'cssFile' => Yii::app()->request->baseUrl.'/css/pdetailview.css',
		'data'=>$model,
		'attributes'=>array(
			'username',
			'fullname',
			'email',
			'joined',
			'is_socio',
		),
	)); ?>

<div>
	<?php changeColumn();?>
	<div class="row">
		<?php echo $form->labelEx($model,'is_description_editor'); ?>
		<?php echo $form->checkBox($model,'is_description_editor', array('checked'=>$model->is_description_editor)); ?>
		<?php echo __('Can edit budget descriptions').'.';?>
	</div>
	</div>

	<?php changeColumn();?>
	<div class="row">
		<?php echo $form->labelEx($model,'is_team_member'); ?>
		<?php echo $form->checkBox($model,'is_team_member', array('checked'=>$model->is_team_member)); ?>
		<?php echo __('Manage the enquiries you are responsable for').'.';?>
	</div>
	</div>

	<?php changeColumn();?>
	<div class="row">
		<?php echo $form->labelEx($model,'is_editor'); ?>
		<?php echo $form->checkBox($model,'is_editor', array('checked'=>$model->is_editor)); ?>
		<?php __('Page editor');?>.
	</div>
	</div>

	<?php changeColumn();?>
	<div class="row">
		<?php echo $form->labelEx($model,'is_manager'); ?>
		<?php echo $form->checkBox($model,'is_manager', array('checked'=>$model->is_manager)); ?>
		<?php echo __('Assign enquiries to team members and check status').'.';?>
	</div>
	</div>

	<?php changeColumn();?>
	<div class="row">
		<?php echo $form->labelEx($model,'is_admin'); ?>
		<?php echo $form->checkBox($model,'is_admin', array('checked'=>$model->is_admin)); ?>
		Administer Site, Users, Budgets.
	</div>
	</div>

	<div class="clear"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Save')); ?>
	</div>
</div>

<?php $this->endWidget(); ?>


</div><!-- form -->



