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

/* @var $this EnquiryController */
/* @var $model Enquiry */
/* @var $form CActiveForm */
?>

<div class="wide form" style="width:100%;margin-top:15px;">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

<div style="float:left;width:250px;">

	<div class="row">
		<?php echo $form->label($model,'user'); ?>
		<?php echo $form->textField($model,'username',array('size'=>14)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>14,'maxlength'=>255)); ?>
	</div>

</div>
<div style="float:left;width:30%;">

	<div class="row">
		<?php $team_members = user::model()->findAll(array("condition"=>"is_team_member =  1","order"=>"username")); ?>
		<?php echo $form->label($model,'team_member'); ?>
		<?php echo $form->dropDownList($model,	'team_member',
												CHtml::listData($team_members,'id', 'fullname'),
												array('prompt'=>__('Not filtered'))); ?>
	</div>

	<div class="row">
		<?php $managers = user::model()->findAll(array("condition"=>"is_manager =  1","order"=>"username")); ?>
		<?php echo $form->label($model,'manager'); ?>
		<?php echo $form->dropDownList($model,	'manager',
												CHtml::listData($managers,'id', 'fullname'),
												array('prompt'=>__('Not filtered'))); ?>
	</div>

</div>
<div style="float:left;width:38%">

	<div class="row">
		<?php echo $form->label($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', array(""=>__('Not filtered')) + $model->getHumanTypes());?>
	</div>

</div>
<div style="clear:both"></div>

<div style="float:left;width:250px;">
	<div class="row">
		<?php echo $form->label($model,'body'); ?>
		<?php echo $form->textField($model,'body',array('size'=>14,'maxlength'=>255)); ?>
	</div>
</div>
<div style="float:left;width:60%">
	<div class="row">
		<?php echo $form->label($model,'state'); ?>
		<?php echo $form->dropDownList($model, 'state', array(""=>__('Not filtered')) + $model->getHumanStates());?>
	</div>
</div>
	
<div style="clear:both"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->

