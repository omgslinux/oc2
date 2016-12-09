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

/* @var $this BudgetController */
/* @var $model Budget */

?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/budgetDescription.css" />

<?php 
if(!Yii::app()->request->isAjaxRequest)
	echo '<script src="'.Yii::app()->request->baseUrl.'/scripts/jquery.bpopup-0.9.4.min.js"></script>';
else{
	//Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	//Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
	//Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
}


if(Yii::app()->clientScript->isScriptRegistered('jquery.js'))
	Yii::app()->clientScript->scriptMap['jquery.js'] = false;
if(Yii::app()->clientScript->isScriptRegistered('jquery.min.js'))
	Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
if(Yii::app()->clientScript->isScriptRegistered('jquery.ba-bbq.js'))
	Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;

$root_budget = $model->findByAttributes(array('csv_id'=>$model->csv_id[0], 'year'=>$model->year));
if(!$root_budget){
	$this->render('//site/error',array('code'=>'Budget not found', 'message'=>__('Budget with internal code').' "'.$model->csv_id[0].'" '.__('is not defined')));
	Yii::app()->end();
}

$dataProvider=new CActiveDataProvider('Enquiry', array(
				'criteria'=>array(
					'condition'=>'budget = :id AND state >= '.ENQUIRY_ACCEPTED,
					'params'=>array(':id'=>$model->id),
					'order'=>'created DESC',
				),
				'pagination'=>array(
					'pageSize'=>20,
				),
		));
?>

<script>

function getAnualComparative(budget_id){
	if($('#budget_comparative').html() != ''){
		_showAnualComparative();
		return;
	}
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/getAnualComparison/'+budget_id,
		type: 'GET',
		dataType: 'json',
		beforeSend: function(){ /*$('.pie_loading_gif').hide(); $(loading_gif).show();*/ },
		complete: function() { /*$('.pie_loading_gif').hide();*/ },
		success: function(data){
			$('#budget_comparative').html(data);
			_showAnualComparative();
		},
		error: function() {
			alert("Error on getAnualComparison");
		}
	});
}
function _showAnualComparative(){
	$('#compareYearsLink').hide();
	$('#budget_details').hide();
	$('#hideComparisonLink').show();
	$('#budget_comparative').show();
}
function showBudgetDetails(){
	$('#hideComparisonLink').hide();
	$('#budget_comparative').hide();
	$('#compareYearsLink').show();
	$('#budget_details').show();
}
function budgetModal2Page(){
	$('#budget_popup').bPopup().close();
	window.open('<?php echo $this->createAbsoluteUrl('/budget/'.$model->id); ?>',  '_blank');
}
<?php if(Yii::app()->user->canEditBudgetDescriptions()){ ?>
function editBudgetDescription(){
	if(typeof budgetDetailsUpdated == 'function')
		budgetDetailsUpdated();	//this function is located in budget/index
	$('#budget_popup').bPopup().close();
	window.open("<?php echo Yii::app()->request->baseUrl.'/budgetDescription/modify?budget='.$model->id ?>",  '_blank');
	return null;
}
<?php } ?>
</script>

