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

/* @var $this BudgetDescriptionController */
/* @var $model BudgetDescription */
/* @var $form CActiveForm */

$criteria=new CDbCriteria;
$criteria->addCondition('csv_id = :csv_id AND language = :language AND modified IS NOT NULL');
$criteria->params[':csv_id'] = $model->csv_id;
$criteria->params[':language'] = $model->language;
$common_desc = BudgetDescCommon::model()->find($criteria);
$state_desc = BudgetDescState::model()->findByAttributes(array('csv_id'=>$model->csv_id,'language'=>$model->language));

?>

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/budgetDescription.css" />

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function tabMenu(el, div_id){
	$(el).parent().find('li').removeClass('activeItem');
	$(el).addClass('activeItem');
	$('.tabMenuContent').hide();
	$('#'+div_id).show();
}
function changeLanguage(lang){
	<?php
	echo 'location.href="'.Yii::app()->request->baseUrl.'/budgetDescription/modify?csv_id='.$model->csv_id.'&lang="+lang;';
	?>
}
function viewDescription(){
	$('#description_popup').bPopup({
		  modalClose: false
		, follow: ([false,false])
		, positionStyle: 'absolute'
		, modelColor: '#ae34d5'
		, speed: 10
	});
}
$(function() {
	$("#BudgetDescLocal_concept").mousedown(function() {
		if($(this).val() == '')
			$(this).val(<?php echo json_encode($model->concept);?>);
	});
	$("#BudgetDescLocal_label").mousedown(function() {
		if($(this).val() == '')
			$(this).val(<?php echo json_encode($model->label);?>);
	});
});

function previewLocalDescription() {
	$('#description_popup_content_saved_desc').hide();
	content = "<div class=\"modalTitle\"><?php echo __('Explication preview');?>" + '</div>';
	content = content + '<div class="budgetExplication">' + tinyMCE.get('BudgetDescLocal_description').getContent() + '</div>';
	$('#description_popup_content_desc_preview').html(content);
	$('#description_popup_content_desc_preview').show();
	viewDescription();
}
function viewSavedLocalDescription() {
	$('#description_popup_content_desc_preview').hide();
	$('#description_popup_content_saved_desc').show();
	viewDescription();
}
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'budget-description-form',
	'enableAjaxValidation'=>false,
));

$fieldsForDisplay = $model->getDescriptionFields($model->csv_id,$model->language);

echo '<div class="title">'.__('Budget description').'</div>';

echo '<div style="font-size:16px; margin-top:-10px">';
echo __('Label');
echo ($fieldsForDisplay['label']) ? '<i class="icon-circle green"></i>' : '<i class="icon-circle-empty green"></i>';
echo __('Concept');
echo ($fieldsForDisplay['concept']) ? '<i class="icon-circle green"></i>' : '<i class="icon-circle red"></i>';
echo $model->getAttributeLabel('description');
echo ($fieldsForDisplay['description']) ? '<i class="icon-circle green"></i>' : '<i class="icon-circle red"></i>';
echo '</div>';

echo '<p style="margin:0px;">'.__('Used where').' '.$model->whereUsed().'</p>';
?>

<div>
	<div class="row left" style="width:350px">
		<?php echo $form->labelEx($model,'csv_id'); ?>
		<?php echo $form->textField($model,'csv_id',array('style'=>'width:330px','disabled'=>1)); ?>
		<?php echo $form->hiddenField($model,'csv_id'); ?>
		<?php echo $form->error($model,'csv_id'); ?>
	</div>

	<div class="row left" style="width:160px">
		<?php echo $form->labelEx($model,'code'); ?>
		<?php echo $form->textField($model,'code',array('style'=>'width:125px','maxlength'=>32,'disabled'=>1)); ?>
		<?php echo $form->hiddenField($model,'code'); ?>
		<?php echo $form->error($model,'code'); ?>
	</div>
	<?php
		$listData = getLanguagesArray();
		if ($listData || $model->language != Yii::app()->language){
			if (!$listData){
				$listData = array();
				$listData[Yii::app()->language] = Yii::app()->language;
				$listData[$model->language] = $model->language;
			}else{
				if (!array_key_exists($model->language, $listData)){
					$listData[$model->language] = $model->language;
				}
			}
			echo '<div class="row left" style="width:170px">';
			echo $form->labelEx($model,'language');
			echo $form->dropDownList($model, 'language', $listData,	array('onChange'=>'js:changeLanguage(this.value);'));
			echo '</div>';
		}else{
			echo $form->hiddenField($model,'language');
		}
	?>
