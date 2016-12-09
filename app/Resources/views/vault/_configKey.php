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
/* @var $form CActiveForm */
?>

<div class="form" style="margin-top:-20px;">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'vault-form',
	'action' => Yii::app()->createUrl('vault/configureKey/'.$model->id),
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'key'); ?>
		<?php echo $form->textField($model,'key',array('size'=>20,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'key'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Verify')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
