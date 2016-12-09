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

/* @var $this EnquiryController */
/* @var $model Enquiry */

$this->menu=array(
	//array('label'=>__('View enquiry'), 'url'=>array('teamView', 'id'=>$model->id)),
	array('label'=>__('Sent emails'), 'url'=>array('/email/index/', 'id'=>$model->id, 'menu'=>'team')),
	array('label'=>__('List enquiries'), 'url'=>array('assigned')),
);
$this->inlineHelp=':manual:enquiry:validate';
$this->viewLog='Enquiry|'.$model->id;

echo $this->renderPartial('_validationOptions');

?>

<style>           
	#yourOptions { font-size: 1.2em }
	#yourOptions li { margin-bottom: 20px; }
</style>

<script>
function validate(){
	$('#Enquiry_state').val('<?php echo ENQUIRY_ACCEPTED;?>');
	$('#enquiry-form').submit();
}
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'enquiry-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->hiddenField($model, 'state');?>

	<div class="title"><?php echo __('Validate enquiry');?></div>
	<p style="font-style:italic"><?php echo __('Please study the enquiry below before deciding on an option').'.'?></p>
	
	<?php
		echo '<div style="font-size:16px;">'.__('Who will reply to this enquiry?').'</div>';
		echo $form->radioButtonList($model,'addressed_to',
			$model->getHumanAddressedTo(),
			array('labelOptions'=>array('style'=>'display:inline'))
		);
	?>
	<p></p>

	<ol id="yourOptions">
		<?php
			$text = __('Tell %s you do not want to take responsibility of this enquiry');
			$text = str_replace('%s', $model->manager0->fullname, $text);
		?>
		<li><?php echo $text.'.';?></li>
		<li>
		<?php echo __('Accept the Enquiry').'. '.__('You accept this enquiry as valid').'.';?>
		<?php echo CHtml::button(__('Accept'),array('onclick'=>'js:validate();')); ?>
		</li>
		<li>
		<?php echo __('Reject the Enquiry').'. '.__('The enquiry is inappropriate').'.';?>
		<?php echo CHtml::button(Config::model()->findByPk('siglas')->value.' '.__('Reject'),array('onclick'=>'js:reject();')); ?>
		</li>
	</ol>

<?php $this->endWidget(); ?>
</div><!-- form -->

<p></p>

<?php echo $this->renderPartial('_teamView', array('model'=>$model)); ?>

<?php if(Yii::app()->user->hasFlash('prompt_email')):?>
    <div class="flash-notice">
		<?php echo Yii::app()->user->getFlash('prompt_email');?><br />
		<?php 
		$url=Yii::app()->request->baseUrl.'/email/create?enquiry='.$model->id.'&menu=team';
		?>
		<button onclick="js:window.location='<?php echo $url?>';">Sí</button>
		<button onclick="js:window.location='<?php echo Yii::app()->request->baseUrl;?>/enquiry/teamView/<?php echo $model->id?>'">No</button>
    </div>
<?php endif; ?>
