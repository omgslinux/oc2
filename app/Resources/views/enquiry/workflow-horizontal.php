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
/* @var $form CActiveForm */

$model = new Enquiry;
$stats = $model->getStatistics();
?>

<div id="workflow_diagram" style="position:absolute; margin-left:-10px; width: 930px; height:120px;">

<div class="workflowState" style="top:0px;left:0px;">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/pending.png" />
	<div class="workflowStateText" style="">
		<?php echo $model->getHumanStates(ENQUIRY_PENDING_VALIDATION);?><br />
		<?php echo '<b>'.$stats['pending'].' '.__('enquiries').'</b>'; ?>
	</div>
</div>
<div style="top:36px; left: 7px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow.png" /></div>

<div class="workflowState workflowFilter" style="top:0px;left:190px;" state="<?php echo ENQUIRY_ACCEPTED;?>">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/accepted.png" />
	<div class="workflowStateText" style="color:#a4c30b; border-color:#a4c30b;">
		<?php echo $model->getHumanStates(ENQUIRY_ACCEPTED);?><br />
		<?php echo '<b>'.$stats['accepted'].' '.__('enquiries').'</b>'; ?>
	</div>
</div>
<div style="top:36px; left: 197px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow-green.png" /></div>

<div class="workflowState" style="top:60px;left:190px;">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/rejected.png" />
	<div class="workflowStateText red" style="color:#e0081d; border-color:#e0081d;">
		<?php echo $model->getHumanStates(ENQUIRY_REJECTED);?><br />
		<?php echo '<b>'.$stats['rejected'].'%</b>'; ?>
	</div>
</div>
<div style="top:96px; left: 197px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow.png" /></div>

<div class="workflowState workflowFilter" style="top:0px;left:380px;" state="<?php echo ENQUIRY_AWAITING_REPLY;?>">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/waiting.png" />
	<div class="workflowStateText" style="">
		<?php echo $model->getHumanStates(ENQUIRY_AWAITING_REPLY);?><br />
		<?php echo '<b>'.$stats['waiting_reply'].' '.__('enquiries').'</b>'; ?>
	</div>
</div>
<div style="top:36px; left: 387px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow.png" /></div>

<div class="workflowState workflowFilter" style="top:0px;left:570px;" state="<?php echo ENQUIRY_REPLY_PENDING_ASSESSMENT;?>">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/waiting.png" />
	<div class="workflowStateText" style="">
		<?php echo $model->getHumanStates(ENQUIRY_REPLY_PENDING_ASSESSMENT);?><br />
		<?php echo '<b>'.$stats['pending_assesment'].' '.__('enquiries').'</b>'; ?>
	</div>
</div>
<div style="top:36px; left: 577px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow.png" /></div>

<div class="workflowState workflowFilter" style="top:0px;left:760px;" state="<?php echo ENQUIRY_REPLY_SATISFACTORY;?>">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/satisfactory.png" />
	<div class="workflowStateText green" style="color:#a4c30b; border-color:#a4c30b;">
		<?php echo $model->getHumanStates(ENQUIRY_REPLY_SATISFACTORY);?><br />
		<?php echo '<b>'.$stats['reply_satisfactory'].'%</b>'; ?>
	</div>
</div>
<div style="top:36px; left: 767px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow-green.png" /></div>

<div class="workflowState workflowFilter" style="top:60px;left:760px;" state="<?php echo ENQUIRY_REPLY_INSATISFACTORY;?>">
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/insatisfactory.png" />
	<div class="workflowStateText red" style="color:#e0081d; border-color:#e0081d;">
		<?php echo $model->getHumanStates(ENQUIRY_REPLY_INSATISFACTORY);?><br />
		<?php echo '<b>'.$stats['reply_insatisfactory'].'%</b>'; ?>
	</div>
</div>
<div style="top:96px; left: 767px"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/workflow/arrow.png" /></div>

</div>


