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
	//array('label'=>'Ver enquiry', 'url'=>array('/enquiry/adminView', 'id'=>$model->id)),
	array('label'=>__('Manage enquiry'), 'url'=>array('manage', 'id'=>$model->id)),
	array('label'=>__('Sent emails'), 'url'=>array('/email/index/', 'id'=>$model->id, 'menu'=>'manager')),
	array('label'=>__('List enquiries'), 'url'=>array('admin')),
);
$this->inlineHelp=':manual:enquiry:adminview';
$this->viewLog='Enquiry|'.$model->id;
?>

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
	enquiry_id = $(el).attr('enquiry_id');
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/megaDelete/'+enquiry_id,
		type: 'POST',
		success: function(data){
			window.location = '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/admin';
		},
		error: function() {
			alert("Error on megaDelete");
		}
	});

}
</script>

<?php echo $this->renderPartial('_teamView', array('model'=>$model)); ?>

<?php if(Yii::app()->user->hasFlash('prompt_email')):?>
    <div class="flash-notice">
		<?php echo Yii::app()->user->getFlash('prompt_email');?><br />
		<?php 
		$url=Yii::app()->request->baseUrl.'/email/create?enquiry='.$model->id.'&menu=manager';
		?>
			<button onclick="js:window.location='<?php echo $url?>';">Sí</button>
			<button onclick="$('.flash-notice').slideUp('fast')">No</button>
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


<div id="mega_delete" class="modal" style="display:none;width:850px;">
	<img class="bClose" src="<?php echo Yii::app()->request->baseUrl; ?>/images/close_button.png" />
	<div id="mega_delete_content"></div>
</div>


