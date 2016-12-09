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
 
/* @var $this UserController */
/* @var $model User */

$languages = getLanguagesArray();
$solicit_membership = Config::model()->findByPk('membership')->value;
?>

<script>
function optout(){
	retVal = confirm("<?php echo __('Are you sure you want to delete your account?');?>");
	if( retVal == false ){
		return;
	}
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/user/optout',
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		success: function(){
			window.location.href = '<?php echo Yii::app()->request->baseUrl; ?>';					
		},
		error: function() { alert("error on opt out"); },
	});
}
</script>

<?php if($languages || $solicit_membership) { ?>
	<style>           
		.outer{width:100%; padding: 0px; float: left; margin-top:10px;}
		.left{width: 32%; float: left;  margin: 0px; margin-left:20px;}
		.middle{width: 32%; float: left;  margin: 0px; margin-left:40px;}
		.right{width: 22%; float: left; margin: 0px;}
		.clear{clear:both;}
	</style>
<?php } else { ?>
	<style>           
		.outer{width:100%; padding: 0px; float: left; margin-top:10px;}
		.left{width: 50%; float: left;  margin: 0px; margin-left:20px; }
		.middle{width: 45%; float: left;  margin: 0px;}
		.clear{clear:both;}
	</style>
<?php } ?>

<div style="margin:-10px 0 55px 0;">
<span id="datos_usuario" style="margin-top:-15px;cursor:auto;float:left"></span>
<h1 style="float:left"><?php echo __('Change your user information')?></h1>
<span class="link" style="float:right" onclick="js:optout();"><?php echo __('Delete my user account');?></span>
</div>
<div style="clear:both"></div>


<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="outer">
<div class="left">

	<p class="sub_title"><?php echo __('Your information')?></p>

	<?php /*echo $form->errorSummary($model);*/ ?>

	<div class="row">
		<?php echo $form->labelEx($model,'fullname'); ?>
		<?php echo $form->textField($model,'fullname',array('size'=>30,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'fullname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>30,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

</div>
<div class="middle">
<p class="sub_title"><?php echo __('Change password');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'new_password'); ?>
		<?php echo $form->passwordField($model,'new_password',array('autocomplete'=>'off'));	?>
		<?php echo $form->error($model,'new_password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password_repeat'); ?>
		<?php echo $form->passwordField($model,'password_repeat',array('autocomplete'=>'off')); ?>
		<?php echo $form->error($model,'password_repeat'); ?>
	</div>
</div>

<?php if($languages || $solicit_membership) { ?>
<div class="right">
<p class="sub_title" class="sub_title"><?php echo __('Details');?></p>
	<?php
		if($languages){
			echo '<div class="row">';
			echo $form->labelEx($model,'language');
			echo '<div class="hint">'.__('Your preferred language').'</div>';
			echo $form->dropDownList($model, 'language', $languages );
			echo '</div>';
		}	
	?>
	
	<?php if($solicit_membership){ ?>
	<div class="row">
		<label><?php echo __('Yes, I want to be a member').' '.$form->checkBox($model,'is_socio', array('checked'=>$model->is_socio));?></label>
		<a href="#" onClick="js:$('#membership_means').slideDown('fast');"><?php echo __('What does this mean?');?></a>
	</div>
	<?php } ?>
</div>
<?php } ?>

</div>
<div class="clear"></div>

	<?php if($solicit_membership){ ?>
	<div id="membership_means" style="display:none" class="horizontalRule">
	<p><?php echo  __('MEMBERSHIP_MSG'); ?></p>
	</div>
	<?php } ?>

	<div class="row buttons" style="text-align:center;padding-top:20px;">
		<?php echo CHtml::submitButton(__('Save')); ?>
		<input type="button" onclick="window.location='<?php echo Yii::app()->request->baseUrl;?>/user/panel'" value="<?php echo __('Cancel')?>" />
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php if(Yii::app()->user->hasFlash('success')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-success').slideUp('fast');
    	}, 5000);
		});
	</script>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>
