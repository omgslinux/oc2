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

/* @var $this BudgetController */
/* @var $model Budget */

$graph_width=929;
?>

<style>
.loader_gif {
	margin-left:20px;
	float:right;
	display:none;
}
.loader_gif img {
	margin-top:5px;
 	margin-left:5px;
}
.key { width: 15px; height: 15px; margin-right: 10px; float:left; }
.key_label { float:left; margin-top:-3px; }
</style>

<script>

function lighten(color){
	percent = 15;

	var rgb = color.replace('rgb(', '').replace(')', '').split(',');
	var red = $.trim(rgb[0]);
	var green = $.trim(rgb[1]);
	var blue = $.trim(rgb[2]);
	red = parseInt(red * (100 + percent) / 100);
	green = parseInt(green * (100 + percent) / 100);
	blue = parseInt(blue * (100 + percent) / 100);
	rgb = 'rgb(' + red + ', ' + green + ', ' + blue + ')';
	return rgb;
}

var globals = new Array();
var lightened_color;

$(function() {
	$('#the_graphs').on("click", '.budget', function() {
		showBudget($(this).attr('budget_id'), $(this).find('span').eq(0));
	});
	
	rgb_theme_color = $('.actual_provision_bar').first().css("background-color");
	$('.swatch_actual').css("background-color",rgb_theme_color);
	
	if($('.executed_bar').length != 0){
		lightened_color = lighten(rgb_theme_color);
		$('.executed_bar').css("background-color",lightened_color);
		$('.swatch_executed').css("background-color",lightened_color);
	}else
		$('.key_executed').hide();
});

function colorExecutedBars(){
	$('.executed_bar').css("background-color",lightened_color);
}

function _toggleChildren(id){
	if ($('#budget_children_'+id).is(":visible")){
		$('#budget_children_'+id).slideUp('fast');
		$('#toggle_'+id).attr('src','<?php echo Yii::app()->request->baseUrl;?>/images/plus_icon.png');
	}else{
		$('#budget_children_'+id).slideDown('fast');
		$('#toggle_'+id).attr('src','<?php echo Yii::app()->request->baseUrl;?>/images/minus_icon.png');
	}
}

function toggleChildren(budget_id, indent, cache_id){
	if ($('#budget_children_'+ budget_id +':not(:empty)').length) {
		_toggleChildren(budget_id);
		return;
	}
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/getChildBars/'+budget_id,
		type: 'GET',
		data: {
				'indent': indent+1,
				'globals': globals[cache_id],
				},
		//dataType: 'json',
		beforeSend: function(){	$('#bar_loader_gif').appendTo($('div[budget_id="' + budget_id + '"]').find('span').eq(0));
								$('#bar_loader_gif').show();
							},
		complete: function(){	$('#bar_loader_gif').hide();
								colorExecutedBars();
							},
		success: function(childBars){
			if(childBars){
				$('#budget_children_'+ budget_id).html(childBars);
				_toggleChildren(budget_id);
			}
		},
		error: function() {
			alert("Error on getChildBars");
		}
	});
}
</script>


<?php
	echo '<div id="bar_display" style="margin: 5px 0 15px 0px">';
	foreach($featured as $featured_budget){
	
		$criteria = new CDbCriteria;
		$criteria->condition = 'parent = :featured';
		$criteria->params[':featured'] =$featured_budget->id;
		
	
		$largest_provisions = array('actual'=>0, 'executed'=>0);
		foreach(Budget::model()->findAll($criteria) as $budget){
			if($budget->actual_provision > $largest_provisions['actual'])
				$largest_provisions['actual'] = $budget->actual_provision;
				
			if($budget->getExecuted() > $largest_provisions['executed'])
				$largest_provisions['executed'] = $budget->getExecuted();
		}
		arsort($largest_provisions);
		reset($largest_provisions);
		$largest_provision =max($largest_provisions);

		$cache_id=$featured_budget->id;	
		$globals=array(	'root_executed' => $featured_budget->getExecuted(),
						'root_actual_provision' => $featured_budget->actual_provision,
						'largest_provision'=> $largest_provision,
						'queried_budget' => $featured_budget->id,
						'graph_width'=>$graph_width,
						'cache_id'=>$cache_id,
		);
		echo '<script>';
		echo 'globals['.$cache_id.']='.CJSON::encode($globals);	// a place to keep params needed for ajax @ toggleChildren
		echo '</script>';

		echo '<div class="graph_bar_group graph_group" id="anchor_'.$featured_budget->id.'">';
		echo '<div style="float:left; margin: -5px 0 0px 0;">';
		echo '<a class="graph_title" href="'.Yii::app()->request->baseUrl.'/budget/view/'.$featured_budget->id.
			 '" onclick="js:showBudget('.$featured_budget->id.', this);return false;">';
		echo CHtml::encode($featured_budget->getConcept()).'</a>';
		echo '<span class="graph_title" style="margin-left:20px; padding-right:10px; white-space:nowrap;">'.
			 format_number($featured_budget->actual_provision).
			 '</span>';
		echo '</div>';
		echo '<div style="float:right;margin-top:-5px;margin-right:10px;">';
		echo '<div class="key swatch_actual"></div><div class="key_label">'.__('Actual').'</div><br />';
		echo '<div class="key swatch_executed key_executed"></div><div class="key_label key_executed">'.__('Executed').'</div>';
		echo '</div>';
		echo '<div style="clear:both"></div>';
		echo '<div class="graph_bar_container">';
			$this->renderPartial('childBars', array('model'=>$featured_budget,'indent'=>0,'globals'=>$globals));
		echo '</div>';
		echo '</div>';

	}
	echo '</div>';
?>

<div id="bar_loader_gif" class="loader_gif">
<img src="<?php echo Yii::app()->request->baseUrl;?>/images/preloader.gif"/></div>
<div style="clear:both"></div>
</div>