</div>
<div class="clear"></div>

<div class="tabMenu" style="margin-bottom:15px">
	<ul>
	<li id="state_tab" onClick="js:tabMenu(this, 'state_desc');">
		<?php echo __('State description');
		if($state_desc && $state_desc->description)
			echo '<i class="icon-circle green"></i>';
		elseif($state_desc)
			echo '<i class="icon-dot-circled green"></i>';
		else
			echo '<i class="icon-circle red"></i>';
		?>
	</li>
	<li id="common_tab" onClick="js:tabMenu(this, 'common_desc');">
		<?php echo __('Common description');
		if($common_desc && $common_desc->description)
			echo '<i class="icon-circle green"></i>';
		elseif($common_desc)
			echo '<i class="icon-dot-circled green"></i>';
		else
			echo '<i class="icon-circle red"></i>';
		?>
	</li>
	<li id="local_tab" class="activeItem" onClick="js:tabMenu(this, 'local_desc');">
		<?php echo __('Local description');
		if($model->id && $model->description)
			echo '<i class="icon-circle green"></i>';
		elseif($model->id)
			echo '<i class="icon-dot-circled green"></i>';
		else
			echo '<i class="icon-circle-empty green"></i>';
		?>
	</li>
	</ul>
</div>

<div id="local_desc" class="tabMenuContent" style="display:block;"> <!-- local_desc start -->
	<?php
	if(!$fieldsForDisplay['label'] && !$fieldsForDisplay['concept'] && !$fieldsForDisplay['description']){
		if($model->label || $model->concept)
			echo '<div style="font-size:16px;margin-top:-10px;">'.
				'<i class="icon-attention green"></i>'.__('Using data imported with CSV files').'.</div>';
	}
	?>
<div>
	<div class="row left" style="width:220px">
		<?php echo $form->labelEx($model,'label'); ?>
		<div class="hint"><?php echo __('Concept, Subconcept, Article').'..';?></div>
		<?php
		($model->isNewRecord) ?	$value = '' : $value = $model->label;
		echo $form->textField($model,'label', array('value'=>$value,'style'=>'width:200px','maxlength'=>255));
		?>
		<?php echo $form->error($model,'label'); ?>
	</div>

	<div class="row left" style="width:505px">
		<?php echo $form->labelEx($model,'concept'); ?>
		<div class="hint"><?php echo __('Concept of this budget');?></div>
		<?php
		($model->isNewRecord) ?	$value = '' : $value = $model->concept;
		echo $form->textField($model,'concept', array('value'=>$value,'style'=>'width:500px','maxlength'=>255));
		?>
		<?php echo $form->error($model,'concept'); ?>
	</div>
</div>
<div class="clear"></div>

<?php
$settings=array('convert_urls'=>true,
				'relative_urls'=>false,
				'remove_script_host'=>false,
				//'entity_encoding' => "raw",
				'theme_advanced_resize_horizontal' => 0,
				'theme_advanced_resize_vertical' => 0,
				'theme_advanced_resizing_use_cookie' => false,
				'width'=>'100%',
				'height' => 300,
				'valid_elements' => "@[style],p,span,a[href|target=_blank],strong/b,div[align],br,ul,ol,li",
				'theme_advanced_buttons1' => "undo,redo,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,
												justifyright,|,bullist,numlist,|,outdent,indent,|,
												link,unlink,|,image,|,code",
			);
if(Config::model()->findByPk('htmlEditorUseCompressor'))
	$settings['useCompression']=true;
else
	$settings['useCompression']=false;

$init = array(
    'model' => $model,
    'attribute' => 'description',
    // Optional config
    'compressorRoute' => 'tinyMce/compressor',
    //'spellcheckerUrl' => array('tinyMce/spellchecker'),
    // or use yandex spell: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
    'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
	'settings' => $settings,
);

if(!Config::model()->findByPk('htmlEditorUseCompressor')->value)
	unset($init['compressorRoute']);

