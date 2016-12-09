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

/* @var $this FileController */
/* @var $model File */
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
?>

<div>
	<div class="modalTitle"><?php echo __('Restore all budgets from a backup');?></div>


<?php
	echo '<div style="font-size:1.2em;margin-bottom:15px;"><i class="icon-attention green"></i>'.
		__('These copies are made right before a CSV file is imported').' '.
		'<a href="'.getInlineHelpURL(':manual:budget:restore').'" target="_new">'.__('READ ME').'</a>'.
		'</div>';?>

<div style="margin:-10px;margin-bottom:10px;">
<?php
$dataProvider = new CActiveDataProvider('File', array(
    'criteria'=>array(	'condition'=>'model = "Budget"',
						'order'=>'id DESC',
				),
));
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'file-grid',
	'dataProvider'=>$dataProvider,
	'template' => '{items}{pager}',
	'columns'=>array(
		'name',
		array(
			'class'=>'CButtonColumn',
			'buttons' => array(
				'restore' => array(
					'label'=> '<i class="icon-recycle green"></i>',
					'url'=> '"javascript:restoreBudgets(\"".$data->id."\");"',
					//'imageUrl' => Yii::app()->request->baseUrl.'/images/down.png',
				)
			),
			'template'=>'{restore} {delete}',
		),
	),
));
?>
</div>
<?php 
if($dataProvider->getTotalItemCount() > 10){
	echo '<p><i class="icon-attention amber"></i>';
	echo __('Note: Delete some backups to save disk space. You only really need the last good copy.');
	echo '</p>';
}
?>
</div>
