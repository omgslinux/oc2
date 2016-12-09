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

<?php $this->inlineHelp=':manual:config:locale'; ?>

<?php $this->renderPartial('_title', array('paramGroup'=>__('Locale')));?>

<script>
function changeCoordinate(el){
	updateParam(el);
	document.getElementById('map').contentDocument.location.reload(true);
}
</script>

<div class="parameterGroup">
	<div class="param">
		<?php $param = Config::model()->findByPk('currencySymbol'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('languages'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
</div>

<div class="parameterGroup">
	<div style="margin-top:15px;font-size:16px;">
	<?php echo __('Coordinates for the map at').' <a href="http://network.ocax.net/map/" target="_blank">http://network.ocax.net/map/</a>';?>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('administrationLongitude'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:changeCoordinate(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('administrationLatitude'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:changeCoordinate(this); return false;"/>
		<div class="progress"></div>
	</div>
	<p>
	<a href="http://gll.petschge.de/" target="_blank"><?php echo __('Find coordinates here');?></a>
	</p>
</div>

<iframe id="map" src="<?php echo Yii::app()->getBaseUrl(true).'/site/map';?>" width="740px">
</iframe>
