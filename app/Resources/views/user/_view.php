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
 
/* @var $this UserController */
/* @var $data User */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('username')); ?>:</b>
	<?php echo CHtml::encode($data->username); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fullname')); ?>:</b>
	<?php echo CHtml::encode($data->fullname); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('joined')); ?>:</b>
	<?php echo CHtml::encode($data->joined); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_socio')); ?>:</b>
	<?php echo CHtml::encode($data->is_socio); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_team_member')); ?>:</b>
	<?php echo CHtml::encode($data->is_team_member); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_editor')); ?>:</b>
	<?php echo CHtml::encode($data->is_editor); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_manager')); ?>:</b>
	<?php echo CHtml::encode($data->is_manager); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_admin')); ?>:</b>
	<?php echo CHtml::encode($data->is_admin); ?>
	<br />

</div>
