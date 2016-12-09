<?php

/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2015 OCAX Contributors. See AUTHORS.

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

/* @var $this ArchiveController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('archive-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$user_id = 0;
$is_admin = 0;
$priviliged = Yii::app()->user->isPrivileged();

if ($priviliged){
	$containerID = '';
	if ($container){
		$containerID  = $container->id;
	}
	$user_id = Yii::app()->user->getUserID();
	$is_admin = Yii::app()->user->isAdmin();
}
?>

<style>
</style>

<?php echo '<link rel="stylesheet" type="text/css" href="'.Yii::app()->request->baseUrl.'/css/archive.css" />'; ?>

<script>
$(function() {
	$(window).scroll(function() {
		if($(this).scrollTop() > 300)
			$('.goToTop').fadeIn(500);
		else
			$('.goToTop').fadeOut(500);
	});
	$(".goToTop").click(function(){
		$("html, body").animate({ scrollTop: 0 }, 0);
		$('.goToTop').hide();
	});
});
function loadSocialWidgets(archive_id, el){
	$.ajax({
		url: "<?php echo Yii::app()->request->baseUrl.'/archive/getSocialWidgets/'; ?>"+archive_id,
		type: 'GET',
		beforeSend: function(){ $('.shareBox').remove(); },
		success: function(data){
			if(data != 0){
				$(el).parent( "span" ).append('<span class="shareBox">'+data+'</span>');
				$('.socialWidgetBox').show();
				showSocialWidgets();
			}
		},
		error: function() {
			alert("Error on get archive/getSocialWidgets");
		}
	});
}
</script>

<?php if($priviliged){ ?>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<script>
function flashError(text){
	if(text != ''){
		$(".flash-error").html(text);
	}
	$(".flash-error").show();
	setTimeout(function() {
		$(".flash-error").slideUp("fast");
	}, 4500);
}
function showLoader(){
	$('#archive-grid').addClass('grid-view-loading');
}
function hideLoader(){
	$('#archive-grid').removeClass('grid-view-loading');
}
function validate(){
	$('.errorMessage').html('');
	errors=0;

	if ($('#Archive_name').val() == ''){
		$('#name_error').html("<?php echo __('Name required');?>");
		errors=1;
	}

	if ($('#Archive_description').val() == ''){
		$('#description_error').html("<?php echo __('Description required');?>");
		errors=1;
	}
	if (!errors){
		$('#archive-form').submit();
	}
}
function uploadFile(){
	$.ajax({
		url: "<?php echo Yii::app()->request->baseUrl.'/archive/getUploadFileForm/'.$containerID; ?>",
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
			alert("Error on get archive/getForm");
		}
	});
}
function createContainer(){
	$.ajax({
		url: "<?php echo Yii::app()->request->baseUrl.'/archive/createContainer/'.$containerID; ?>",
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
			alert("Error on get archive/create");
		}
	});
}
function editArchive(archive_id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/archive/update/'+archive_id,
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
			}else{
				alert("<?php echo __('Sorry, cannot edit the csv zip file');?>");
			}
		},
		error: function() {
			alert("Error on get archive/create");
		}
	});
}
function deleteArchive(archive_id){
	if(confirm("<?php echo __('Delete this archive?');?>") == false)
		return;

	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/archive/delete/'+archive_id,
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		beforeSend: function(){ $('#files_popup').bPopup().close(); },
		success: function(data){
			$.fn.yiiGridView.update("archive-grid",{});
		},
		error: function() {
			alert("Error on get archive/edit");
		}
	});
}
</script>
<?php 
	$this->widget('InlineHelp');
	$this->widget('ViewLog');
	echo $this->renderPartial('//file/modal');
}
?>

<div style="margin:-15px 0 8px -15px;">
<?php
echo '<h1 style="float:left;"><i class="icon-folder-1"></i> '.__('Archive').'</h1>';
if($priviliged){
	echo '<div id="archiveOptions">';
	echo '<i title="'.__("Upload a file").'" class="icon-upload-cloud" onClick="js:uploadFile();return false;"></i>';
	echo '<i title="'.__("Create a folder").'" class="icon-folder-1" onClick="js:createContainer();return false;"></i>';
	echo '<i title="'.__("Help").'" class="icon-help-circled" onCLick="js:showHelp(\''.getInlineHelpURL(":manual:archive").'\');return false;"></i>';
	echo '<i title="'.__("Log").'" class="icon-book" onCLick="js:viewLog(\'Archive\');return false;"></i>';
	echo '<i title="'.__("My page").'" class="icon-home" onCLick="js:window.location.href = \''.$this->createUrl('/user/panel').'\';"></i>';
	echo '</div>';
}
?>

<div style="float:right; white-space:nowrap;"><!-- search-form -->
	<?php $this->renderPartial('_search',array('model'=>$model)); ?>
</div>
<div class="clear"></div>
</div>

<div class="horizontalRule"></div>

<?php
if($container){
	echo '<div id="upLevel">';
	echo '<a href="'.$container->getParentContainerURL().'"><i class="icon-back color"></i>'.__('up to');
	echo '&nbsp;&nbsp;"'.$container->getParentContainerName().'"</a>';
	echo '</div>';
}
?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'archive-grid',
	'dataProvider'=>$dataProvider,
	'template' => '{pager} <div style="margin-top:15px;">{items}</div>',
	'ajaxUpdate'=>true,
	'emptyText'=>__('Nothing found here'),
	'columns'=>array(
		array(
			'type'=>'raw',
			'value'=>function($data){
				if ($data->is_container){
					$icon = '/images/fileicons/folder.svg';
				}else{
					$icon = '/images/fileicons/'.strtolower($data->extension).'.svg';
				}
				if (file_exists(dirname(Yii::app()->request->scriptFile).$icon)){
					return '<img class="icon" src="'.Yii::app()->baseUrl.$icon.'"/>';
				}
				return '<img class="icon" src="'.Yii::app()->baseUrl.'/images/fileicons/unknown.svg"/>';
			}
		),
		array(
			'header'=>__('Name'),
			'urlExpression'=>'$data->getURL()',
			'labelExpression'=>function($data){
				if ($data->extension){
					return $data->name.".".$data->extension;
				}
				return $data->name;
			},
			'class'=>'CLinkColumn'
		),
		array(
			'name'=>__('Description'),
			'value'=> '$data->description',
		),
		array(
			'name'=>__('Date'),
			'type'=>'raw',
			'value'=> 'format_date($data->created)',
			'htmlOptions'=>array('style'=>'white-space: nowrap'),
		),
		array(
			'type'=>'raw',
			'value'=>function($data){
				return $data->getShareColumnItem();
			},
		),
		array(
			'class'=>'CButtonColumn',
			'buttons' => array(
				'edit' => array(
					'label'=> '<i class="icon-edit-1"></i>',
					'url'=> '"javascript:editArchive(\"".$data->id."\");"',
					'visible'=>'$data->canEdit('.$user_id.', '.$is_admin.');',
					//'htmlOptions'=>array('style'=>'padding: 0 -10px 0 -10px;'),
				)
			),
			//'name'=>'a',
			'template'=>'{edit}',
			'visible'=>$priviliged,
		),
	),
));
?>
<div style="clear:both"></div>
<div class="goToTop">&#x25B2;&nbsp;&nbsp;&nbsp;<?php echo __('go to top');?></div>

<?php if($priviliged){
	if(Yii::app()->user->hasFlash('success')){
		echo '<script>';
			echo '$(function() {'.
					'$(".flash-success").slideDown("fast");'.
					'setTimeout(function() {'.
						'$(".flash-success").slideUp("fast");'.
	    			'}, 4500);'.
				'});';
		echo '</script>';
	    echo '<div class="flash-success" style="display:none">';
			echo Yii::app()->user->getFlash('success');
	    echo '</div>';
	}
	if(Yii::app()->user->hasFlash('error')){
		echo '<script>';
			echo '$(function() { flashError();});';
		echo '</script>';
	}
	echo '<div class="flash-error" style="display:none">';
		if(Yii::app()->user->hasFlash('error')){
			echo Yii::app()->user->getFlash('error');
		}
	echo '</div>';
} ?>

<?php echo $this->renderPartial('//includes/socialWidgetsScript', array()); ?>
