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

if($displayType == 'grid'){
	Yii::app()->clientScript->registerScript('search', "
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('enquiry-grid', {
			data: $(this).serialize()
		});
		resetFormElements=1;
		return false;
	});
	");
}else{
	Yii::app()->clientScript->registerScript('search', "
	$('.search-form form').submit(function(){
		$.fn.yiiListView.update('enquiry-list', {
			data: $(this).serialize()
		});
		resetFormElements=1;
		return false;
	});
	");
}

echo $this->renderPartial('//includes/socialWidgetsScript', array());
$this->widget('EnquiryModal');
?>

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/enquiry.css" />

<style>
.highlightWorkflowFilter, .selectedWorkflowFilter{
		background-color: rgba(255, 255, 255, 0.8);
}
</style>

<script>
var resetFormElements = 0;

$(function() {
	$(".workflowFilter").on('click', function() {
			filterByDiagram($(this).attr('state'));
	});
	$( document ).on( "mouseenter", ".enquiryPreview", function() {
		$(this).find('.title').addClass('highlightWithColor');
		$(this).find('.created').addClass('highlightWithColor');
	});
	$( document ).on( "mouseleave", ".enquiryPreview", function() {
		$(this).find('.title').removeClass('highlightWithColor');
		$(this).find('.created').removeClass('highlightWithColor');
	});
	$( document ).on( "mouseenter", ".workflowFilter", function() {
		$(this).addClass('highlightWorkflowFilter');
	});
	$( document ).on( "mouseleave", ".workflowFilter", function() {
		$(this).removeClass('highlightWorkflowFilter');
	});
	$(window).scroll(function() {
		if($(this).scrollTop() > 300)
			$('.goToTop').fadeIn(500);
		else
			$('.goToTop').fadeOut(500);
	});
	$(".goToTop").click(function(){
		$("html, body").animate({ scrollTop: 0 }, 0);
		$('.goToTop').hide();
	});
});
function basicFilter(el, filter){
	$(el).parent().find('li').removeClass('activeItem');
	$(el).addClass('activeItem');
	$("#Enquiry_basicFilter").val(filter);
	$("#search_enquiries").submit();
}
function filterByDiagram(state){
	humanStates = <?php echo json_encode($model->getHumanStates()) ?>;
	$("#Enquiry_state").val(state);
	$("#search_enquiries").submit();
	$('.workflowFilter').removeClass('selectedWorkflowFilter');
	$("[state='"+state+"']").addClass('selectedWorkflowFilter');
}
function resetToggleIcons(){
	$('#searchOptionsToggle').find('i').removeClass('icon-cancel-circled');
	$('#workflowOptionsToggle').find('i').removeClass('icon-cancel-circled');
	$('#searchOptionsToggle').find('i').addClass('icon-search-circled');
	$('#workflowOptionsToggle').find('i').addClass('icon-flow-tree');
}
function toggleSearchOptions(){
	resetForm();
	resetToggleIcons();
	if ($("#advancedFilterOptions").is(":visible")){
		$("#advancedFilterOptions").hide();
		$("#workflowFilterOptions").show();
		$('.workflowFilter').removeClass('selectedWorkflowFilter');	
	}else{
		$("#Enquiry_basicFilter").val('');
		$("#advancedFilterOptions").show();
		$("#workflowFilterOptions").hide();
		$('#searchOptionsToggle').find('i').removeClass('icon-search-circled');
		$('#searchOptionsToggle').find('i').addClass('icon-cancel-circled');
	}
}
function resetForm(){
	if(resetFormElements == 0)
		return;
	$('#Enquiry_searchText').val('');
	$("#Enquiry_state").val('');
	$('#Enquiry_type').val('');
	$('#Enquiry_searchDate_min').val('');
	$('#Enquiry_searchDate_max').val('');
	$("#Enquiry_basicFilter").val('');
	$("#search_enquiries").submit();
	resetFormElements = 0;
}
</script>

<div id="toggleIcons" style="position:relative;">
	<div id="searchOptionsToggle" onCLick="js:toggleSearchOptions();return false;">
		<i class="icon-search-circled"></i>
	</div>
</div>

<div id="enquiryPageTitle">
	<h1 style="margin-top:-10px;"><?php echo __('Enquiries made to date');?></h1>
	<p style="margin-top:0px;">
		<?php	echo __('This is a list of enquiries made by citizens like you.').'.';
				echo '<span style="margin-left:40px">'.CHtml::link(__('Formulate a new enquiry'),array('enquiry/create/')).'</span>';
		?>
	</p>
</div>

<div id="filterOptions" style="margin-top:-10px; height:125px;"> <!-- filter options start -->
	<div id="advancedFilterOptions" class="search-form" style=" display:none;">
		<?php $this->renderPartial('_searchPublic',array('model'=>$model)); ?>
	</div>
	<div id="workflowFilterOptions" style="display:inline-block">
		<?php $this->renderPartial('//enquiry/workflow-horizontal'); ?>
	</div>
</div>	<!-- filter options end -->
<div class="horizontalRule"></div>

<div id="enquiryDisplayTypeIcons">
<i class="icon-th-large" onclick="js:location.href='<?php echo Yii::app()->request->baseUrl;?>/enquiry?display=list'"></i>
<i class="icon-th-list" onclick="js:location.href='<?php echo Yii::app()->request->baseUrl;?>/enquiry?display=grid'"></i>
</div>

<div id="enquiryList" style="position:relative">
<?php
$template = '<div style="height:20px;">'.
			'<div style="float:left; position:absolute; top: -20px; left: 60px;">{summary}</div>'.
			'<div style="float:right; position:absolute; top: -20px; right:5px; ">{pager}</div><div class="clear">'.
			'</div></div>'.
			'{items}';

if($displayType == 'grid'){
	$this->widget('PGridView', array(
		'id'=>'enquiry-grid',
		'dataProvider'=>$model->publicSearch(),
		'rowCssClassExpression'=>function($row, $data){
			if(Yii::app()->user->isGuest)
				return $row % 2 ? 'even' : 'odd';
			if(EnquirySubscribe::model()->isUserSubscribed($data->id, Yii::app()->user->getUserID()))
				return 'tag_enquiry_row_as_subscribed';
			else
				return $row % 2 ? 'even' : 'odd';
		},
		'onClick'=>array(
			'type'=>'javascript',
			'call'=>'showEnquiry',
		),
		'ajaxUpdate'=>true,
		'template' => $template,
		'columns'=>array(
		array(
			'header'=>__('Enquiries'),
			'name'=>'title',
			'value'=>'$data[\'title\']',
		),
		array(
			'header'=>__('Formulated'),
			'name'=>'created',
			'value'=>'format_date($data[\'created\'])',
		),
		array(
			'header'=>__('State'),
			'name'=>'state',
			'type' => 'raw',
			'value'=>'$data->getHumanStates($data[\'state\'])'
		),
	array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
	)));
}else{
	$this->widget('zii.widgets.CListView', array(
		'id'=>'enquiry-list',
		'dataProvider'=>$dataProvider,
		'afterAjaxUpdate'=>'function(){
							$("html, body").animate({scrollTop: $("#scrollTop").position().top }, 100);
							}',
		'itemView'=>'_preview',
		'emptyText'=>'<div class="sub_title" style="margin-bottom:60px;">'.__('No enquiries here').'</div>',
		'template' => $template,
	));
}
?>
</div>

<div class="clear"></div>
<div class="goToTop">&#x25B2;&nbsp;&nbsp;&nbsp;<?php echo __('go to top');?></div>
