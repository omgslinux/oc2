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
 * along w

/* @var $this IntroPageController */
/* @var $model IntroPage */

$nextPage=$model->getNextPage();
//$model->getTitleForModel($nextPage->id,$content->language)
?>

<style>
.introPageBlock {
	top:<?php echo $model->toppos;?>px;
	left:<?php echo $model->leftpos;?>px;
	width:<?php echo $model->width;?>px;
	color:#<?php echo $model->color;?>;
	background:<?php echo $model->hex2rgba($model->bgcolor, ($model->opacity * 0.1));?>;
}
.introTitle {
	background:<?php echo $model->hex2rgba($model->bgcolor, 0.1);?>;
}
</style>

<div class="introPageBlock">
	<div class="introTitle"><?php echo $content->title; ?></div>
	<div class="sub_title"><?php echo $content->subtitle ?></div>
	<p><?php echo $content->body; ?></p>
	<?php
	if($nextPage){
		echo '<div class="nextIntroPage" onClick="js:nextPage('.$nextPage->id.')">';
		echo '<p style="cursor:pointer;text-decoration:underline">'.$model->getTitleForModel($nextPage->id,$content->language).'</p>';
		echo '</div>';
	}
	?>
</div>
