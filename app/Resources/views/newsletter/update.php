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

/* @var $this NewsletterController */
/* @var $model Newsletter */

$this->menu=array(
	array('label'=>__('Create bulk email'), 'url'=>array('create')),
	array('label'=>__('View bulk email'), 'url'=>array('adminView', 'id'=>$model->id)),
	array('label'=>__('Manage bulk email'), 'url'=>array('admin')),
);
$this->inlineHelp=':profiles:admin:newsletters';
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'total_recipients'=>$total_recipients)); ?>
