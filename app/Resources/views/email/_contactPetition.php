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

/* @var $this EmailController */
/* @var $model Email */
/* @var $form CActiveForm */
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-form',
	//'enableAjaxValidation'=>true,
	//'enableClientValidation'=>false,
	//'action'=>Yii::app()->baseUrl.'/email/create',
)); ?>

	<div class="modalTitle"><?php echo __('Petition to make contact via email')?></div>

	<?php
		echo $form->hiddenField($model,'enquiry');
		$model->recipients=$recipient->email;
		echo $form->hiddenField($model,'recipients');

		$user = User::model()->findByPk(Yii::app()->user->getUserID());
		if ($user===null){
			throw new CHttpException(404,'The requested User does not exist.');
		}
		$block = Yii::app()->createAbsoluteUrl('user/block/'.$user->username);
		$block = '<a href="'.$block.'">'.$block.'</a>';

		$model->title=	'<p>'.__('Hello').' '.$recipient->fullname.',</p><p>'.
						$user->fullname.', '.
						str_replace("%s", Config::model()->findByPk('siglas')->value, __('a user like you at the %s')).', '.
						__('would like to contact you privately via email').'.<br />'.
						__('However, we do not share users\' email addresses').'.</p><p>'.
						__('If you wish, you may make contact yourself with').' '.$user->fullname.'; '.$user->email.'</p>'.
						//__('If you think this user is spamming you, you can block future petitions at this link').': '.$block.'</p>'.
						'<p>'.__('Kind regards').',<br />'.Config::model()->getObservatoryName().'</p>';

		echo '<div style="	background-color:white;
							margin:0px -10px 0px -10px;
							padding:5px">'.$model->title.'</div>';		
		echo $form->hiddenField($model,'title');

		echo '<div class="row">';
		echo '<b>'.$user->fullname.' '.__('says').'...</b><br />';
		echo '<div class="hint">'.__('Optionally, you may attach text to this email').'</div>';
		echo $form->textArea($model, 'body', array('style'=>'width: 690px; height: 120px;'));
		echo '</div>';

	?>


	<div id="contact_petition_buttons" class="row buttons">
		<input type="button" value="<?php echo __('Send email')?>" onClick="js:sendContactForm('email-form');"/>
		<input type="button" value="<?php echo __('Cancel')?>" onClick="js:$('#contact_petition').bPopup().close()" />
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<style>
.contact_form_bottom{
	display:none;
	margin:0 -10px -10px -10px;
	padding:15px;
	border-top: 1px solid #CDCBC9;
	text-align:center;
	font-size:1.4em;
	background-color:white;
}
</style>
<div id="contact_petition_sending" class="contact_form_bottom" style="color:amber">
<?php echo __('Sending email')?>&nbsp;&nbsp;
<?php echo '<img style="vertical-align:text-middle;"
			src="'.Yii::app()->request->baseUrl.'/images/loading.gif" />'?>
</div>
<div id="contact_petition_sent" class="contact_form_bottom">
<?php echo __('Email sent')?>
<i class="icon-ok-circled green" style="margin-left:10px;"></i>
</div>
<div id="contact_petition_error" class="contact_form_bottom" style="color:red">
</div>


