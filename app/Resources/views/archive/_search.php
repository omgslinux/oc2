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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<script>
function resetSearch(){
	if ($('#Archive_searchText').val() != ''){
		$('#Archive_searchText').val('');
		$('#archive_search').submit();
	}
}
</script>

<div class="form wide">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'archive_search',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>


	<div class="row" style="font-size: 1.2em;">
		<?php echo $form->textField($model,'searchText'); ?>
		<i class="icon-cancel-alt-filled" style="cursor:pointer;" onClick="js:resetSearch();return false;"></i>
		<?php echo CHtml::submitButton(__('Search')); ?>
	</div>


<?php $this->endWidget(); ?>

</div><!-- search-form -->
<div style="clear:both"></div>
