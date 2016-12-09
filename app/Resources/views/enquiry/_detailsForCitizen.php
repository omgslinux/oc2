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

/* @var $this EnquiryController */
/* @var $model Enquiry */


$attribs = array();

if(!isset($hideLinks)){

	$attribs[] = array(
			'label'=>__('Formulated'),
			'type' => 'raw',
			'value'=>($model->user0->username == Yii::app()->user->id || $model->user0->is_disabled == 1) ?
						format_date($model->created).' '.__('by').' '.$model->user0->fullname :
						format_date($model->created).' '.__('by').' '.CHtml::link(
																CHtml::encode($model->user0->fullname), '#!',
																array('onclick'=>'js:getContactForm('.$model->user.');return false;')
															),
		);
} else {
	$attribs[] = array(
			'label'=>__('Formulated'),
			'type' => 'raw',
			'value'=>(format_date($model->created).' '.__('by').' '.$model->user0->fullname),
		);
}
$attribs[] = array(
		'label'=>__('State'),
		'type' => 'raw',
		'value'=> CHtml::encode($model->getHumanStates($model->state,$model->addressed_to)),
	);
		
if($model->state >= ENQUIRY_AWAITING_REPLY){

		$submitted_info=format_date($model->submitted).', '.__('Registry number').': '.$model->registry_number;
		if($model->documentation && !isset($hideLinks))
			$submitted_info = '<a href="'.$model->documentation0->getWebPath().'" target="_new">'.$submitted_info.'</a>';
		$attribs[] = array(	'label'=>__('Submitted'),
							'type'=>'raw',
							'value'=>$submitted_info,
					);
}
$attribs[] = array(
		'label'=>__('Type'),
		'value'=>($model->related_to) ? $model->getHumanTypes($model->type).' ('.__('reformulated').')' : $model->getHumanTypes($model->type),
	);
$this->widget('zii.widgets.CDetailView', array(
	'id' => 'e_details',
	'cssFile' => Yii::app()->request->baseUrl.'/css/pdetailview.css',
	'data'=>$model,
	'attributes'=>$attribs,
));

if(!isset($hideBudgetDetails)){
	if ($model->budget){
		$this->renderPartial('_budgetDetails', array(	'model'=>$model->budget0,
														'showLinks'=>1,
														'showEnquiriesMadeLink'=>1,
														'enquiry'=>$model,
													));
	}
}
?>
