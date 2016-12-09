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

$this->setPageTitle($content->pageTitle);
?>

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/budget.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/sitepage.css" />

<?php if(isset($preview)){ ?>
<script>
$(function() {
	$('.language_link').hide();
});
function savePreview(){
	form = $('#sitePage-form');
	form.attr(	'action',
				"<?php echo Yii::app()->request->baseUrl; ?>/sitePage/savePreview/<?php echo $model->id;?>?lang=<?php echo $content->language;?>"
			);
	form.submit();
}
function editPreview(){
	form = $('#sitePage-form');
	form.attr(	'action',
				"<?php echo Yii::app()->request->baseUrl; ?>/sitePage/editPreview/<?php echo $model->id;?>?lang=<?php echo $content->language;?>"
			);
	form.submit();}
</script>

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'sitePage-form',
	'enableAjaxValidation'=>false,
));
echo $form->hiddenField($content,'language');
echo $form->hiddenField($content,'pageURL');
echo $form->hiddenField($content,'pageTitle');
echo $form->hiddenField($content,'previewBody');
$this->endWidget();

echo '<div id="sitePageOptions">';
	echo '<div style="width:30%; float: left; text-align: center;">';
	echo '<a href="#" onclick="js:savePreview();">'.__('Save changes').'</a>';
	echo '</div>';
	echo '<div style="width:30%; float: left; text-align: center;">';
	echo '<a href="#" onclick="js:editPreview();">'.__('Edit page').'</a>';
	echo '</div>';
	echo '<div style="width:30%; float: left; text-align: center;">';
	echo CHtml::link(__('Manage pages'),array('sitePage/admin'));
	echo '</div>';
echo '<div style="clear:both;"></div>';
echo '</div>';

} ?>

<?php
if(!$model->published)
	echo '<i class="icon-attention green"></i> '.__('Not published');
if(!Yii::app()->user->isGuest && Yii::app()->user->isEditor() && !isset($preview)){
	echo '<div style="float:right; margin-right: -10px; ">';
	echo	'<i class="icon-edit-1" style=" font-size:18px; cursor:pointer;" '.
			'onclick="js:window.location.href=\''.$this->createUrl('/sitePage/update/'.$model->id).'\'"></i>';
	echo	'<i class="icon-th-list" style=" font-size:18px; cursor:pointer;" '.
			'onclick="js:window.location.href=\''.$this->createUrl('/sitePage/admin').'\'"></i>';
	echo '</div>';
}			

if($model->showTitle)
	echo '<div class="sitePage_titulo">'.CHtml::encode($content->pageTitle).'</div>';
?>

<div class="sitePage_content">
	<?php echo isset($preview) ? $content->previewBody : $content->body; ?>
</div>


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
