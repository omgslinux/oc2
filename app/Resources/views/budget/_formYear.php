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

?>

<style>           
	.outer{width:100%; padding: 0px; float: left;}
	.left{width: 38%; float: left;  margin: 0px;}
	.right{width: 58%; float: left; margin: 0px;}
	.clear{clear:both;}
</style>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'budget-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="title"><?php echo $title;?></div>

<div class="outer">
<div class="left">

	<?php /*echo $form->errorSummary($model); */?>

	<div class="row">
	<?php if($model->isNewRecord){
		echo $form->labelEx($model,'year');
		echo '<div class="hint">YYYY '.__('Only 4 digits').'</div>';
		echo $form->textField($model,'year');
		echo $form->error($model,'year');
	 }?>
	</div>

	<div class="row">
		<b><?php echo __('Population');?></b><br />
		<?php
			$model->initial_provision = substr_replace($model->initial_provision ,"",-3);	//don't want population to have decimals
			echo '<div class="hint">'.__('Population this year').'</div>';
			echo $form->textField($model,'initial_provision');
		?>
	</div>
	<div class="row">
		<label><?php echo __('Publish');?></label>
		<?php echo $form->checkBox($model, 'code', array('onChange'=>'js:$("#updateZipWarning").show();')); ?>
		<div id="updateZipWarning" style="font-size:16px; display:none;">
		<?php echo ' '.__('Remember to update the zip file');?> <i class="icon-attention green"></i>
		</div>
	</div>


</div>
<div class="right">
	<div class="row" style="font-size:1.4em">
		<?php echo $totalBudgets.' '.__('defined budgets'); ?>
	</div>
	<div class="row" style="font-size:1.4em">
		<?php	
		if($totalBudgets > 0)
			echo $featuredCount.' '.__('Featured budgets');
		?>
	</div>
</div>
</div>
<div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? __('Create') : __('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
