<?php

/**
 * OCAX -- Citizen driven Municipal Observatory software
 * Copyright (C) 2015 OCAX Contributors. See AUTHORS.

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

/* @var $this ArchiveController */
/* @var $model Archive */
/* @var $form CActiveForm */

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;

?>
<script>
function validate(){
	$('.errorMessage').html('');
	errors=0;

	if ($('#Archive_name').val() == ''){
		$('#name_error').html("<?php echo __('Name required');?>");
		errors=1;
	}
	if ($('#Archive_description').val() == ''){
		$('#description_error').html("<?php echo __('Description required');?>");
		errors=1;
	}
	if (!errors){
		$('#archive-form').submit();
	}
}
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'archive-form',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->baseUrl.'/archive/createContainer/'.$model->container,
)); ?>

	<div class="modalTitle"><?php echo __('Create a folder');?></div>

	<?php
		echo $form->label($model, 'name');
		echo '<div class="errorMessage" id="name_error"></div>';
		echo $form->textField($model,'name');
	?>

	<?php
		echo $form->label($model, 'description');
		echo '<div class="hint">'.__('A description of this folder').'</div>';
		echo '<div class="errorMessage" id="description_error"></div>';
		echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50));
	?>

	<div class="row buttons">
		<input type="button" value="<?php echo __('Create')?>" onClick="js:validate();" />
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->



