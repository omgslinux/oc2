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

$this->menu=array(
	array('label'=>__('Sent emails'), 'url'=>array('/email/index/', 'id'=>$model->id, 'menu'=>'team')),
	array('label'=>__('List enquiries'), 'url'=>array('/enquiry/assigned')),
);
if($model->state == ENQUIRY_ACCEPTED){
	$item = array( array('label'=>__('Submit enquiry'), 'url'=>array('/enquiry/submit', 'id'=>$model->id)) );
	array_splice( $this->menu, 0, 0, $item );
}
if(	(	$model->state < ENQUIRY_AWAITING_REPLY && $model->state != ENQUIRY_REJECTED) ||
		($model->state == ENQUIRY_AWAITING_REPLY && $model->addressed_to == OBSERVATORY) ){
	$item = array( array('label'=>__('Edit enquiry'), 'url'=>array('/enquiry/edit', 'id'=>$model->id)) );
	array_splice( $this->menu, 0, 0, $item );
}
if($model->state == ENQUIRY_ASSIGNED){
	$item = array( array('label'=>__('Accept / Reject'), 'url'=>array('/enquiry/validate', 'id'=>$model->id)) );
	array_splice( $this->menu, 0, 0, $item );
}
if($model->state == ENQUIRY_AWAITING_REPLY){
	$item = array( array('label'=>__('Add reply'), 'url'=>array('/reply/create?enquiry='.$model->id)) );
	array_splice( $this->menu, 0, 0, $item );
}
if($model->state >= ENQUIRY_REPLY_PENDING_ASSESSMENT){
	$reply = Reply::model()->findByAttributes(array('enquiry'=>$model->id));
	if ($reply===null){
		throw new CHttpException(404,'The requested Reply does not exist.');
	}
	$item = array( array('label'=>__('Correct reply'), 'url'=>array('/reply/update', 'id'=>$reply->id)) );
	array_splice( $this->menu, 0, 0, $item );	
}
if($model->state >= ENQUIRY_AWAITING_REPLY && $model->addressed_to == ADMINISTRATION){
	$label=__('Correct submission');
	if($model->id == $model->registry_number)	// was addressed_to OBSERVATORY and got registry_number from id
		$label = $label.'<i class="icon-attention green"></i>';
	$item = array( array('label'=>$label, 'url'=>array('/enquiry/submit', 'id'=>$model->id)) );
	array_splice( $this->menu, 0, 0, $item );	
}
if($model->state == ENQUIRY_REPLY_PENDING_ASSESSMENT){
	$item = array( array('label'=>__('Assess reply'),  'url'=>array('/enquiry/assess', 'id'=>$model->id)) );
	array_splice( $this->menu, 0, 0, $item );
}
if($model->state > ENQUIRY_REPLY_PENDING_ASSESSMENT){
	$item = array( array('label'=>__('Reformulate enquiry'), 'url'=>array('/enquiry/reformulate?related='.$model->id))  );
	array_splice( $this->menu, 0, 0, $item );
}

$this->inlineHelp=':manual:enquiry:teamview';
$this->viewLog='Enquiry|'.$model->id;
?>

<?php echo $this->renderPartial('_teamView', array('model'=>$model)); ?>

<?php if(Yii::app()->user->hasFlash('prompt_email')):?>
    <div class="flash-notice">
		<?php echo Yii::app()->user->getFlash('prompt_email');?><br />
		<?php 
		$url=Yii::app()->request->baseUrl.'/email/create?enquiry='.$model->id.'&menu=team';
		?>
		<button onclick="js:window.location='<?php echo $url?>';"><?php echo __('Yes');?></button>
		<button onclick="$('.flash-notice').slideUp('fast')"><?php echo __('No');?></button>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>
