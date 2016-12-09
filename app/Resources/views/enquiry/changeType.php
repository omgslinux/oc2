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

/* @var $this EnquiryController */
/* @var $model Enquiry */

$this->menu=array(
	array('label'=>__('View enquiry'), 'url'=>array('/enquiry/teamView', 'id'=>$model->id)),
	array('label'=>__('Edit enquiry'), 'url'=>array('/enquiry/edit', 'id'=>$model->id)),
	array('label'=>__('List enquiries'), 'url'=>array('/enquiry/assigned')),
);
$this->inlineHelp=':manual:enquiry:changetype';
$this->viewLog='Enquiry|'.$model->id;
?>

<h1 style="margin-bottom:15px"><?php echo __('Change type')?></h1>

<?php $this->renderPartial('_changeType',array('model'=>$model,'budgetModel'=>$budgetModel));?>


