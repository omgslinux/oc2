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

/* @var $this ReplyController */
/* @var $model Reply */
/* @var $form CActiveForm */

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reply-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>false,
)); ?>

	<?php echo $form->hiddenField($model,'enquiry');?>

	<div class="row">
		<?php echo $form->label($model,'created');
		if($model->enquiry0->addressed_to == OBSERVATORY)
			echo '<div class="hint">'.__('Date the Obseravatory replied').'</div>';
		else
			echo '<div class="hint">'.__('Date the Administration replied').'</div>';
		$this->widget('zii.widgets.jui.CJuiDatePicker',array(
				'model' => $model,
				'name'=>'Reply[created]',
				'value'=>$model->created,
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd',
				),
				'htmlOptions'=>array(
					'style'=>'height:20px;',
					'readonly'=>'readonly',
				),
		));
		?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php
		$settings = array('theme_advanced_buttons1' => "undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,
														justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,|,
														link,unlink",
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
		<?php echo CHtml::submitButton($model->isNewRecord ? __('Publish') : __('Update'));
		$cancelURL='/enquiry/teamView/'.$enquiry->id;
		?>
		<input type="button" value="<?php echo __('Cancel')?>" onclick="js:window.location='<?php echo Yii::app()->request->baseUrl?><?php echo $cancelURL?>';" />

	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->



