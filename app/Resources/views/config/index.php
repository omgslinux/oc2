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

/* @var $this ConfigController */
/* @var $model Config */
?>

<style>
.parameterGroup {
	float:left;
	padding:3px;
	margin-right:10px;
	margin-bottom:15px;
	/* border: solid 1px grey; */
}
.parameterGroup > .parameterGroupTitle {
	font-size:1.2em;
}
.param { margin-top: 10px; }
.param > .paramDescription { font-size: 1.25em;}
.param > label { color: green; }

.param > .progress {
	display:inline-block;
	width:18px;
}
.param > .error {
	color: red;
}
#preload-01 { background:url(<?php echo Yii::app()->request->baseUrl;?>/images/tick.png); z-index: -999999; }
#preload-02 { background:url(<?php echo Yii::app()->request->baseUrl;?>/images/loading.gif); z-index: -999999; }
</style>
<script>
function submitChange(el, id, callback){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/config/update/'+id,
		type: 'POST',
		data: $('#config-form').serialize(),
		beforeSend: function(){
						$(el).prop("disabled", true);
						$(el).next('.progress').empty();
						$(el).next('.progress').append('<img style="vertical-align:middle;" class="loading_gif" src="<?php echo Yii::app()->request->baseUrl;?>/images/loading.gif" />');
					},
		complete: function(){ $(el).prop("disabled", false); },
		success: function(data){
			$(el).next('.progress').empty();
			$(el).parent().find('.error').remove();
			if(data == 1){
						$(el).next('.progress').append('<i class="icon-ok-circled" style="font-size:18px;"></i>');

						$(el).prop("disabled", false);
						//$(".icon-ok-circled").fadeOut(1600, function() {
						//	$(el).next('.progress').empty();
						//});
						if(typeof callback !== 'undefined'){
							callback();
						}
			}else{
				$(el).next('.progress').after('<div class="error">'+JSON.parse(data)['value']+'</div>');
			}
		},
		error: function() { alert("error on config/udapte"); },
	});
	return 1;
}
function updateParam(el, callback){
	param = $(el).attr('param');
	value = $('#value_'+param).val();
	$('#Config_parameter').val(param);
	$('#Config_value').val(value);
	submitChange(el, param, callback);
}
function updateBool(el){
	param = $(el).attr('param');
	value = $('input[name='+param+']:checked').val();
	$('#Config_parameter').val(param);
	$('#Config_value').val(value);
	submitChange(el, param);
}
function updateMultiple(el){
	param = $(el).attr('param');
	value = $('#value_'+param).find('option:selected').val();
	$('#Config_parameter').val(param);
	$('#Config_value').val(value);
	submitChange(el, param);
}
</script>

<?php
$this->menu=array(
	array('label'=>__('Observatory'), 'url'=>array('observatory')),
	array('label'=>__('Email'), 'url'=>array('email')),
	array('label'=>__('Locale'), 'url'=>array('locale')),
	array('label'=>__('Social networks'), 'url'=>array('social')),
	array('label'=>__('Image'), 'url'=>array('image')),
	array('label'=>__('Backups'), 'url'=>array('backups')),
	array('label'=>__('Misc'), 'url'=>array('misc')),
);
$this->inlineHelp=':manual:config:index';
?>

<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'config-form',
		'enableAjaxValidation'=>false,
	));
	echo $form->hiddenField($model, 'parameter');
	echo $form->hiddenField($model, 'value');
	$this->endWidget();
?>

<?php
	if(!isset($page))
		$page='summary';

	$this->renderPartial($page, array('model'=>$model));
?>
