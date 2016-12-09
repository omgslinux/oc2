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
?>

<?php
$this->widget('zii.widgets.CDetailView', array(
	'cssFile' => Yii::app()->request->baseUrl.'/css/pdetailview.css',
	'data'=>$model,
	'attributes'=>array(
		array(
	        'label'=>__('State'),
	        'value'=>$model->getHumanStates($model->state,$model->addressed_to),
		),
		array(
	        'label'=>__('Formulated by'),
	        'value'=>$model->user0->fullname.' '.__('on the').' '.$model->created.' ('.$model->user0->email.')',
		),
		array(
	        'label'=>__('Assigned to'),
	        'value'=>($model->team_member) ? $model->teamMember->fullname.' '.__('on the').' '.$model->assigned : "",
		),
		array(
	        'label'=>__('Type'),
	        'value'=>($model->related_to) ? $model->getHumanTypes($model->type).' ('.__('reformulated').')' : $model->getHumanTypes($model->type),
		),
		array(
	        'label'=>__('Addressed to'),
	        'value'=>$model->getHumanAddressedTo($model->addressed_to),
		),
		array(
	        'label'=>__('Subscribed users'),
	        'value'=>count($model->subscriptions),
		),
	),
));

if($model->state >= ENQUIRY_AWAITING_REPLY){
	$document='';
	if($model->documentation)
		$document ='Doc: <a href="'.$model->documentation0->getWebPath().'" target="_new">'.$model->documentation0->name.'</a>';
	elseif($model->addressed_to == ADMINISTRATION)
		$document ='Doc: <span style="color:red">'.__('missing').'</span>';
	$submitted_info=format_date($model->submitted).', '.__('Registry number').': '.$model->registry_number;
	if($document)
		$submitted_info = $submitted_info.', '.$document;

	$this->widget('zii.widgets.CDetailView', array(
	'cssFile' => Yii::app()->request->baseUrl.'/css/pdetailview.css',
	'data'=>$model,
	'attributes'=>array(
		array(
	        'label'=>__('Submitted'),
			'type'=>'raw',
	        'value'=>$submitted_info,
		),
	),
	));
}

if($model->budget){
	$budget=Budget::model()->findByPk($model->budget);
	if ($budget===null){
		throw new CHttpException(404,'The requested Budget does not exist.');
	}
	$this->renderPartial('//enquiry/_budgetDetails', array('model'=>$budget,'showMore'=>1));
}

?>
