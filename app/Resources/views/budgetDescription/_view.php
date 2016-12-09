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

/* @var $this BudgetDescriptionController */
/* @var $model BudgetDescription */

?>

<div class="modalTitle"><?php echo __('Budget description');?>
<?php
/*
	if(!$fieldsForDisplay['label'] && !$fieldsForDisplay['concept'] && !$fieldsForDisplay['description']){
		if($model->label || $model->concept)
			echo ': <i class="icon-attention green"></i>'.__('Using data imported with CSV files');
	}
*/
?>
</div>

<?php
	if(!$fieldsForDisplay['label'] && $model->label)
		$fieldsForDisplay['label'] = $model->label;
	if(!$fieldsForDisplay['concept'] && $model->concept)
		$fieldsForDisplay['concept'] = $model->concept;
?>

<h1 style="margin-bottom:15px">
<?php
	if($fieldsForDisplay['label'])
		echo $fieldsForDisplay['label'].': ';
	echo $fieldsForDisplay['concept'];
?>
</h1>

<div class="budgetExplication"><?php echo $fieldsForDisplay['description'];?></div>
