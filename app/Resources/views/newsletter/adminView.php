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

/* @var $this NewsletterController */
/* @var $model Newsletter */


$this->menu=array(
	array('label'=>__('Create bulk email'), 'url'=>array('create')),
	array('label'=>__('Manage bulk email'), 'url'=>array('admin')),
);

if($model->sent == 0){
	$delete = array(
			array('label'=>__('Delete draft'), 'url'=>'#', 'linkOptions'=>array(
																			'submit'=>array('delete',
																					'id'=>$model->id
																			),
																			'csrf'=>true,
																			'confirm'=>__('Are you sure you want to delete this item?'))
																		));
	array_splice( $this->menu, 0, 0, $delete );
}
$this->inlineHelp=':manual:newsletter:adminview';
?>

<style>           
.outer{width:100%; padding: 0px; float: left;}
.left{width: 38%; float: left;  margin: 0px;}
.right{width: 58%; float: left; margin: 0px;}
.clear{clear:both;}

#recipients_link{
	cursor:pointer;
	text-decoration:underline;
}
</style>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function showRecipients(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/newsletter/showRecipients/<?php echo $model->id?>',
		type: 'GET',
		success: function(data){
			if(data != 0){
				$("#recipients_popup_body").html(data);
				$('#recipients_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show recipients");
		}
	});
}
function send(){
	$('input').prop('disabled', true);
	$('#loading').show();
	
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl;?>/newsletter/send/<?php echo $model->id?>',
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		error: function() {
			alert("Error on send email");
		},
		complete: function(){ window.location.reload(); }
	});
}
</script>

<h1 class="sub_title"><?php echo CHtml::encode($model->subject);?></h1>
<div class="email">

<div class="details outer">	
<div class="form">

<div class="left">
	<p style="margin-bottom:5px">
	<b><?php echo CHtml::encode($model->getAttributeLabel('created'));?>:</b>
	<?php echo CHtml::encode($model->created); ?><br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('sent'));?>:</b>
	<?php echo CHtml::encode($model->getHumanSentValues($model->sent));?><br />


	<b><?php echo CHtml::encode($model->getAttributeLabel('sender'));?>:</b>
	<?php echo CHtml::encode($model->sender0->fullname);?><br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('sent_as')); ?>:</b>
	<?php echo CHtml::encode($model->sent_as); ?><br />

	<b><?php echo $total_recipients.' '.__('BCC Recipients');?></b>:
	<span id="recipients_link" onClick="js:showRecipients();">
	<?php echo __('Show');?>
	</span>
	</p>
</div>

<div class="right" style="margin-top:15px">

<?php if($model->sent == 0){

echo CHtml::button(__('Edit draft'), array('onclick'=>'js:document.location.href="'.Yii::app()->request->baseUrl.'/newsletter/update/'.$model->id.'"'));
echo CHtml::button(__('Publish now'), array('onclick'=>'js:send();','style'=>'margin-left:100px;'));
echo '<img id="loading" src="'.Yii::app()->request->baseUrl.'/images/small_loading.gif" style="vertical-align:middle;margin-left:15px;display:none"/>';

}?>
</div>
</div>
</div>

<div class="clear"></div>
<p><?php echo $model->body; ?></p>
</div>

<div id="recipients_popup" class="modal" style="width:650px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="recipients_popup_body"></div>
</div>


<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>
