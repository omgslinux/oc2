<?php

/**
 * OCAX -- Citizen driven Municipal Observatory software
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

/*
/* @var $this BudgetController */
/* @var $model Budget
 * $indent
 * $globals
 */
?>

<?php
	$child_budgets  = $model->budgets;

	foreach($child_budgets as $budget){

		$budget_indent = 0;
		if($indent > 0)
			$budget_indent = 32;

		echo '<div style="margin-left:'.$budget_indent.'px;margin-top:20px;">';
			if($budget->budgets)
				echo '<div style="margin-left:'. (-16 - 4) .'px">';	// 16 width of icon
			else
				echo '<div style="margin-left:0px">';

			if($budget->budgets){
			echo '<div style="float:left;" class="showChildrenIcon">';
			echo '<img id="toggle_'.$budget->id.'" src="'.Yii::app()->request->baseUrl.
				 '/images/plus_icon.png" onClick="js:toggleChildren('.$budget->id.','.$indent.','.$globals["cache_id"].');"/>';
			echo '</div>';
			}
			echo '<div class="budget" budget_id="'.$budget->id.'" style="float:left;">';
				echo '<span class="barBudgetConcept">'.$budget->code.'. '
						.$budget->getConcept().' '.format_number($budget->actual_provision)
						//.', root actual '.$globals['root_actual_provision']	
						//.', executed '.format_number($budget->getExecuted())
						//.', root executed '.$globals['root_executed']				
						.'</span> ';
				$percent=percentage($budget->actual_provision,$globals['root_actual_provision']);
				$width = $globals['graph_width']*(percentage($budget->actual_provision,$globals['largest_provision']) / 100);
				echo '<div class="actual_provision_bar" style="width:'.$width.'px;">';
				echo '<div class="graph_bar_percent">'.$percent.'%</div>';
				echo '</div>';
				
				if($executed=$budget->getExecuted()){
					$percent=percentage($executed, $globals['root_actual_provision']);
					$width = $globals['graph_width']*(percentage($executed, $globals['largest_provision']) / 100);
					echo '<div class="executed_bar" style="width:'.$width.'px;">';
					echo '<div class="graph_bar_percent">'.$percent.'%</div>';
					echo '</div>';
				}
				
			echo '</div>';
		echo '</div>';
		echo '<div style="clear:both"></div>';

		if($budget->budgets){
			echo '<div id="budget_children_'.$budget->id.'" style="display:none"></div>';
		}
		echo '</div>';
	}

?>
