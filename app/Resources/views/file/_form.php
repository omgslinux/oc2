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

/* @var $this FileController */
/* @var $model File */
/* @var $form CActiveForm */
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;

?>
<script>
function validateFileName(form){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/validateFileName',
		type: 'GET',
		data: {	'file_name'	: $('#File_file').val().replace('C:\\fakepath\\', ''),
				'model' 	: $('#File_model').val(),
				'model_id' 	: $('#File_model_id').val(),
		},
		//beforeSend: function() {},
		success: function(data){
			if(data == 1){
				$('#file-form').hide();
				$('#loading').show(); 
				$(form).submit();
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
	'id'=>'file-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
//, 'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
?>

	<div class="modalTitle"><?php echo __('Upload file');?></div>

	<?php echo $form->hiddenField($model,'model'); ?>
	<?php echo $form->hiddenField($model,'model_id'); ?>

	<?php
	if($model->model == 'Reply'){
		echo $form->label($model, 'name');
		echo '<div class="hint">'.__('Name used for the link').'</div>';
		echo $form->textField($model, 'name');
		echo $form->label($model, 'file');
	}else
		echo '<p></p>';
	?>

	<?php echo $form->fileField($model, 'file'); ?>
	<div class="errorMessage" id="file_error"></div>

	<div class="row buttons">
		<input type="button" value="<?php echo __('Upload')?>" onClick="js:validateFileName($('#file-form'));" />
	</div>

<?php $this->endWidget(); ?>

<div id="loading" style="display:none;text-align:center;">
<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/big_loading.gif" />
</div>

</div><!-- form -->



