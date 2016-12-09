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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/* @var $this BudgetDescriptionController */
/* @var $model BudgetDescription */

$this->menu=array(
	array('label'=>__('Manage descriptions'), 'url'=>array('budgetDescription/admin')),
);
$this->inlineHelp=':manual:budget:nodescriptions';
?>

<h1><?php echo __('Budgets without description');?></h1>

<?php
	$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'loadingCssClass'=>'grid-view-loading',
	'id'=>'budget-grid',
	'dataProvider'=>$model->budgetsWithoutDescription(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'header'=>'internal code',
			'value'=>'$data["csv_id"]',
		),
        'code',
        'year',
		array(
			'class'=>'CButtonColumn',
			'template'=>'{create}',
			'buttons'=>array(
				'create' => array(
					'label'=> '<i class="icon-plus-circled amber"></i>',
					'url'=>'Yii::app()->createUrl("budgetDescription/modify", array("budget"=>$data[\'id\']))',
				),
			),
		),

	),
));
?>



