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
/* @var $model BudgetDescLocal */


$this->menu=array(
	array('label'=>__('Common descriptions'), 'url'=>array('budgetDescription/browseCommon')),
	array('label'=>__('State descriptions'), 'url'=>array('budgetDescription/browseState')),
	array('label'=>__('Budgets without description'), 'url'=>array('budget/noDescriptions')),
);
$this->inlineHelp=':manual:budgetdescription:admin';
$this->viewLog='BudgetDescription';

?>

<h1 style="margin-bottom:15px"><?php echo __('Manage').' '.__('budget descriptions');?></h1>


<div id="searchOptions" class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
$this->widget('PGridView', array(
	'id'=>'budget-description-grid',
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'dataProvider'=>$model->search(),
    'onClick'=>array(
        'type'=>'url',
        'call'=>Yii::app()->request->baseUrl.'/budgetDescription/update',
    ),
	'filter'=>$model,
	'columns'=>array(
		'language',
		'csv_id',
		'code',
		'concept',
		array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
	),
));
?>
