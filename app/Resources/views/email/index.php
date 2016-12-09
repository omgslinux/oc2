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

/* @var $this EmailController */
/* @var $dataProvider CActiveDataProvider */

if($menu == 'team'){
	$this->menu=array(
		array('label'=>__('View enquiry'), 'url'=>array('/enquiry/teamView', 'id'=>$enquiry->id)),
		array('label'=>__('List enquiries'), 'url'=>array('/enquiry/assigned')),
		//array('label'=>'email ciudadano', 'url'=>'#', 'linkOptions'=>array('onclick'=>'getEmailForm('.$model->user0->id.')')),
	);
}
if($menu == 'manager'){
	$this->menu=array(
		array('label'=>__('View enquiry'), 'url'=>array('/enquiry/adminView', 'id'=>$enquiry->id)),
		array('label'=>__('Manage enquiry'), 'url'=>array('/enquiry/manage', 'id'=>$enquiry->id)),
		array('label'=>__('List enquiries'), 'url'=>array('/enquiry/admin')),
		//array('label'=>'email ciudadano', 'url'=>'#', 'linkOptions'=>array('onclick'=>'getEmailForm('.$model->user0->id.')')),
	);
}
?>

<?php echo __('Emails originated from the enquiry');?>
<h1><?php echo $enquiry->title;?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
