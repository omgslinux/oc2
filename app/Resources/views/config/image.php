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

?>
<?php $this->inlineHelp=':manual:config:image'; ?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<script>
function uploadFile(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/create?model=logo',
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
function deleteLogo(id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/delete/'+id,
		type: 'POST',
		success: function(){
			window.location = '<?php echo Yii::app()->request->baseUrl; ?>/file/logo/';
		},
		error: function() {
			alert("Error on delete");
		}
	});
}
</script>

<style>
	.outer {width:100%; padding: 0px;}
	.left {width:53%; padding: 0px; float: left;}
	.right {width:43%; padding: 0px; float: right;}
	.clear{clear:both;}
</style>

<h1><?php echo __('Observatory color and logo');?></h1>
<?php $logo = File::model()->findByAttributes(array('model'=>'logo'));?>

<div class="outer">
<div class="left">
<p style="margin-top:20px">
1. <?php echo '<a href="http://ocax.net/ocm-logo">'.__('Create a logo').'</a> ';?>
<?php echo __('and save it on your PC').'.';?>
<br />
2. <a href="#" onclick="js:uploadFile()"><?php echo __('Upload the file');?></a>
<br />
3. <?php echo __('Refresh the page pressing F5');?>
</p>

<?php
/*
if($logo){
	echo '<p style="margin-top:50px">';
	echo __('You can repeat the steps above or').' ';
	echo '<a href="#" onclick="js:deleteLogo('.$logo->id.')">'.__('delete the logo').'</a>';
	echo '</p>';
}
*/
?>
</div>

<div class="right">
<?php
if($logo){
	echo '<p><a href="'.$logo->getWebPath().'">'.$logo->getWebPath(TRUE).'</a><br />';
	echo '<img src="'.$logo->getWebPath().'" />';
	echo '</p>';
}
?>
</div>
</div>
<div class="clear"></div>

<div class="parameterGroup">
	<div class="param">
		<?php $param = Config::model()->findByPk('siteColor'); ?>
		<span class="paramDescription"><?php echo __('Your site color is').' ';?>
		<span style="background-color:#<?php echo $param->value;?> ;display:inline-block;width:50px;margin:0 10px 0 10px;">&nbsp;</span>
		#</span>
		<input id="value_<?php echo $param->parameter;?>" type="text" style="width:80px" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
</div>

<?php echo $this->renderPartial('//file/modal'); ?>
