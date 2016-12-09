<?php

/**
 * OCAX -- Citizen driven Municipal Observatory software
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

/* @var $this FileController */
/* @var $model File */

$this->menu=array(
	array('label'=>__('Upload file'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:uploadFile();')),
);
$this->inlineHelp=':archive';
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<script>
function uploadFile(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/create?model=archive',
		type: 'POST',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#files_content").html(data);
				$('#files').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on get file/create");
		}
	});
}
</script>

<h1><?php echo str_replace('%s',Config::model()->findByPk('siglas')->value,__('Document archive'));?></h1>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'file-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'header'=>__('Path'),
			'name'=>'path',
			'type' => 'raw',
			'value' => 'CHtml::link($data->getWebPath(),$data->getWebPath())'
		),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{delete}',
		),
	),
));
?>

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

<div id="files" class="modal" style="width:500px;">
<img class="bClose" src="<?php echo Yii::app()->request->baseUrl; ?>/images/close_button.png" />
<div id="files_content" style="margin:-10px"></div>
</div>
