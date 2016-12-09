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

/* @var $this SitePageController */
/* @var $model SitePage */

$deleteConfirm=__('Delete this page');
if(count($model->sitePageContents) > 1)
	$deleteConfirm .= ' '.__('and translations');
$deleteConfirm .='?';

$this->menu=array(
	array('label'=>__('Delete page'), 'url'=>'#', 'linkOptions'=> array('submit'=>array('delete','id'=>$model->id), 'csrf'=>true, 'confirm'=>$deleteConfirm)),
	array('label'=>__('Manage pages'), 'url'=>array('admin')),
);
$this->inlineHelp=':manual:sitepage:create';
$this->viewLog='sitePage|'.$model->id;
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'content'=>$content,'title'=>__('Update page'))); ?>
