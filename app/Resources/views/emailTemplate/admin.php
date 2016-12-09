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

/* @var $this EmailTemplateController */
/* @var $model EmailTemplate */

$this->inlineHelp=':manual:emailtemplate';
$this->viewLog='EmailTemplate';
?>

<h1 style="margin-bottom: 15px;"><?php echo __('Email text templates');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view pgrid-cursor-pointer'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'text-grid',
	'selectableRows'=>1,
	'selectionChanged'=>'function(id){ location.href = "'.$this->createUrl('/emailTemplate/update').'/"+$.fn.yiiGridView.getSelection(id);}',
	'template' => '{items}',
	'dataProvider'=>$model->search(),
	//'ajaxUpdate'=>true,
	'columns'=>array(
			array(
				'header'=>__('State'),
				'type' => 'raw',
    	        'value'=>function($data,$row){
					$value = Enquiry::model()->getHumanStates($data->state);
					if(!$data->updated)
						$value = $value.' <i class="icon-attention amber"></i>';
					return $value;
				},
			),
))); ?>

