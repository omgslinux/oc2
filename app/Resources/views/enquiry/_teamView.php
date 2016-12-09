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

if(Yii::app()->request->isAjaxRequest){
	Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
}
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<h1 style="margin-bottom: 15px;"><?php echo __('The Enquiry');?></h1>

<?php $this->renderPartial('//enquiry/_detailsForTeam', array('model'=>$model));?>

<div class="horizontalRule" style="margin-top:20px"></div>

<?php
if($reformulatedDataprovider = $model->getReformulatedEnquires()){
	$this->renderPartial('//enquiry/_reformulated', array(	'dataProvider'=>$reformulatedDataprovider,
															'model'=>$model,
															'onClick'=>'/enquiry/teamView'));

}
echo '<h1 style="margin-bottom: 15px;">'.$model->title.'</h1>';
echo $this->renderPartial('//enquiry/_view', array('model'=>$model));
?>
