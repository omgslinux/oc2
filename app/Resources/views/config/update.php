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

$this->menu=array(
	array('label'=>__('Global parameters'), 'url'=>array('index')),
);

if(isset($returnURL))
	$returnURL='?returnURL='.$returnURL;
else
	$returnURL=Null;
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'config-form',
	'action' => Yii::app()->createUrl('config/update/'.$model->parameter.$returnURL),
	'enableAjaxValidation'=>false,
)); ?>

	<div class="title"><?php echo __('Change global parameter'); ?></div>

	<p class="row" style="margin:30px 0px 30px 0px">
		<?php echo $model->description.'<br />';	?>
		<?php echo $form->textField($model,'value',array('size'=>40,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'value'); ?>
	</p>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
