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

/* @var $this IntroPageController */
/* @var $model IntroPage */


$this->menu=array(
	array('label'=>__('Create page'), 'url'=>array('create')),
);
$this->inlineHelp=':manual:intropage:admin';
$this->viewLog='introPage';
?>

<h1><?php echo __('Manage Introduction pages');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view pgrid-cursor-pointer'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'intro-page-grid',
	'selectableRows'=>1,
	'selectionChanged'=>'function(id){ location.href = "'.$this->createUrl('update').'/"+$.fn.yiiGridView.getSelection(id);}',
	'dataProvider'=>$model->search(),
	'columns'=>array(
		'weight',
		array(
			'header'=>__('Title'),
			'value'=>'IntroPage::model()->getTitleForModel($data->id)',
		),
		'published',
	),
)); ?>
