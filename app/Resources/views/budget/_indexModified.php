<?php

/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2015 OCAX Contributors. See AUTHORS.

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

if ($featured){
?>
	<script>
	function filterbyFeatured(){
		$.fn.yiiGridView.update('modified-budget-grid', {
				data: $('#selectFeatured').serialize()
			});
	}
	</script>
<?php
}

echo '<div class="horizontalRule"></div>';
echo '<div style="font-size: 1.5em;">';
echo '<span style="float:left; margin: -5px 10px 0 0">'.__('Modifications').'</span>';

if ($featured){
	$list=CHtml::listData($featured, 'csv_id', function($featured) {
			return $featured->getTitle();
		}
	);
	echo '<div style="float:left">';
	echo CHtml::dropDownList('featuredFilter', $model->featuredFilter, array(''=>__('Not filtered')) + $list,
							array(	'id'=>'selectFeatured',
									'onchange'=>'js:filterbyFeatured();return false;'
							));
	echo '</div>';
}
//echo '<a href="" target="_blank" style="float:right;"><i class="icon-download-alt"></i></a>';
echo '</div>';
echo '<div class="clear"></div>';

echo '<div style="margin-top:5px;">';

$this->widget('PGridView', array(
	'id'=>'modified-budget-grid',
	'template' => '<span style="float:left; clear:both;">{summary}</span> {pager} <div style="clear:both;">{items}</div>',
	'dataProvider'=>$dataProvider,
	//'filter'=>$model,
    'onClick'=>array(
        'type'=>'javascript',
        'call'=>'showBudget',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
			array(
				'header'=>__('Category'),
				'value'=>'$data->getCategory()',
			),	
			'code',
			array(
				'header'=>__('Budget'),
				'name'=>'concept',
				'value'=>'$data->getConcept()',
			),
			array(
				'name'=>__('initial_provision'),
				'type'=>'raw',
				'value'=>function($data){
					return format_number($data->initial_provision);
				},
				'headerHtmlOptions'=>array('style'=>'text-align: right; white-space: nowrap'),
				'htmlOptions'=>array('style'=>'text-align: right; white-space: nowrap'),
			),	
			array(
				'name'=>__('actual_provision'),
				'type'=>'raw',
				'value'=>function($data){
					return format_number($data->actual_provision);
				},
				'headerHtmlOptions'=>array('style'=>'text-align: right; white-space: nowrap'),
				'htmlOptions'=>array('style'=>'text-align: right; white-space: nowrap'),
			),			
			array(
				'type'=>'raw',
				'value'=>function($data){
					$diff = $data->actual_provision - $data->initial_provision;
					if ($diff > 0){
						return '<span class="green">'.format_number($diff).'</span>';
					}
					return '<span class="red">'.format_number($diff).'</span>';
				},
				'headerHtmlOptions'=>array('style'=>'text-align: right'),
				'htmlOptions'=>array('style'=>'text-align: right; white-space: nowrap'),
			),
			array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
)));
echo '</div>';
?>
