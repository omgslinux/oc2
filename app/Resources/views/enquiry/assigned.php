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

/* @var $this EnquiryController */
/* @var $model Enquiry */

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('enquiry-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<script>
function toggleSearchOptions(){
	if ($("#searchOptions").is(":visible")){
		$("#searchOptionsToggle").html("<i class='icon-search-circled'></i>");
		$("#searchOptions").slideUp();
	}else{
		$("#searchOptionsToggle").html("<i class='icon-cancel-circled'></i>");
		$("#searchOptions").slideDown();
	}
}
</script>
<div style="position:relative;" >
	<div class="teamMenu" onCLick="js:window.location.href = '<?php echo $this->createUrl('/user/panel');?>';">
		<i class="icon-home"></i>
	</div>
</div>
<div style="position:relative; right:80px" >
	<div class="teamMenu" onCLick="js:showHelp('<?php echo getInlineHelpURL(":manual:enquiry:assigned");?>');return false;">
		<i class="icon-help-circled"></i>
	</div>
</div>
<div style="position:relative; right:40px" >
	<div class="teamMenu" class="color" onCLick="js:viewLog('enquiry');return false;">
		<i class="icon-book"></i>
	</div>
</div>
<div style="position:relative; right:120px">
	<div id="searchOptionsToggle" class="color" onCLick="js:toggleSearchOptions();return false;">
		<i class="icon-search-circled"></i>
	</div>
</div>
<?php $this->widget('InlineHelp'); ?>
<?php $this->widget('ViewLog'); ?>

<div id="enquiryPageTitle">
<h1><?php echo __('Entrusted enquiries');?></h1>
</div>

<div id="searchOptions" class="search-form">
<?php $this->renderPartial('_memberSearch',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
$this->widget('PGridView', array(
	'id'=>'enquiry-grid',
	'dataProvider'=>$model->teamMemberSearch(),
	//'filter'=>$model,
    'onClick'=>array(
        'type'=>'url',
        'call'=>'teamView',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
	        array(
				'header'=>__('Enquiry'),
				'name'=>'title',
				'value'=>'$data->title',
			),
			'assigned',
			array(
				'name'=>'state',
				'type'=>'raw',
    	        'value'=>function($data,$row){
					$value = Enquiry::getHumanStates($data->state,$data->addressed_to);
					if($data->state == ENQUIRY_ASSIGNED)
						$value = $value.' <i class="icon-attention amber"></i>';
					if($data->state == ENQUIRY_AWAITING_REPLY && $data->addressed_to == OBSERVATORY)
						$value = $value.' <i class="icon-attention amber"></i>';
					if($data->addressed_to == ADMINISTRATION && $data->id == $data->registry_number)
						// probably was addressed_to OBSERVATORY and got registry_number from id
						$value = $value.' <i class="icon-attention green"></i>';
					return $value;
				},
			),
            array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
)));
?>
