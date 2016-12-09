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

/* @var $this EnquiryController */
/* @var $model Enquiry */
/* @var $form CActiveForm */

$model = new Enquiry;
?>

<style>
#workflow_diagram > div {
	position:absolute;
	font-size:0.95em; line-height:95%; width:150px;
}
</style>

<div id="workflow_diagram" style="background-image: url('<?php echo Yii::app()->request->baseUrl; ?>/images/workflow-vertical.png'); height:415px; position:relative;">
<div style="top:25px;left:120px;"><?php echo $model->getHumanStates(ENQUIRY_PENDING_VALIDATION);?></div>
<div style="top:107px;left:0px;width:140px;" state="<?php echo ENQUIRY_ACCEPTED;?>"><?php echo $model->getHumanStates(ENQUIRY_ACCEPTED);?></div>
<div style="top:107px;left:218px;width:145px;" state="<?php echo ENQUIRY_REJECTED;?>"><?php echo $model->getHumanStates(ENQUIRY_REJECTED);?></div>
<div style="top:190px;left:120px;" state="<?php echo ENQUIRY_AWAITING_REPLY;?>"><?php echo $model->getHumanStates(ENQUIRY_AWAITING_REPLY);?></div>
<div style="top:275px;left:120px;" state="<?php echo ENQUIRY_REPLY_PENDING_ASSESSMENT;?>"><?php echo $model->getHumanStates(ENQUIRY_REPLY_PENDING_ASSESSMENT);?></div>
<div style="top:355px;left:0px;width:140px;" state="<?php echo ENQUIRY_REPLY_INSATISFACTORY;?>"><?php echo $model->getHumanStates(ENQUIRY_REPLY_INSATISFACTORY);?></div>
<div style="top:355px;left:218px;width:145px;" state="<?php echo ENQUIRY_REPLY_SATISFACTORY;?>"><?php echo $model->getHumanStates(ENQUIRY_REPLY_SATISFACTORY);?></div>

</div>