<?php
	$category=$model->getCategory();

	if(Yii::app()->request->isAjaxRequest){
		echo '<div class="modalTitle">'.__('Budget').': '.$category.'</div>';
		echo '<h1>'.$model->getTitle().'</h1>';
	}else{
		echo '<div style="font-size:16px;margin-top:-20px;">&nbsp;'.$category.'</div>';
		echo '<h1>'.$model->getTitle().'</h1>';
	}

	echo '<div>';
		echo '<div id="budget_box" style="width:450px;padding:0px;margin-left:10px;float:right">';

		if(count($model->getAllBudgetsWithCSV_ID()) > 1){
			$compareYears = '<span id="compareYearsLink" class="link" style="float:right;font-size:1.1em" '.
					'onclick="js:getAnualComparative('.$model->id.')">'.__('Compare years').
					'</span>';
			$words = str_replace('%s', $model->year, __('Details for %s'));
			$showBudgetDetails='<span id="hideComparisonLink" class="link" style="float:right;font-size:1.1em;display:none" '.
								'onclick="js:showBudgetDetails()">'.$words.'</span>';
			echo $showBudgetDetails;
			echo $compareYears;
		}
		echo '<div style="clear:both"></div>';

		echo '<div id="budget_details">';
		$this->renderPartial('_budgetDetails',array('model'=>$model,
													'showCreateEnquiry'=>1,
													'showLinks'=>1,
													'noConcept'=>1,
													'showMore'=>1,
													'hideConcept'=>1,
													'showComparison'=>1,
												),false,true);
		echo '</div>';
		echo '<div id="budget_comparative" style="display:none"></div>';
		echo '</div>';

	echo '<div style="margin-top:15px; font-size:1.2em">';
	if($description = $model->getDescription()){
		if(Yii::app()->user->canEditBudgetDescriptions()){
			if(Yii::app()->request->isAjaxRequest)
				echo '<a href="#" onclick="js:editBudgetDescription()">'.__('Can you improve this description?').'</a>';
			else
				echo '<a href="'.Yii::app()->request->baseUrl.'/budgetDescription/modify?budget='.$model->id.'">'.__('Can you improve this description?').'</a>';
			echo '<br />';
		}
		echo '<div class="budgetExplication">'.$description->description.'</div>';
		if($description->modified){
			$state_description = BudgetDescState::model()->getDescription($description->csv_id, $description->language);
			if($state_description && $state_description->description){
				echo '<div class="link" style="margin-top:15px;" onClick="js:$(\'#state_desc\').slideDown();$(this).empty();return false">'.
					  __('Read the administration\'s description').'</div>';
				echo '<div id="state_desc" style="display:none;margin-top:10px;">';
				echo '<div class="sub_title">'.__('Official description').'</div>';
				echo '<div class="budgetExplication">'.$state_description->description.'</div>';
				echo '</div>';
			}
		}
	}else{
		if(Yii::app()->user->canEditBudgetDescriptions()){
			if(Yii::app()->request->isAjaxRequest)
				echo '<a href="#" onclick="js:editBudgetDescription()">'.__('Please consider adding a description here').'</a>';
			else
				echo '<a href="'.Yii::app()->request->baseUrl.'/budgetDescription/modify?budget='.$model->id.'">'.__('Please consider adding a description here').'</a>';
			echo '<br />';
		}
	}

	echo '<p style="font-size:1.2em;margin-top:35px;">';
	if(!$dataProvider->getData()){
		echo '<span>'.__('No enquiries have been made about this budget yet').'.</span><br />'.
			CHtml::link(__('Do you wish to make an enquiry').'?' ,array('enquiry/create', 'budget'=>$model->id));
	}
	echo '</p>';
	echo '</div>';
	echo '</div>';

?>
<div style="clear:both"></div>

<?php
if(count($dataProvider->getData()) > 0){
	echo '<p>';
	if(count($dataProvider->getData()) == 1)
		echo '<div style="font-size:1.3em;margin-top:25px;">'.__('One enquiry has already been made about this budget').'</div>';
	else{
		$str = str_replace("%s", count($dataProvider->getData()), __('%s enquiries have already been made about this budget'));
		echo '<div style="font-size:1.3em;margin-top:25px;">'.$str.'</div>';
	}

	$this->widget('PGridView', array(
		'id'=>'budgets-enquiry-grid',
		'dataProvider'=>$dataProvider,
	    'onClick'=>array(
	        'type'=>'url',
	        'call'=>Yii::app()->request->baseUrl.'/enquiry/view',
	    ),
		'template' => '{items}{pager}',
		'ajaxUpdate'=>true,
		'columns'=>array(
				array(
					'header'=>__('Enquiry'),
					'name'=>'title',
					'value'=>'$data[\'title\']',
				),
				array(
					'header'=>__('Formulated'),
					'name'=>'created',
					'value'=>'format_date($data[\'created\'])',
				),
				array(
					'header'=>'Estat',
					'name'=>'state',
					'type' => 'raw',
					'value'=>'$data->getHumanStates($data[\'state\'])',
				),
    	        array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
	)));
}
?>
</p>

