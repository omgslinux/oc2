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
$this->inlineHelp=':manual:budget:deletetree';
?>

<script>
function deleteTree(budget_id, budget_csv){
	$('.flash-error').hide();
	$('.flash-success').hide();
	msg = "<?php echo __('Delete \"%s\" and its sub-budgets?');?>";
	if(!confirm(msg.replace("%s", budget_csv)))
		return;

	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/delTree/'+budget_id,
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		dataType: 'json',
		beforeSend: function(){	$('#budget-grid').addClass('grid-view-loading'); },
		complete: function(){ $('#budget-grid').removeClass('grid-view-loading'); },
		success: function(data){
			if(data != 0){
				if(data.totalEnquiries > 0){
					msg = "<?php echo __('Cannot delete the budget tree because it contains %s enquiries');?>";
					$('.flash-error').html(msg.replace("%s", data.totalEnquiries));
					$('.flash-error').show();
				}else{
					msg = "<?php echo __('%s budgets deleted ok');?>";
					$('.flash-success').html(msg.replace("%s", data.totalBudgets));
					$('.flash-success').show();
					$.fn.yiiGridView.update('budget-grid');
				}
			}
		},
		error: function() {
			alert("Error on budget deltree");
		}
	});
}
</script>


<h1><?php echo __('Delete budgets').' '.$model->year;?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'budget-grid',
	'dataProvider'=>$model->deleteTreeSearch(),
	'filter'=>$model,
	'columns'=>array(
		'csv_id',
		'code',
		array(
			'header'=>__('Concept'),
			'name'=>'concept',
			'value'=>'Budget::model()->findByPk($data[\'id\'])->getConcept()',
		),
		array(
			'class'=>'CButtonColumn',
			'buttons' => array(
				'deltree' => array(
					'label'=> '<i class="icon-cancel-circle red" style="font-size:1.2em"></i>',
					'url'=> '"javascript:deleteTree(\"".$data->id."\",\"".$data->csv_id."\");"',
				)
			),
			'template'=>'{deltree}',
		),
	),
)); ?>

<div class="flash-success" style="display:none"></div>
<div class="flash-error" style="display:none"></div>
