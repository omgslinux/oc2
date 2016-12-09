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

/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Config::model()->findByPk('siglas')->value . ' - '.__('Login');
?>

<style>           
	.outer{width:100%; padding: 0px; float: left;}
	.left{width: 43%; float: left;  margin: 0px; padding-left:25px;}
	.right{width: 53%; float: left; margin: 0px;}
	.clear{clear:both;}
</style>

<script>
function showPasswdInstructions(){
	$('#passwd_instructions_link').replaceWith($('#passwd_instructions'));
	$('#passwd_instructions').show();
}
function requestNewPasswd(){
	if($('#email').val() == ''){
		alert("<?php echo __('Please enter your email address');?>");
		return;
	}
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/site/requestNewPassword',
		type: 'POST',
		data: { 
				'email': $('#email').val(),
				'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>'
			},
		beforeSend: function(){ $('#loading').show(); $('#email_button').prop('disabled', true);  },
		success: function(data){
			$('#loading').hide();
			$('#email_button').prop('disabled', false); 
			$('#passwd_text').hide();
			$('#passwd_text').html(data);
			$('#passwd_text').fadeIn('fast');
		},
		error: function() {
			alert("Error on request new password");
		}
	});
}
</script>


<div class="outer">
<div class="left">
<h1><?php echo __('Login')?><i class="icon-user-female"></i></h1>
	
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<?php if ($model->scenario == 'withCaptcha' && CCaptcha::checkRequirements()): ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'verifyCode'); ?>
			<div>
				<?php $this->widget('CCaptcha'); ?>
				<?php echo $form->textField($model, 'verifyCode'); ?>
			</div>
			<?php echo $form->error($model, 'verifyCode'); ?>
		</div>
	<?php endif; ?>


	<div class="row buttons">
		<?php echo CHtml::submitButton(__('Login')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<p></p>

</div>
<div class="right">
<p style="font-size:1.5em;margin-bottom:10px;"><?php echo __('Still haven\'t got an account?');?></p>
<p>
	<h1 style="margin-top:-15px"><a href="<?php echo Yii::app()->request->baseUrl; ?>/site/register"><?php echo __('Sign up');?></a></h1>
</p>
<br/>
<p style="font-size:1.5em;margin-bottom:10px;"><?php echo __('Forgotten your password?');?></p>

<p>
<a id="passwd_instructions_link" class="link" onClick="js:showPasswdInstructions()"><?php echo __('Follow these instructions');?></a>
<div id="passwd_instructions" class="form" style="display:none">
<span id="passwd_text"><?php echo __('Enter your email address and we will send you a link');?></span><br />
<input id="email" type="text" size="35" style="margin-right:10px;" /><button id="email_button" onClick="js:requestNewPasswd();"><?php echo __('Send');?></button>
<img id="loading" src="<?php echo Yii::app()->request->baseUrl;?>/images/small_loading.gif" style="vertical-align:middle;margin-left:10px;display:none"/>
</div>
</p>

</div>
</div>

<?php if(Yii::app()->user->hasFlash('error')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-error').slideUp('fast');
    	}, 3500);
		});
	</script>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>


