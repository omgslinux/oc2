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

/* @var $this BudgetDescriptionController */
/* @var $model BudgetDescription */
/* @var $form CActiveForm */
?>

<div class="wide form" style="width:100%">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

<div style="float:left;width:35%">

	<div class="row">
		<?php echo $form->label($model,'csv_id'); ?>
		<?php echo $form->textField($model,'csv_id',array('size'=>15,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'code'); ?>
		<?php echo $form->textField($model,'code',array('size'=>15,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'language'); ?>
		<?php echo $form->textField($model,'language',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Search')); ?>
	</div>

</div>
<div style="float:right;width:65%">

	<div class="row">
		<?php echo $form->label($model,'concept'); ?>
		<?php echo $form->textField($model,'concept',array('size'=>40,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textField($model,'text',array('size'=>40,'maxlength'=>255)); ?>
	</div>

</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
<div style="clear:both"></div>
