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

/* @var $this IntroPageController */
/* @var $model IntroPage */
/* @var $form CActiveForm */
?>

<style>           
	.outer{width:100%; padding: 0px; float: left;}
	.left{width: 48%; float: left;  margin: 0px;}
	.right{width: 48%; float: left; margin: 0px;}
	.clear{clear:both;}
</style>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'intro-page-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php
		if($listData = getLanguagesArray())
			$show_language='('.$listData[$content->language].')';
		else
			$show_language='';
	?>

	<div class="title"><?php echo $model->isNewRecord ? $title.' '.$show_language : $title; ?></div>
	<?php echo $form->errorSummary($model); ?>

<div class="outer">
<div class="left">

	<div class="row">
		<?php echo $form->labelEx($model,'weight'); ?>
		<?php echo $form->textField($model,'weight'); ?>
		<?php echo $form->error($model,'weight'); ?>
	</div>

</div>
<div class="right">

	<div class="row">
		<?php echo $form->labelEx($model,'published'); ?>
		<?php echo $form->checkBox($model,'published', array('checked'=>$model->published)); ?>
	</div>

</div>
</div>
<div class="clear"></div>
<div class="horizontalRule"></div>

<div class="outer">
<div class="sub_title"><?php echo __('Text box attributes');?></div>
<div class="left">

	<div class="row">
		<?php echo $form->labelEx($model,'toppos'); ?>
		<span class="hint"><?php echo __('Distance between top of photo and top of box');?></span><br />
		<?php echo $form->textField($model,'toppos'); ?>
		<?php echo $form->error($model,'toppos'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'leftpos'); ?>
		<span class="hint"><?php echo __('Distance from lefthand side of photo to box');?></span><br />
		<?php echo $form->textField($model,'leftpos'); ?>
		<?php echo $form->error($model,'leftpos'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'width'); ?>
		<span class="hint"><?php echo __('Width of the box');?></span><br />
		<?php echo $form->textField($model,'width'); ?>
		<?php echo $form->error($model,'width'); ?>
	</div>
</div>
<div class="right">
	
	<div class="row">
		<?php echo $form->labelEx($model,'color'); ?>
		<span class="hint"><?php echo __('Leave empty to recover default values');?></span><br />
		<?php echo '# '.$form->textField($model,'color'); ?>
		<?php echo $form->error($model,'color'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bgcolor'); ?>
		<span class="hint"><?php echo __('Leave empty to recover default values');?></span><br />
		<?php echo '# '.$form->textField($model,'bgcolor'); ?>
		<?php echo $form->error($model,'bgcolor'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'opacity'); ?>
		<span class="hint"><?php echo __('Box transparentcy').' (0 - 10)';?></span><br />
		<?php echo $form->textField($model,'opacity'); ?>
		<?php echo $form->error($model,'opacity'); ?>
	</div>

</div>
</div>
<div class="clear"></div>
<div class="horizontalRule"></div>
<div class="sub_title"><?php echo __('Text box content');?></div>

	<?php
		if(!$model->isNewRecord  && $listData = getLanguagesArray()){
			echo '<div class="row">';
			echo $form->labelEx($content,'language');
			echo '<div class="hint">'.__('Translations').'</div>';
			echo $form->dropDownList($content, 'language', $listData,
									array('onchange'=>	'location.href="'.Yii::app()->request->baseUrl.
														'/introPage/update/'.$model->id.'?lang="+this.options[this.selectedIndex].value'
									));
			echo '</div>';
		}	
	?>

	<div class="row">
		<?php echo $form->labelEx($content,'title'); ?>
		<?php echo $form->textField($content,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($content,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($content,'subtitle'); ?>
		<?php echo $form->textField($content,'subtitle',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($content,'subtitle'); ?>
	</div>

	<div class="row">
		<?php $content->body = preg_replace('#<br\s*?/?>#i', "", $content->body); ?>
		<?php echo $form->labelEx($content,'body'); ?>
		<?php echo $form->textArea($content,'body',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($content,'body'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? __('Create') : __('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
