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

/* @var $this EmailController */
/* @var $model Email */
/* @var $form CActiveForm */

if($returnURL == 'enquiry/teamView'){
	$this->menu=array(
		array('label'=>__('View enquiry'), 'url'=>array('/enquiry/teamView', 'id'=>$enquiry->id)),
		array('label'=>__('Edit enquiry'), 'url'=>array('/enquiry/edit', 'id'=>$enquiry->id)),
		array('label'=>__('Sent emails'), 'url'=>array('/email/index/', 'id'=>$enquiry->id, 'menu'=>'team')),
		array('label'=>__('List enquiries'), 'url'=>array('/enquiry/assigned')),
);
}
if($returnURL == 'enquiry/adminView'){
	$this->menu=array(
		array('label'=>'Emails sent', 'url'=>array('/email/index/', 'id'=>$enquiry->id, 'menu'=>'manager')),
		array('label'=>'List enquirys', 'url'=>array('/enquiry/admin')),
);
}
?>

<style>
#recipients_link{
	cursor:pointer;
	text-decoration:underline;
}
</style>

<script>
function toggleRecipients(){
	if ($('#recipients').is (':visible'))
		$('#recipients_link').html('Show');
	else
		$('#recipients_link').html('Hide');
	$('#recipients').toggle();
}
function submitForm(){
	$('.loading_gif').show();
	$('input[type=button]').prop("disabled",true);
	document.forms['email-form'].submit();
}
</script>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-form',
	//'enableAjaxValidation'=>true,
	//'enableClientValidation'=>false,
	'action'=>Yii::app()->baseUrl.'/email/create',
)); ?>

	<div class="title"><?php echo __('Send email')?></div>

	<?php echo $form->hiddenField($model,'enquiry'); ?>
	<input type="hidden" name="Email[returnURL]" value="<?php echo $returnURL;?>" />

	<div class="row">
		<?php
		$sender=User::model()->findByPk($model->sender);
		if ($sender===null){
			throw new CHttpException(404,'The requested Page does not exist.');
		}
		$senderList=array(	0=>Config::model()->findByPk('emailNoReply')->value,
							$sender->id=>$sender->email);
		if($enquiry->state == ENQUIRY_ASSIGNED)
			$senderList=array_reverse($senderList);
		$model->sender=0;
		?>
		
		<?php echo $form->labelEx($model,'sender'); ?>
		<?php echo $form->dropDownList($model, 'sender', $senderList );?>
		<?php echo $form->error($model,'sender'); ?>
	</div>


	<div class="row">
		<?php
			$subscribedUsers = $enquiry->getEmailRecipients();
			$model->recipients='';
			foreach($subscribedUsers as $subscribed)
				$model->recipients=$model->recipients.' '.$subscribed->email.',';
			$model->recipients = substr_replace($model->recipients ,"",-1);
			echo $form->hiddenField($model,'recipients');
			
			echo '<p><b>'.count($subscribedUsers).' BCC Recipients</b> <span id="recipients_link" onClick="js:toggleRecipients();">Show</span>';
			echo '<div id="recipients" style="background-color:white;padding:4px;display:none">'.$model->recipients.'</div>';
		?>
		</p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php
		$settings = array('theme_advanced_buttons1' => "undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,
														justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,|,
														link,unlink",
							'convert_urls'=>true,
							'relative_urls'=>false,
							'remove_script_host'=>false
						);
		if(Config::model()->findByPk('htmlEditorUseCompressor')->value)
			$settings['useCompression']=true;
		else
			$settings['useCompression']=false;
			
		$init = array(
		    'model' => $model,
		    'attribute' => 'body',
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
		<input type="button" onclick="submitForm()" value="<?php echo $model->isNewRecord ? __('Send') : __('Save'); ?>">
		<input type="button" value="Cancel" onclick='js:window.location="<?php echo Yii::app()->baseUrl.'/'.$returnURL.'/'.$enquiry->id;?>";' />
		<img style="vertical-align:middle;display:none" class="loading_gif" src="<?php echo Yii::app()->request->baseUrl;?>/images/loading.gif" />
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<p></p>
<?php echo $this->renderPartial('//enquiry/_teamView', array('model'=>$enquiry)); ?>


