<?php

/**
 * OCAX -- Citizen driven Observatory software
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
	array('label'=>__('Sent emails'), 'url'=>array('/email/index/', 'id'=>$model->id, 'menu'=>'manager')),
	array('label'=>__('Manage enquiries'), 'url'=>array('admin')),
);
$this->inlineHelp=':manual:enquiry:manage';
$this->viewLog='Enquiry|'.$model->id;

echo $this->renderPartial('_validationOptions');
?>

<style>           
	#yourOptions { font-size: 1.2em }
	#yourOptions li { margin-bottom: 10px;}
</style>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function showEnquiry(enquiry_id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/getMegaDelete/'+enquiry_id,
		type: 'GET',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#mega_delete_content").html(data);
				$('#mega_delete_button').attr('enquiry_id', enquiry_id)
				$('#mega_delete').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show mega delete");
		}
	});
}
function megaDelete(el){
	$("#enquiry-form :input").attr("disabled", true);
	enquiry_id = $(el).attr('enquiry_id');
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/megaDelete/'+enquiry_id,
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		success: function(data){
			if($('#disable_user').is(':checked') == true)
				disableUser();
			else
				window.location = '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/admin';
		},
		error: function() {
			alert("Error on megaDelete");
		}
	});

}
function disableUser(){
	user_id = <?php echo $model->user; ?>;
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/user/disable/'+user_id,
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		success: function(data){
			window.location = '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/admin';
		},
		error: function() {
			alert("Error on disableUser");
		}
	});
}
</script>

<h1><?php echo __('Manage enquiry');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'enquiry-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p style="font-style: italic;">
	<?php echo __('Please study the enquiry below before continuing').'.'?>
	</p>

	<?php
		echo '<div style="font-size:16px;">'.__('Who will reply to this enquiry?').'</div>';
		echo $form->radioButtonList($model,'addressed_to',
			$model->getHumanAddressedTo(),
			array('labelOptions'=>array('style'=>'display:inline'))
		);
	?>
	<p></p>

	<ol id="yourOptions">
		<li>
		<?php
			echo '<div>'.__('Assign enquiry').': ';

			$data=CHtml::listData($team_members,'id', 'fullname');
			echo $form->dropDownList($model, 'team_member', $data, array('prompt'=>__('Not assigned')));
			
			if(!$model->team_member)
				echo CHtml::submitButton(__('Assign'));
			else
				echo CHtml::submitButton(__('Change team member'));
			echo '</div>';
		?>
		</li>
		<li>
		<?php echo __('Reject the enquiry').'. '.__('The enquiry is inappropriate').'.';?>
		<?php echo CHtml::button(Config::model()->findByPk('siglas')->value.' '.__('Reject'),array('onclick'=>'js:reject();')); ?>
		</li>
		<li>
			<div style="float:left">
			<?php
				echo __('The enquiry is spam').'.<br />'.__('Delete the enquiry and').' ';
				echo '<input type="checkbox" id="disable_user" value="0"> '.__('disable the user');
			?>
			</div>
			<div style="float:left;margin: 20px 0 0 20px;">
			<?php echo CHtml::button(__('Delete'),array('onclick'=>'js:showEnquiry('.$model->id.');')); ?>
			</div>
			<div style="clear:both"></div>
		</li>
	</ol>

<?php echo $form->hiddenField($model,'state');?>
<?php $this->endWidget(); ?>
</div><!-- form -->

<p></p>
<?php echo $this->renderPartial('_teamView', array('model'=>$model)); ?>

<?php if(Yii::app()->user->hasFlash('prompt_email')):?>
    <div class="flash-notice">
		<?php echo Yii::app()->user->getFlash('prompt_email');?><br />
		<?php 
		$url=Yii::app()->request->baseUrl.'/email/create?enquiry='.$model->id.'&menu=manager';
		?>
		<button onclick="js:window.location='<?php echo $url?>';"><?php echo __('Yes');?></button>
		<button onclick="window.location='<?php echo Yii::app()->request->baseUrl;?>/enquiry/admin'">
		<?php echo __('No');?>
		</button>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('success')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-success').slideUp('fast');
    	}, 3000);
		});
	</script>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('notice')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-notice').slideUp('fast');
    	}, 3000);
		});
	</script>
    <div class="flash-notice">
		<?php echo Yii::app()->user->getFlash('notice');?>
    </div>
<?php endif; ?>

<div id="mega_delete" class="modal" style="display:none;width:850px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="mega_delete_content"></div>
</div>
