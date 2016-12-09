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

/* @var $this NewsletterController */
/* @var $model Newsletter */

?>

<style>
#recipients_link{
	cursor:pointer;
	text-decoration:underline;
}
</style>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function showRecipients(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/newsletter/showRecipients',
		type: 'GET',
		success: function(data){
			if(data != 0){
				$("#recipients_popup_body").html(data);
				$('#recipients_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show recipients");
		}
	});
}
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'newsletter-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="title"><?php echo __('Send bulk email')?></div>


	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php $model->sent_as=Config::model()->findByPk('emailNoReply')->value;?>
		<?php echo '<p><b>'.__('Sent as').':</b> '.$model->sent_as.'</p>'; ?>
		<?php echo $form->hiddenField($model,'sent_as'); ?>
	</div>

	<div class="row">
		<p>
		<b><?php echo $total_recipients.' '.__('BCC Recipients');?></b>: <span id="recipients_link" onClick="js:showRecipients();">
		<?php echo __('Show');?>
		</span>
		</p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subject'); ?>
		<?php echo $form->textField($model,'subject',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'subject'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php
		$settings = array('theme_advanced_buttons1' => "undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,
														justifyright,|,bullist,numlist,|,outdent,indent,|,
														link,unlink,|,image",
							'convert_urls'=>true,
							'relative_urls'=>false,
							'remove_script_host'=>false,
							'theme_advanced_resize_horizontal' => 0,
							'theme_advanced_resize_vertical' => 0,
							'theme_advanced_resizing_use_cookie' => false,
							'width'=>'100%',
							'valid_elements' => "@[style],p,span,a[href|target=_blank],strong/b,div[align],br,ul,ol,li,img[src]",
				);
		if(Config::model()->findByPk('htmlEditorUseCompressor')->value)
			$settings['useCompression']=true;
		else
			$settings['useCompression']=false;
			
		$init = array(
		    'model' => $model,
		    'attribute' => 'body',
		    // Optional config
		    'compressorRoute' => 'tinyMce/compressor',
		    //'spellcheckerUrl' => array('tinyMce/spellchecker'),
		    // or use yandex spell: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
		    'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
			'settings' => $settings,
		);
		if(!Config::model()->findByPk('htmlEditorUseCompressor')->value)
				unset($init['compressorRoute']);

		$this->widget('ext.tinymce.TinyMce', $init);
		echo $form->error($model,'body');
	?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Preview')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<div id="recipients_popup" class="modal" style="width:650px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="recipients_popup_body"></div>
</div>

