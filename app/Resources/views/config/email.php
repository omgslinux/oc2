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

/* @var $this ConfigController */
/* @var $model Config */
?>

<?php $this->inlineHelp=':manual:config:email'; ?>

<script>
$(function() {
	if(<?php echo Config::model()->findByPk('smtpMethod')->value; ?> == 1)
		$('#smtp_params').find('input').prop('disabled',true);
});
function changeSMTPMethod(el){
	updateBool(el);
	if(value == 1)
		$('#smtp_params').find('input').prop('disabled',true);
	else
		$('#smtp_params').find('input').prop('disabled',false);
}
function updateNoReply(){
		value = $('#value_emailNoReply').val();
		$('#noReply').html("<?php echo __('Send as').':&ensp;&ensp;';?>"+value);
}
function sendTestEmail(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl;?>/email/test/'+$('#subject').val(),
		type: 'POST',
		data: {	
				'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>'
		},
		error: function() {
			alert("Error on test email");
		},
		complete: function(){ window.location.reload(); }
	});
}
</script>

<?php $this->renderPartial('_title', array('paramGroup'=>__('Email')));?>

<div class="parameterGroup">
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpMethod'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="smtpMethod" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />Remote SMTP
		<input type="radio" name="smtpMethod" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Local Sendmail
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:changeSMTPMethod(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('emailNoReply'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this, function(){ updateNoReply();}); return false;"/>
		<div class="progress"></div>
	</div>
	
	<?php
	$user = User::model()->findByPk(Yii::app()->user->getUserID());
	if ($user===null){
		throw new CHttpException(404,'User does not exist.');
	}
	?>
	<div class="param" style="margin-top:40px">
	<span class="paramDescription" style="font-size:1.4em"><?php echo __('Send a test email');?></span>
	<p style="margin-top:10px">
	<span id="noReply"><?php echo __('Send as').':&ensp;&ensp;'.Config::model()->findByPk('emailNoReply')->value;?></span><br />
	<?php echo __('Send to').':&ensp;&ensp;'.$user->email;?><br />
	Subject:&ensp;&ensp;<input style="width:120px" id="subject" type="text" value = "test_1"/>
	<input type="button" value="test" onClick="js:sendTestEmail(); return false;"/>
	</p>
	</div>
	
</div>


<div id="smtp_params" class="parameterGroup">
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpAuth'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="smtpAuth" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />No
		<input type="radio" name="smtpAuth" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Yes
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateBool(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpHost'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpPassword'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpPort'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpSecure'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('smtpUsername'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
</div>


<?php if(Yii::app()->user->hasFlash('success')):?>
	<script>
		$(function() { 
			$(".flash-success").slideDown('fast');		
			setTimeout(function() {
				$('.flash-success').slideUp('fast');
    		}, 4500);
		});
	</script>
    <div class="flash-success" style="display:none">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>


<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>
