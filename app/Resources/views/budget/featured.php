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

/* @var $this BudgetController */
/* @var $model Budget */

$criteria = new CDbCriteria;
$criteria->condition = 'parent IS NULL AND year =:year';
$criteria->params[':year'] = $model->year;
$this_year=$model->find($criteria);

$this->menu=array(
	array('label'=>__('Edit year').' '.$model->year, 'url'=>array('/budget/updateYear/'.$this_year->id)),
	array('label'=>__('Manage years'), 'url'=>array('admin')),
);
$this->inlineHelp=':manual:budget:featured';
?>

<script>
function featureBudget(budget_id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/feature',
		type: 'GET',
		data: {'id': budget_id},
		success: function(data){
			if(data != 0){
				$('#budget-grid :input[type=text]').val('');
  				$.fn.yiiGridView.update('budget-grid', {
					data: $(this).serialize()
				});
			}
		},
		error: function() {
			alert("Error on Feature budget");
		}
	});
}
function changeWeight(budget_id, increment){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/changeWeight',
		type: 'GET',
		data: {'id': budget_id, 'increment': increment},
		success: function(data){
			if(data != 0){
  				$.fn.yiiGridView.update('budget-grid', {
					data: $(this).serialize()
				});
			}
		},
		error: function() {
			alert("Error on budget/changeWeight");
		}
	});
}
</script>


<h1><?php echo __('Featured budgets').' '.$model->year;?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'budget-grid',
	'dataProvider'=>$model->featuredSearch(),
	'filter'=>$model,
	'columns'=>array(
		//'featured',
		//'weight',
		array(
			'header'=>__('Concept'),
			'name'=>'concept',
			'value'=>'Budget::model()->findByPk($data[\'id\'])->getConcept()',
		),
		//'code',
		'csv_id',
		array(
			'class'=>'CButtonColumn',
			'htmlOptions' => array('style' => 'width:70px; text-align:right'),
			'buttons' => array(
				'up' => array(
					'label'=> '<i class="icon-up green"></i>',
					'visible'=>'$data->featured',
					'url'=> '"javascript:changeWeight(\"".$data->id."\",+1);"',
				),
				'down' => array(
					'label'=> '<i class="icon-down green"></i>',
					'visible'=>'$data->featured',
					'url'=> '"javascript:changeWeight(\"".$data->id."\",-1);"',
				),
				'feature' => array(
					'label'=> '<i class="icon-star green"></i>',
					'visible'=>'$data->featured',
					'url'=> '"javascript:featureBudget(\"".$data->id."\");"',
				),
				'unfeature' => array(
					'label'=> '<i class="icon-star-empty green"></i>',
					'visible'=>'!$data->featured',
					'url'=> '"javascript:featureBudget(\"".$data->id."\");"',
				)
			),
			'template'=>'{up}{down} {feature}{unfeature}',
		),
	),
)); ?>

