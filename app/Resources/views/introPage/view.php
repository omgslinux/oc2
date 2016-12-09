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
 * along w

/* @var $this IntroPageController */
/* @var $model IntroPage */


// get wallpaper photo
if($wallpapers = File::model()->findAllByAttributes(array('model'=>'wallpaper'))){
	$images=array();
	foreach($wallpapers as $wallpaper){
		$images[]=$wallpaper->getWebPath();
	}
}else{
	$files=array();
	$images=array();
	$dir = Yii::app()->theme->basePath.'/wallpaper/';
	$files = glob($dir.'*.jpg', (real)GLOB_BRACE);

	foreach($files as $image)
		$images[] = Yii::app()->theme->baseUrl.'/wallpaper/'.basename($image);
}
shuffle($images);

?>

<style>
#pageOptions{
	font-size:1.5em;
	margin: -30px -25px 30px -25px;
	padding: 10px 0 10px 0;
	background-color:white;
}
#pageOptions a{
	padding: 12px 20px 12px 20px;
}
#pageOptions a:hover{
	background-color:#f5f1ed;
}
#wallpaper {
	position:relative;
	margin-left:-25px;
	margin-top:-30px;
	margin-bottom:-10px;
	height:728px;
	width:980px;
	background: url("<?php echo $images[0];?>") 0 0 no-repeat;
}
</style>

<script>
$(function() {
	$('.language_link').hide();
});
function nextPage(id){
	alert('<?php echo __('You are in edit mode');?>');
}
</script>
<?php
echo '<div id="pageOptions">';
	echo '<div style="width:50%; float: left; text-align: center;">';
	echo CHtml::link(__('Edit page'),array('introPage/update',
										'id'=>$model->id,
										'lang'=>$content->language,
									));
	echo '</div>';
	echo '<div style="width:50%; float: left; text-align: center;">';
	echo CHtml::link(__('Manage pages'),array('introPage/admin'));
	echo '</div>';
echo '<div style="clear:both;"></div>';
echo '</div>';
?>

<div id="wallpaper">
	<?php echo $this->renderPartial('show', array('model'=>$model,'content'=>$content));?>
</div>



