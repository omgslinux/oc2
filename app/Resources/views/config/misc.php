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

<?php $this->inlineHelp=':manual:config:misc'; ?>
<?php $this->renderPartial('_title', array('paramGroup'=>__('Misc')));?>

<div class="parameterGroup">
	<div class="param">
		<?php $param = Config::model()->findByPk('htmlEditorUseCompressor'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="htmlEditorUseCompressor" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />No
		<input type="radio" name="htmlEditorUseCompressor" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Yes
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateBool(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php $param = Config::model()->findByPk('budgetAutoFeature'); ?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="budgetAutoFeature" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />No
		<input type="radio" name="budgetAutoFeature" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Yes
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateBool(this); return false;"/>
		<div class="progress"></div>
	</div>
	<div class="param">
		<?php /* Remove this global param when we know all servers work correctly with PDF export
				* I just added 'showExport' as a save guard
				*/
		$param = Config::model()->findByPk('showExport');
		?>
		<span class="paramDescription"><?php echo $param->description;?></span><br />
		<input type="radio" name="showExport" value="0" <?php echo ($param->value == 0) ? 'checked="checked"' : '' ?> />No
		<input type="radio" name="showExport" value="1" <?php echo ($param->value == 1) ? 'checked="checked"' : '' ?> />Yes
		<input type="button" value="save" param="<?php echo $param->parameter;?>" onClick="js:updateBool(this); return false;"/>
		<div class="progress"></div>
	</div>
</div>

