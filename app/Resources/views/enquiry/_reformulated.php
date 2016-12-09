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
?>
<?php
	$data = $dataProvider->getData();
	
	echo '<style>.highlight_row{background:#FFDEAD;}</style>';
	echo '<div style="font-size:1.3em">';
	echo __('The enquiry').' "'.$data[0]->title.'" '.__('has been reformulated').' '.(count($data)-1).' ';
	if(count($data)-1 == 1)
		echo __('time');
	else
		echo __('times');
	echo '</div>';

	$this->widget('PGridView', array(
		'id'=>'reforumulated-enquiry-grid',
		'dataProvider'=>$dataProvider,
		'template' => '{items}{pager}',
		'rowCssClassExpression'=>'($data->id == '.$model->id.')? "highlight_row":"row_id_".$row." ".($row%2?"even":"odd")',
	    'onClick'=>array(
	        'type'=>'url',
	        'call'=>Yii::app()->request->baseUrl.$onClick,
	    ),
		'columns'=>array(
				array(
					'header'=>__('Enquiry'),
					'value'=>'$data[\'title\']',
				),
				array(
					'header'=>__('State'),
					'type' => 'raw',
					'value'=>'$data->getHumanStates($data[\'state\'])',
				),
				array(
					'header'=>__('Formulated'),
					'value'=>'$data[\'created\']',
				),
				array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
	)));
?>
