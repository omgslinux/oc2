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
/* @var $model Budget */

$this->menu=array(
	array('label'=>__('Create Year'), 'url'=>array('createYear')),
	array('label'=>__('Default year').' '.Config::model()->findByPk('year')->value, 'url'=>array('/config/update/year?returnURL=/budget/admin')),
);
if(File::model()->findByAttributes(array('model'=>'Budget'))){
	$restore = array( array('label'=>__('Restore database'), 'url'=>'#', 'linkOptions'=>array('onclick'=>'js:showBudgetDumps();')));
	array_splice( $this->menu, 1, 0, $restore );
}
$this->inlineHelp=':manual:budget:admin';
$this->viewLog="budget";
?>

<script>
function showBudgetDumps(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/file/showBudgetFiles',
		type: 'GET',
		// beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			if(data != 0){
				$("#budget_dumps_content").html(data);
				$('#budget_dumps').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show budget dumps");
		}
	});
}
function restoreBudgets(file_id){
	if(confirm('<?php echo __('Restore budgets');?>?') == false)
		return;
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/restoreBudgets/'+file_id,
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>' },
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			$('#budget_dumps').bPopup().close();
			location.reload(true);
		},
		error: function() {
			alert("Error on restore budgets");
		}
	});
}
</script>

<h1><?php echo __('Manage years');?></h1>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view pgrid-cursor-pointer'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'budget-grid',
	'selectableRows'=>1,
	'selectionChanged'=>'function(id){ location.href = "'.$this->createUrl('/budget/updateYear').'/"+$.fn.yiiGridView.getSelection(id);}',
	'dataProvider'=>$years,
	'ajaxUpdate'=>true,
	'columns'=>array(
		'year',
		array(
			'header'=>__('Published'),
			'name'=>'code',
			'value'=>'$data[\'code\']',
		),
		array(
			'header'=>__('Population'),
			'name'=>'initial_provision',
			'value'=>'substr_replace($data[\'initial_provision\'] ,"",-3)',	// remove decimals
		),
)));
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<div id="budget_dumps" class="modal" style="width:600px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="budget_dumps_content"></div>
</div>

<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>


