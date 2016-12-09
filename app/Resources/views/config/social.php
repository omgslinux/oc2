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

<?php $this->inlineHelp=':manual:config:social'; ?>
<?php $this->renderPartial('_title', array('paramGroup'=>__('Social networks')));?>

<div class="parameterGroup">
	<div class="param">
		<?php $param = Config::model()->findByPk('socialFacebookURL'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('socialTwitterURL'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('socialActivateNonFree'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="socialActivateNonFree" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />No
		<input type="radio" name="socialActivateNonFree" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Yes
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateBool(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('socialTwitterUsername'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input id="value_<?php echo $param->parameter;?>" type="text" value = "<?php echo $param->value;?>"/>
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateParam(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('socialActivateMeneame'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="socialActivateMeneame" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />No
		<input type="radio" name="socialActivateMeneame" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Yes
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateBool(this); return false;"/>
		<div class="progress"></div>
	</div>
</div>
