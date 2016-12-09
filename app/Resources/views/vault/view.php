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

/* @var $this VaultController */
/* @var $model Vault */

$this->menu=array(
	array('label'=>__('Show schedule'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:showSchedule(); return false;')),
	array('label'=>__('Manage backups'), 'url'=>array('backup/admin')),
	array(	'label'=>'Delete Vault',
			'url'=>'#',
			'linkOptions'=>array('submit'=>array(	'delete',
													'id'=>$model->id
												),
												'confirm'=>__('You are going to delete this vault. Are you sure?')
		)),
);
$this->inlineHelp=':manual:vault:view';
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function copyNotHere(){
	alert("<?php echo __('The copy was saved on '.$model->host);?>");
}
function showSchedule(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/vault/viewSchedule',
		type: 'GET',
		beforeSend: function(){ /* */ },
		success: function(html){
			if(html != 0){
				$("#schedule_body").html(html);
				$('#schedule_modal').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
					, speed: 10
                });
			}
		},
		error: function() {
			alert("Error on show schedule");
		}
	});
}
function updateCapacity(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/vault/updateCapacity/<?php echo $model->id;?>',
		type: 'GET',
		beforeSend: function(){ /* */ },
		success: function(html){
			if(html != 0){
				$("#capacity_popup_body").html(html);
				$('#capacity_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
					, speed: 10
                });
			}
		},
		error: function() {
			alert("Error on update capacity");
		}
	});
}
</script>

<?php
if($model->type == LOCAL){
	echo '<h1>'.__('Local vault').'</h1>';
	echo '<div class="sub_title" style="margin-bottom:5px;">'.$model->host.' &rarr; '.Yii::app()->getBaseUrl(true).'</div>';
	echo '<p>'.__('They save their copies on your server').'</p>';
}else{
	echo '<h1>'.__('Remote vault').'</h1>';
	echo '<div class="sub_title" style="margin-bottom:5px;">'.Yii::app()->getBaseUrl(true).' &rarr; '.$model->host.'</div>';
	echo '<p>'.__('You save your copies on their server').'</p>';
}
?>

<div>
<div style="float: left; width:49%;">
<?php
if($model->type == LOCAL && $model->state == CREATED){
	$text = __('Tell the Admin at %s that the vault has been created and the key is:');
	$text = str_replace("%s", $model->host, $text);
	echo "<h2>You need to:</h2><p>$text ".$model->key."</p>";
}
if($model->type == REMOTE && $model->state == CREATED){
	$text = __('Ask the Admin at %s to create a vault for you. Your URL is').' '.Yii::app()->getBaseUrl(true);
	$text = str_replace("%s", $model->host, $text);
	echo "<h2>You need to:</h2><p>$text</p>";
	echo '<p>'.str_replace("%s", $model->host, __('%s will send you a key')).'</p>';
	$this->renderPartial('//vault/_configKey', array('model'=>$model));
}

if($model->type == LOCAL && $model->state == VERIFIED){
	$text = __('You are waiting for the admin at %s to choose one or more of the following days').'.';
	$text = str_replace("%s", $model->host, $text);
	echo "<h2>".__('Waiting...')."</h2>";
	echo "<p>".$text."</p>";
	echo "<p>".$model->getHumanSchedule()."</p>";
}
if($model->type == REMOTE && $model->state == VERIFIED){
	echo "<h2>".__('Choose day(s) to backup')."</h2>";
	$this->renderPartial('//vault/_configSchedule', array('model'=>$model));
}
if($model->state >= READY){
	echo '<p>'.__('Total copies made').': '.$model->count.'<br />';
	echo __('Capacity').': ';
	if($model->type == LOCAL)
		echo '<span class="link" onClick="js:updateCapacity();">'.$model->capacity.' '.__('copies').'</span>';
	else
		echo $model->capacity.' '.__('copies');
	echo '</p>';
}

?>

</div>
<div style="float: right; width:44%;">
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'host',
		array(
	        'label'=>__('State'),
			'type' => 'raw',
			'value'=> $model->getHumanStates($model->state),
		),
		array(
	        'label'=>__('Schedule'),
			'type' => 'raw',
			'value'=> ($model->state >= READY)? $model->getHumanSchedule() : __('Pending'),
		),
	),
)); ?>
</div>
</div>
<div class="clear"></div>

<?php if($model->state >= READY){
	echo '<h1>'.__('Backups').'</h1>';

	if($model->type == LOCAL){
		$onClick = array(
					'type'=>'url',
					'call'=>Yii::app()->request->baseUrl.'/backup/downloadBackup',
					);
	}else{
		$onClick = array(
					'type'=>'javascript',
					'call'=>'copyNotHere',
					);
	}
	$this->widget('PGridView', array(
		'id'=>'backup-grid',
		'dataProvider'=>$backups,
		'onClick'=>$onClick,
		'columns'=>array(
			'filename',
			'initiated',
			'completed',
			array(
				'header'=>__('Filesize'),
				'type' => 'raw',
				'value'=>'$data->fileSizeForHumans()',
			),
			array(
				'header'=>__('State'),
				'type' => 'raw',
				'value'=>'$data->getHumanState()',
			),
			array('class'=>'PHiddenColumn','value'=>'$data->id'),
		),	
	));
}
?>


<div id="schedule_modal" class="modal" style="width:800px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="schedule_body"></div>
</div>

<div id="capacity_popup" class="modal" style="width:500px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="capacity_popup_body"></div>
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

<?php if(Yii::app()->user->hasFlash('notice')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-notice').slideUp('fast');
    	}, 5000);
		});
	</script>
    <div class="flash-notice">
		<?php echo Yii::app()->user->getFlash('notice');?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>
