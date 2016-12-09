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

if(Yii::app()->request->isAjaxRequest){
	Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
	Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
}else{
	echo '<link rel="stylesheet" type="text/css" href="'.Yii::app()->request->baseUrl.'/css/enquiry.css" />';
	echo '<link rel="stylesheet" type="text/css" href="'.Yii::app()->request->baseUrl.'/fonts/fontello/css/fontello.css" />';
	echo '<script src="'.Yii::app()->request->baseUrl.'/scripts/jquery.bpopup-0.9.4.min.js"></script>';
	echo $this->renderPartial('subscribeScript',array(),false,false);
	
}
?>

<?php
if(!Yii::app()->request->isAjaxRequest) {
	echo $this->renderPartial('//includes/socialWidgetsScript', array());
}
?>

<script>
function showBudget(budget_id, element){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/getBudget/'+budget_id,
		type: 'GET',
		beforeSend: function(){
						$('.loading_gif').remove();
						$(element).after('<img style="vertical-align:middle;" class="loading_gif" src="<?php echo Yii::app()->request->baseUrl;?>/images/loading.gif" />');
					},
		complete: function(){ $('.loading_gif').remove(); },
		success: function(data){
			if(data != 0){
				$("#budget_popup_body").html(data);
				$('#budget_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show budget");
		}
	});
}
function enquiryModal2Page(){
	$('#enquiry_popup').bPopup().close();
	window.open('<?php echo $this->createAbsoluteUrl('/e/'.$model->id); ?>',  '_blank');
}
</script>

<?php
	if(Yii::app()->request->isAjaxRequest)
		echo '<div class="modalTitle">'.__('Enquiry').'</div>';

	if($reformulatedDataprovider = $model->getReformulatedEnquires()){
		$this->renderPartial('//enquiry/_reformulated', array(	'dataProvider'=>$reformulatedDataprovider,
															'model'=>$model,
															'onClick'=>'/enquiry/view'));
	}
?>

<h1 id="enquiryTitle" <?php echo !Yii::app()->request->isAjaxRequest ? 'style="margin-top:-15px;"':'' ?>>
<?php echo $model->title?>
</h1>

<div id="enquiryDetails">
<?php $this->renderPartial('//enquiry/_detailsForCitizen', array('model'=>$model)); ?>

</div>	<!-- end enquiryDetails -->
<div>

<!-- socaial options start -->
<div id="socialOptions">
	<?php
	if($model->state >= ENQUIRY_ACCEPTED){
		$active='';
		if(EnquirySubscribe::model()->isUserSubscribed($model->id, Yii::app()->user->getUserID()))
			$active = "active";

		echo '<span style="position:relative; margin-right:10px">';
		echo '<span class="ocaxButton" onClick="js:showSocialWidgets(); ">'.
			 __('Share').'<i class="icon-share"></i>';
		echo '</span>';
		echo $this->renderPartial('//includes/socialWidgets', array(
							'fullurl' => $this->createAbsoluteUrl('/enquiry/'.$model->id),
							'url' => $this->createAbsoluteUrl('/e/'.$model->id),
							'title'=> $model->title));
		echo '</span>';

		if (Config::model()->findByPk('showExport')->value){
			echo '<span style="position:relative; margin-right:13px">';
			echo '<span class="ocaxButton" onClick="js:window.open(\''.Yii::app()->request->baseUrl.'/enquiry/export/'.$model->id.'\'); ">'.
				 __('Export').'<i class="icon-download-alt"></i>';
			echo '</span>';
			echo '</span>';
		}
		echo '<span style="position:relative;"
				id="subscribe-icon_'.$model->id.'" class="ocaxButton email-subscribe subscribe-icon_'.$model->id.' '.$active.'"
				onClick="js:showSubscriptionNotice(this, '.$model->id.');">'.
			 __('Subscribed').'<i class="icon-mail-1"></i>';
		echo '<span class="ocaxButtonCount" id="subscriptionTotal">'.count($model->subscriptions).'</span>';
		echo '<div class="alert subscription_notice" style="margin-top:-30px;"></div>';
		echo '</span>';
	}?>
</div>
<!-- social options stop -->

<?php
if($model->state == ENQUIRY_PENDING_VALIDATION && $model->user == Yii::app()->user->getUserID()){
	echo '<div style="font-style:italic;margin-top:-30px;margin-bottom:10px;">'.__('You can').' '.
		 CHtml::link(__('edit the enquiry'),array('enquiry/edit','id'=>$model->id)).' '.__('and even').' '.
		 CHtml::link(__('delete it'),"#",
                    array(
						"submit"=>array('delete', 'id'=>$model->id),
						"params"=>array('returnUrl'=>Yii::app()->request->baseUrl.'/user/panel'),
						'confirm' => __('Are you sure?'),
						'csrf'=>true)).
		 ' '.__('until it has been accepted by the observatory.').
		 '</div>';
}
?>

<?php echo $this->renderPartial('_view', array('model'=>$model)); ?>
</div>

<div class="clear"></div>
