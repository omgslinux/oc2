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

/* @var $this FileController */
/* @var $model File */

$this->menu=array(
	array('label'=>__('Generate the zip file'), 'url'=>array('file/createZipFile')),
	array('label'=>__('Add file to the queue'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:uploadFile();')),
	array('label'=>__('Add csv to the queue'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:showYears();')),
);
if($csv_file=File::model()->findByAttributes(array('model'=>'DatabaseDownload'))){
	$download = array( array('label'=>__('Download zip file'), 'url'=>$csv_file->getWebPath()));
	array_splice( $this->menu, 3, 0, $download );
}
$this->inlineHelp=':manual:file:databasedownload';
$this->viewLog="zipfile,budget";

?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function uploadFile(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/create?model=DatabaseDownload/docs',
		type: 'GET',
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
function deleteFile(file_id){
	answer=confirm("Are you sure?");
	if(!answer)
		return 1;
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/delete/'+file_id,
		type: 'POST',
		success: function(){
				$("#attachment_"+file_id).remove();
		},
		error: function() {
			alert("Error on get file/delete");
		}
	});
}
function showYears(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/csv/showYears',
		type: 'GET',
		beforeSend: function(){ $('#generating').hide(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#years").html(data);
				$("#years").show();
				$('#csvs_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show years");
		}
	});
}
function regenCSV(id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/csv/regenerateCSV/'+id,
		type: 'GET',
		beforeSend: function(){
					$('#years').hide();
					$('#generating').show();
					$('#csvs_popup').bPopup({
						modalClose: false
						, follow: ([false,false])
						, speed: 10
						, positionStyle: 'absolute'
						, modelColor: '#ae34d5'
					});
					$('#loading').show();
				},
		success: function(data){
			$('#csvs_popup').bPopup().close();
			$.fn.yiiGridView.update('file-grid');
		},
		error: function() {
			alert("Error on regenerate csv");
		}
	});
}
</script>

<h1 style="margin-bottom: 15px;">
	<?php echo __('Prepare file').' '.File::model()->normalize(Config::model()->findByPk('siglas')->value);?>.zip
</h1>

<?php
$dataProvider = new CActiveDataProvider('File', array(
    'criteria'=>array(	'condition'=>'model = "DatabaseDownload/data" OR model = "DatabaseDownload/docs"',
						'order'=>'path ASC',
				),
));
echo '<div style="font-size:1.3em">'.__('Files queued and ready to include in zip').'</div>';
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'file-grid',
	'dataProvider'=>$dataProvider,
	'template' => '{items}{pager}',
	'ajaxUpdate'=>true,
	'columns'=>array(
		//'webPath',
		array(
			'name'=>__('Files'),
			'type'=>'raw',
			'value'=> '$data->model."/".$data->name',
		),
		array(
			'class'=>'CButtonColumn',
			'htmlOptions' => array('style' => 'width:70px; text-align:right'),
			'template'=>'{regenerate} {download} {delete}',
			'buttons'=>array(
				'download' => array(
					'label'=> '<i class="icon-download-alt green"></i>',
					'url'=> '"javascript:location.href=\"".$data->webPath."\";"',
					//'imageUrl' => Yii::app()->request->baseUrl.'/images/down.png',
				),
				'regenerate' => array(
					'label'=> '<i class="icon-ccw green"></i>',
					'visible'=> '$data->checkExtension("csv")',
					'url'=> '"javascript:regenCSV(\"".$data->getYearFromCSVFilename()."\");"',
				),
				'delete' => array(
					'label'=> '<i class="icon-cancel-circled red"></i>',
					'imageUrl' => Null,
				),
			),
		),
	),
));
?>

<div id="csvs_popup" class="modal" style="width:500px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="csvs_popup_content">
		

	<div id="years" style="display:none;">
	
	</div>

	<div id="generating" style="display:none;">
		<div class="modalTitle"><?php echo __('Generating CSV file').' ';?></div>
		<div style="text-align:center; padding:10px;">
		<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/big_loading.gif" />
		</div>
	</div>
	
	</div>
</div>


<?php echo $this->renderPartial('//file/modal'); ?>

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

<?php if(Yii::app()->user->hasFlash('error')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-error').slideUp('fast');
    	}, 3000);
		});
	</script>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>
