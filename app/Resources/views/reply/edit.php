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

/* @var $this ReplyController */
/* @var $model Reply */
/* @var $form CActiveForm */

$this->menu=array(
	array('label'=>__('View enquiry'), 'url'=>array('/enquiry/teamView', 'id'=>$enquiry->id)),
	array('label'=>__('Sent emails'), 'url'=>array('/email/index/', 'id'=>$enquiry->id, 'menu'=>'team')),
	array('label'=>__('List enquiries'), 'url'=>array('/enquiry/assigned')),
);
$this->inlineHelp=':manual:reply:create';


if($model->isNewRecord){
	$text =	'<i class="icon-attention green"></i><br />'.__('The body of text will be displayed together with the enquiry').'.<br />'.
			__('After publishing this reply you will be able to attach the documentation you received from the administration');
	$this->extraText = $text;
}

echo '<h1 style="margin-bottom:15px;">';
if($model->isNewRecord)
	echo __('Add reply');
else
	echo __('Correct reply');
echo '</h1>';

$this->renderPartial('_form', array('model'=>$model, 'enquiry'=>$enquiry));
echo '<p></p>';
$this->renderPartial('//enquiry/_teamView', array('model'=>$enquiry));
?>
