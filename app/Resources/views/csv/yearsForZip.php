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

/* @var $this FileController */
/* @var $model File */

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
?>

<div class="modalTitle"><?php echo __('Add CSV file to queue').' ';?></div>

<div style="margin: 10px -10px 0 -10px">
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'years-grid',
	'dataProvider'=>$dataProvider,
	'template' => '{items}{pager}',
	'columns'=>array(
		'year',
		array(
			'header'=>'Published',
			'name'=>'code',
			'value'=>'$data[\'code\']',
		),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{regen}',
			'buttons' => array(
				'regen' => array(
					'label'=> '<i class="icon-plus-circled-1 green"></i>',
					'url'=> '"javascript:regenCSV(\"".$data->year."\");"',
					//'imageUrl' => Yii::app()->request->baseUrl.'/images/regen.png',
				)
			),
		),
	),
));
?>
</div>

