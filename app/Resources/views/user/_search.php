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
/* @var $form CActiveForm */
?>

<style>
.outer{ width: 100% }

</style>

<div class="wide form" style="width:100%;margin-top:15px;">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

<div style="float:left;width:50%">

	<div class="row">
		<?php echo $form->label($model,'username',array('style'=>'white-space: nowrap;')); ?>
		<?php echo $form->textField($model,'username',array('style'=>'width:200px')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fullname',array('style'=>'white-space: nowrap;')); ?>
		<?php echo $form->textField($model,'fullname',array('style'=>'width:200px')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'email',array('style'=>'white-space: nowrap;')); ?>
		<?php echo $form->textField($model,'email',array('style'=>'width:200px')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Search')); ?>
	</div>

</div>
<div style="float:right;width:24%">

	<div class="row">
		<?php echo $form->label($model,'is_team_member'); ?>
		<?php echo $form->textField($model,'is_team_member',array('size'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_manager'); ?>
		<?php echo $form->textField($model,'is_manager',array('size'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_admin'); ?>
		<?php echo $form->textField($model,'is_admin',array('size'=>5)); ?>
	</div>

</div>
<div style="float:right;width:24%">

	<div class="row">
		<?php echo $form->label($model,'is_active'); ?>
		<?php echo $form->textField($model,'is_active',array('size'=>5)); ?>
	</div>

	<?php if(Config::model()->findByPk('membership')->value){ ?>
	<div class="row">
		<?php echo $form->label($model,'is_socio'); ?>
		<?php echo $form->textField($model,'is_socio',array('size'=>5)); ?>
	</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->label($model,'is_editor'); ?>
		<?php echo $form->textField($model,'is_editor',array('size'=>5)); ?>
	</div>

</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
<div style="clear:both"></div>
