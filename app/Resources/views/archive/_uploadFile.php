<?php

/**
 * OCAX -- Citizen driven Municipal Observatory software
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

	if($('#Archive_description').val() == ''){
		$('#description_error').html("<?php echo __('Description required');?>");
		errors=1;
	}
	if(errors)
		return;
		
	$.ajax({
		url: "<?php echo Yii::app()->request->baseUrl.'/archive/validateFile/'.$model->container;?>",
		type: 'GET',
		data: {	'file_name'	: $('#Archive_file').val().replace('C:\\fakepath\\', '') },
		//beforeSend: function() {},
		success: function(data){
			if(data == 1){
				$('#archive-form').hide();
				$('#loading').show(); 
				$('#archive-form').submit();
			}else
				$("#file_error").html(data);
		},
		error: function() {
			alert("Error on validate file name");
		}
	});
}
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'archive-form',
	'action'=>Yii::app()->baseUrl.'/archive/uploadFile',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<div class="modalTitle"><?php echo __('Upload file');?></div>

	<?php echo $form->hiddenField($model,'container'); ?>

	<?php
		echo $form->label($model, 'file');	
		echo '<div class="errorMessage" id="file_error"></div>';
		echo $form->fileField($model, 'file');	
	?>
		
	<?php
		echo $form->label($model, 'description');
		echo '<div class="hint">'.__('A description of this file').'</div>';
		echo '<div class="errorMessage" id="description_error"></div>';
		echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50));
	?>

	<div class="row buttons">
		<input type="button" value="<?php echo __('Upload')?>" onClick="js:validate();" />
	</div>

<?php $this->endWidget(); ?>

<div id="loading" style="display:none;text-align:center;">
<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/big_loading.gif" />
</div>

</div><!-- form -->



