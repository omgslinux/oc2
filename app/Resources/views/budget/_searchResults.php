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

/* @var $this BudgetController */
/* @var $data Budget */
?>

<style>
.label { color:grey; font-weight:bold;}
</style>

<p>
<?php

$model=Budget::model();

if($data['local_score'] && $data['local_concept'])
	$concept=$data['local_concept'];
else
	$concept=$data['common_concept'];
	
if($data['local_score'] && $data['local_text'])
	$text=$data['local_text'];
else
	$text=$data['common_text'];


echo '<span class="highlight_text"><b>';
if($data['code'])
	echo $data['code'].': ';

//echo CHtml::encode($concept);
echo $concept.'</b>

</span><br />';

//echo 'Score_common: '.CHtml::encode($data['common_score']).'<br />';
//echo 'Score_local: '.CHtml::encode($data['local_score']).'<br />';
//echo 'Score: '.CHtml::encode($data['score']).'<br />';

$url = Yii::app()->createAbsoluteUrl('budget/view', array('id'=>$data['id']));
echo CHtml::link($url, array('view', 'id'=>$data['id']), array('onclick'=>'js:showBudget('.$data['id'].', this);return false;')).'<br />';
	
echo '<span class="label">'.CHtml::encode($model->getAttributeLabel('initial_provision')).':</span> ';
echo number_format($data['initial_provision'], 2, ',', '.').' €<br />';

echo '<span class="label">'.CHtml::encode($model->getAttributeLabel('actual_provision')).':</span> ';
echo number_format($data['actual_provision'], 2, ',', '.').' €<br />';

echo '<span class="highlight_text">'.$text.'</span>';
?>
</p>