echo '<div class="row">';
	echo '<div style="float:left;">';
	if($model->id && !$model->description)
		echo $form->labelEx($model,'description', array('label' => $model->getAttributeLabel('description').' <i class="icon-circle-empty green"></i>'));
	elseif($model->id && $model->description)
		echo $form->labelEx($model,'description', array('label' => $model->getAttributeLabel('description').' <i class="icon-circle green"></i>'));
	else
		echo $form->label($model,'description');
	echo '<span class="hint">'.__('Citizen friendly explication').'</span>';
	echo '</div>';
	echo '<div style="float:right">';
	echo '<input type="button" value="'.__('Preview the explication').'" onClick="js:previewLocalDescription();" />';
	echo '</div>';
	echo '<div class="clear"></div>';

	$this->widget('ext.tinymce.TinyMce', $init);
	echo $form->error($model,'description');
echo '</div>';
?>

<div class="row buttons">
	<?php echo CHtml::submitButton(__('Save changes')); ?>
</div>

</div><!-- local_desc end -->

<div id="common_desc" class="tabMenuContent"> <!-- common_desc start -->
<?php
	if($common_desc)
	{
?>
	<div class="row left" style="width:220px">
		<?php echo $form->labelEx($model,'label'); ?>
		<div class="hint"><?php echo __('Concept, Subconcept, Article').'..';?></div>
		<span style="font-size:18px"><?php echo $common_desc->label;?></span>

	</div>

	<div class="row left" style="width:505px">
		<?php echo $form->labelEx($model,'concept'); ?>
		<div class="hint"><?php echo __('Concept of this budget');?></div>
		<span style="font-size:18px"><?php echo $common_desc->concept;?></span>

	</div>
	<div class="clear"></div>
	<div class="row">
	<?php
		if(!$common_desc->description)
			echo $form->labelEx($model,'description', array('label' => $model->getAttributeLabel('description').' <i class="icon-dot-circled green"></i>'));
		else{
			echo $form->labelEx($model,'description', array('label' => $model->getAttributeLabel('description').' <i class="icon-circle green"></i>'));
			echo '<div style="font-size:16px">'.$common_desc->description.'</div>';
		}
	?>
	</div>
	<div class="clear"></div>
<?php
	}else
		echo '<div class="sub_title" style="margin-top:60px;">'.__("No common description in the database").'.</div>';
?>

</div><!-- common_desc end -->

<div id="state_desc" class="tabMenuContent"> <!-- state_desc start -->
<?php
	if($state_desc)
	{
?>
	<div class="row left" style="width:220px">
		<?php echo $form->labelEx($model,'label'); ?>
		<div class="hint"><?php echo __('Concept, Subconcept, Article').'..';?></div>
		<span style="font-size:18px"><?php echo $state_desc->label;?></span>

	</div>

	<div class="row left" style="width:505px">
		<?php echo $form->labelEx($model,'concept'); ?>
		<div class="hint"><?php echo __('Concept of this budget');?></div>
		<span style="font-size:18px"><?php echo $state_desc->concept;?></span>

	</div>
	<div class="clear"></div>
	<div class="row">
	<?php
		if(!$state_desc->description)
			echo $form->labelEx($model,'description', array('label' => $model->getAttributeLabel('description').' <i class="icon-circle-empty green"></i>'));
		else{
			echo $form->labelEx($model,'description', array('label' => $model->getAttributeLabel('description').' <i class="icon-circle green"></i>'));
			echo '<div style="font-size:16px">'.$state_desc->description.'</div>';
		}
	?>
	</div>
	<div class="clear"></div>
<?php
	}else
		echo '<div class="sub_title" style="margin-top:60px;">'.__('No state description in the database').'</div>';
?>

</div><!-- state_desc end -->

<?php $this->endWidget(); ?>
</div><!-- form -->

<div id="description_popup" class="modal" style="width:850px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="description_popup_content">
	<div id="description_popup_content_saved_desc">
	<?php $this->renderPartial('_view',array('model'=>$model,'fieldsForDisplay'=>$fieldsForDisplay));?>
	</div>
	<div id="description_popup_content_desc_preview"></div>
	</div>
</div>


<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>
