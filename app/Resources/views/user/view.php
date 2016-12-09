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

$this->menu=array(
	array('label'=>__('Change user\'s roles'), 'url'=>array('updateRoles', 'id'=>$model->id)),
	array('label'=>__('List all users'), 'url'=>array('admin')),
);

if(Yii::app()->user->getUserID() != $model->id){
	if(!$model->enquirys){
		$item= array(	array(	'label'=>__('Delete user'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('delete','id'=>$model->id), 'csrf'=>true,'confirm'=>__('Are you sure you want to delete this item?'))
						));
		array_splice( $this->menu, 1, 0, $item );
	}
	if($model->is_disabled){
		$item = array( array(	'label'=>__('Enable user'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('enable', 'id'=>$model->id), 'csrf'=>true)));
		array_splice( $this->menu, 1, 0, $item );
	}else{
		$item = array( array(	'label'=>__('Disable user'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('disable', 'id'=>$model->id), 'csrf'=>true)));
		array_splice( $this->menu, 1, 0, $item );	
	}
}

$this->inlineHelp=':manual:user:view';
$this->viewLog='User|'.$model->id;
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function showEnquiry(enquiry_id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/getMegaDelete/'+enquiry_id,
		type: 'GET',
		//dataType: 'json',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#mega_delete_content").html(data);
				$('#mega_delete_button').attr('enquiry_id', enquiry_id);
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
		//data: { 'id' : enquiry_id },
		//dataType: 'json',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			$('#mega_delete').bPopup().close();
			if(data != 0){
				window.location = '<?php echo Yii::app()->request->baseUrl; ?>/user/view/<?php echo $model->id;?>';
			}
		},
		error: function() {
			alert("Error on megaDelete");
		}
	});
}

</script>

<div class="form">
<div class="title"><?php echo __('User details'); ?></div>

<div class="row">
<?php $this->widget('zii.widgets.CDetailView', array(
	'cssFile' => Yii::app()->request->baseUrl.'/css/pdetailview.css',
	'data'=>$model,
	'attributes'=>array(
		'username',
		'fullname',
		'email',
		'joined',
		'is_socio',
		//'is_description_editor',
		'is_team_member',
		'is_editor',
		'is_manager',
		'is_admin',
	),
)); ?>
</div>
</div>
<p></p>
<?php
if($enquirys->getData()){
echo '<div class="horizontalRule" style="margin-top:20px"></div>';
echo '<span style="font-size:1.5em">'.__('Enquiries made by').' '.$model->fullname.'</span>';
$this->widget('PGridView', array(
	'id'=>'enquiry-grid',
	'dataProvider'=>$enquirys,
    'onClick'=>array(
        'type'=>'javascript',
        'call'=>'showEnquiry',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
			array(
				'header'=>__('Enquiries'),
				'name'=>'title',
				'value'=>'$data[\'title\']',
			),
			'created',
			array(
				'header'=>__('State'),
				'name'=>'state',
				'type' => 'raw',
				'value'=>'$data->getHumanStates($data[\'state\'])',
			),
            array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
)));
}else
echo '<p style="font-size:1.5em">'.$model->fullname.' '.__('has not made a enquiry').'</p>';
?>

<div id="mega_delete" class="modal" style="width:850px;">
		<img class="bClose" src="<?php echo Yii::app()->request->baseUrl; ?>/images/close_button.png" />
		<div id="mega_delete_content"></div>
</div>

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
