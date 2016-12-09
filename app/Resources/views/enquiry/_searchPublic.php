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

/* @var $this EnquiryController */
/* @var $model Enquiry */
/* @var $form CActiveForm */
?>

<style>
#search_enquiries { }	
#search_enquiries div { font-size: 16px; }

</style>

<!-- outer start -->
<div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'search_enquiries',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
echo $form->hiddenField($model,'basicFilter');
?>


<!-- column 1 start -->
<div style="float:left; width:480px;">
	<?php echo $form->label($model,'state'); ?><br />
	<?php
		$states=$model->getHumanStates();
		unset($states[1]);	// pending validation
		unset($states[2]);	// assigned to team member
		unset($states[3]);	// rejected by observatory
		echo $form->dropDownList($model, 'state', array(""=>__('Not filtered')) + $states);
	?>
<br />
	<?php echo $form->label($model,'type'); ?><br />
	<?php echo $form->dropDownList($model, 'type', array(""=>__('Not filtered')) + $model->getHumanTypes());?>
</div>
<!-- column 1 end -->

<!-- column 2 start -->
<div style="float:left; width:275px;">

<div style="float:left">
<span><?php echo __('Minimum date');?></span>
<br />
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model' => $model,
	'attribute' => 'searchDate_min',
	'language' => Yii::app()->language,
	'options' => array(
		'dateFormat'=>'dd/mm/yy',
		//'minDate' => '2000-01-01',
		//'maxDate' => '2099-12-31',
	),
	'htmlOptions' => array(
		'style'=>'width:100px; margin-right:10px;',
		'readonly'=>'readonly',
	),
));
?>
</div>

<div style="float:left">
<span><?php echo __('Maximum date');?></span>
<br />
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model' => $model,
	'attribute' => 'searchDate_max',
	'language' => Yii::app()->language,
	'options' => array(
		'dateFormat'=>'dd/mm/yy',
		//'minDate' => '2000-01-01',
		//'maxDate' => '2099-12-31',
	),
	'htmlOptions' => array(
		'style'=>'width:100px; margin-right:10px;',
		'readonly'=>'readonly',
	),
));
?>
</div>
<div class="clear"></div>
<span><?php echo __('Text');?></span><br />
<?php echo $form->textField($model,'searchText',array('style'=>'width:217px','maxlength'=>255));?>
</div>	<!-- column 2 end -->

<!-- column 3 start -->
<div style="float:left; text-align:right; width:150px; padding-top: 70px">
<?php echo CHtml::submitButton(__('Filter'));?>
</div>	<!-- column 3 end -->

<?php $this->endWidget(); ?>
<div class="clear" style=""></div>

</div>	<!-- close outer -->




