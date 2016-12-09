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

/* @var $this EmailTemplateController */
/* @var $model EmailTemplate */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'emailTemplate-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="title"><?php echo __('Template for')?> "<?php echo Enquiry::model()->getHumanStates($model->state); ?>"</div>

	<?php echo $form->hiddenField($model,'state'); ?>
	<div class="row">
		<?php
		$settings = array('theme_advanced_buttons1' => "undo,redo,|,bold,italic,underline,|,
														justifyleft,justifycenter,justifyright,|,
														bullist,numlist,|,outdent,indent,|,
														link,unlink,|,image",
							'convert_urls'=>true,
							'relative_urls'=>false,
							'remove_script_host'=>false,
							'theme_advanced_resize_horizontal' => 0,
							'theme_advanced_resize_vertical' => 0,
							'theme_advanced_resizing_use_cookie' => false,
							'width'=>'100%',
							'valid_elements' => "@[style],p,span,a[href|target=_blank],strong/b,div[align],br,ul,ol,li,img[*]",
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
		<?php echo CHtml::submitButton('Update'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
