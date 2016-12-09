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

/* @var $this SitePageController */
/* @var $model SitePage */

$this->menu=array(
	array('label'=>__('Create page'), 'url'=>array('create')),
	array('label'=>__('Upload file'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:uploadFile();')),
	array('label'=>__('Show uploaded files'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:showUploadedFiles();')),
);
$this->inlineHelp=':manual:sitepage:admin';
$this->viewLog='sitePage';
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<script>
function showUploadedFiles(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/showCMSfiles',
		type: 'GET',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#files_popup_content").html(data);
				$('#files_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on showCMSfiles");
		}
	});
}
function uploadFile(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/create?model=<?php echo get_class($model);?>',
		type: 'GET',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#files_popup_content").html(data);
				$('#files_popup').bPopup({
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

<h1><?php echo __('Manage pages')?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view pgrid-cursor-pointer'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'cms-page-grid',
	'selectableRows'=>1,
	'selectionChanged'=>'function(id){ location.href = "'.$this->createUrl('update').'/"+$.fn.yiiGridView.getSelection(id);}',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'header'=>__('Page name'),
			'value'=>'SitePage::model()->getTitleForModel($data->id)',
		),
		'block',
		'weight',
		'published',
	),
)); ?>

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

<?php echo $this->renderPartial('//file/modal'); ?>

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
