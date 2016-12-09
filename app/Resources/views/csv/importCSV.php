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

?>

<?php
$yearBudget=Budget::model()->findByAttributes(array('parent'=>Null,'year'=>$model->year));
if ($yearBudget===null){
	throw new CHttpException(404,'The requested Budget year does not exist.');
}
$this->menu=array(
	array('label'=>__('Edit').' '.$model->year, 'url'=>array('//budget/updateYear/'.$yearBudget->id)),
	array('label'=>__('List Years'), 'url'=>array('//budget/admin')),
);
if($model->csv){
	$importAgain = array( array('label'=>__('Upload CSV again'), 'url'=>array('csv/importCSV/'.$model->year)), );
	array_splice( $this->menu, 0, 0, $importAgain );
}

$this->inlineHelp=':manual:csv:import';
?>

<style>
p { font-size:1.3em; }
.error { margin-left:10px; color:red; }
.warn { margin-left:10px; color:#CD661D; }
.success { margin-left:10px; color:green; }
</style>

<script>
function changeYear(el){
	$('#ImportCSV_year').val( $(el).val() );
}
function step41_to_5(){
	$('#step_41').hide();
	$('#step_5').show();
}
function checkFormat(el, next){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/csv/checkCSVFormat',
		type: 'GET',
		dataType: 'json',
		data: { 'csv_file': '<?php echo $model->csv;?>' },
		//beforeSend: function(){  },
		//complete: function(){  },
		success: function(data){
					if(data.error){
						$(el).replaceWith('<span class="error">'+data.error+'</span>');
					}else{
						$(el).replaceWith('<span class="success">'+data+' registers seem ok</span>');
						$('#step_'+next).show();
					}
		},
		error: function() {
			$(el).replaceWith("<?php
				$link = Yii::app()->getBaseUrl(true).'/csv/checkCSVFormat?csv_file='.$model->csv;
				echo	'<span class=\"error\"><br />error on checkFormat. For more detail see<br />'.
						'<a href=\"'.$link.'\">'.$link.'</a>'.
						'</span>';
			?>");
		},
	});
}

function addMissingValues(el, next){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl.'/csv/addMissingValues/';?>',
		type: 'GET',
		dataType: 'json',
		data: { 'csv_file': '<?php echo $model->csv;?>', 'year': '<?php echo $model->year;?>' },
		beforeSend: function(){ $("#check_missing_values_button").attr("disabled", "disabled"); $('#checking_missing_values').show(); },
		complete: function(){ $('#checking_missing_values').hide(); },
		success: function(data){
					if(data.error){
						$(el).replaceWith('<span class="error">'+data.error+'</span>');
					}else{
						if(data.updated > 0 || data.new_concepts > 0 || data.new_totals > 0){
							download = '<?php echo Yii::app()->request->baseUrl; ?>/csv/downloadUpdatedCSV/<?php echo $model->year;?>';
							msg = '<br />1. Download new CSV: <a href="'+download+'"><?php echo $model->csv;?></a>';
							msg = msg+'<br />2. Open new CSV on your PC and check it.';
							msg = msg+'<br />3. Use new CSV to import your data.';
						}else{
							msg = '';
							$('#step_'+next).show();
						}
						$(el).replaceWith('<span class="success">'+data.msg+'</span> '+msg);
					}
		},
		error: function() {
			$(el).replaceWith("<?php
				$link = Yii::app()->getBaseUrl(true).'/csv/addMissingValues?csv_file='.$model->csv.'&year='.$model->year;
				echo	'<span class=\"error\"><br />error on checkMissingValues. For more detail see<br />'.
						'<a href=\"'.$link.'\">'.$link.'</a>'.
						'</span>';
			?>");
		},
	});
}
function checkTotals(el,next_step){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/csv/checkCSVTotals',
		type: 'GET',
		dataType: 'json',
		data: { 'csv_file': '<?php echo $model->csv;?>' },
		success: function(data){
					if(data.error){
						alert(data.error);
					}else if(data.totals){
						$(el).replaceWith('<span class="warn">Some totals do not match'+data.totals+'</span>');
						next_step = $(el).attr('step');
						$('#step_'+next_step).show();
					}else{
						$(el).replaceWith('<span class="success">'+data+' registers seem ok</span>');
						$('#step_'+next_step).show();
					}
		},
		error: function() {
			$(el).replaceWith("<?php
				$link = Yii::app()->getBaseUrl(true).'/csv/checkCSVTotals?csv_file='.$model->csv;
				echo	'<span class=\"error\"><br />error on checkTotals. For more detail see<br />'.
						'<a href=\"'.$link.'\">'.$link.'</a>'.
						'</span>';
			?>");
		},
	});
}
function dumpBudgets(el,next_step){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/budget/dumpBudgets',
		type: 'GET',
		dataType: 'json',
		//beforeSend: function(){  },
		//complete: function(){  },
		success: function(data){
					if(data != 0){
						$(el).replaceWith('<span class="warn">exec(mysqldump) returned:'+data+' Failed to backup Budgets.</span>');
					}else{
						$(el).replaceWith('<span class="success">All budgets backed up ok.</span>');
					}
					$('#step_'+next_step).show();
		},
		error: function() {
			$(el).replaceWith("<?php
				$link = Yii::app()->getBaseUrl(true).'/budget/dumpBudgets';
				echo	'<span class=\"error\"><br />error on dump Budgets. For more detail see<br />'.
						'<a href=\"'.$link.'\">'.$link.'</a>'.
						'</span>';
			?>");
		},
	});
}
function importData(el,next_step){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/csv/importCSVData/<?php echo $model->year;?>',
		type: 'POST',
		data: { 'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>',
				'csv_file': '<?php echo $model->csv;?>'
		},
		dataType: 'json',
		beforeSend: function(){ $("#import_button").attr("disabled", "disabled"); $('#loading_importing_csv').show(); },
		complete: function(){ $('#loading_importing_csv').hide(); },
		success: function(data){
					if(data.error)
						$(el).replaceWith('<span class="error">'+data.error+'</span>');
					else{
						msg = '<span class="success">New registers: '+data.new_budgets+', Updated registers: '+data.updated_budgets+'</span>';
						$(el).replaceWith(msg);
						$('#step_'+next_step).show();
					}
		},
		error: function() {
			$(el).replaceWith("<?php
				$link = Yii::app()->getBaseUrl(true).'/csv/importCSVData/'.$model->year.'?csv_file='.$model->csv;
				echo	'<span class=\"error\"><br />error on importData. For more detail see<br />'.
						'<a href=\"'.$link.'\">'.$link.'</a>'.
						'</span>';
			?>");
		},
	});
}
</script>

<h1 style="margin: 0 0 15px 0"><?php echo __('Import csv into').' '.$model->year;?></h1>

<?php
if(!$model->csv){

echo "<script>$(document).ready(function() {\n";
echo "$('#upload-form').submit(function () {\n";
echo "	return true;";
echo "});\n";
echo "});</script>\n";

echo '<p>Step 1. Upload .csv file</p>';
$form = $this->beginWidget(
    'CActiveForm',
    array(
		'id' => 'upload-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => array('enctype' => 'multipart/form-data'),
		'action' => Yii::app()->request->baseUrl.'/csv/uploadCSV/'.$model->year,
    ));
	echo '<p>';
	echo '<div class="row">';
		echo $form->hiddenField($model, 'year');
		echo $form->labelEx($model, 'csv');
		echo $form->fileField($model, 'csv');
		echo $form->error($model, 'csv');
	echo '</div>';
	echo '</p>';
	echo '<p>'.CHtml::submitButton('Upload').'</p>';
$this->endWidget();

}else{
	echo '<p>Step 1. <span class="success">File uploaded correctly</span></p>';
}

if($model->step == 2){
	echo '<p id="step_2">Step 2. Check CSV format ';
	echo '<input id="check_csv_button" type="button" value="Check" onClick="js:checkFormat(this,3);" /></p>';
}

echo '<p id="step_3" style="display:none">Step 3. Check for missing values ';
echo '<input id="check_missing_values_button" type="button" value="Check" onClick="js:addMissingValues(this,4);" />';
echo '<img id="checking_missing_values" style="display:none" src="'.Yii::app()->request->baseUrl.'/images/loading.gif" />';
echo '</p>';

echo '<p id="step_4" style="display:none">Step 4. Check totals  ';
echo '<input type="button" step="41" value="Calculate" onClick="js:checkTotals(this,5);" />';
echo '</p>';

echo '<p id="step_41" style="display:none">';
echo '<input type="button" value="Try again" onClick="js:location.href=\''.Yii::app()->request->baseUrl.'/csv/importCSV/'.$model->year.'\';" /> ';
echo '<input type="button" value="Continue anyway" onClick="js:step41_to_5();" /></p>';

echo '<p id="step_5" style="display:none">Step 5. Backup budget database: ';
echo '<input id="dump_button" type="button" style="margin-left:15px;" value="Backup" onClick="js:dumpBudgets(this,6);" /></p>';

echo '<p id="step_6" style="display:none">Step 6. Import into database: <b>'.$model->year.'</b> ';
echo '<input id="import_button" type="button" style="margin-left:15px;" value="Import" onClick="js:importData(this,7);" />';
echo '<img id="loading_importing_csv" style="display:none" src="'.Yii::app()->request->baseUrl.'/images/loading.gif" />';
echo '</p>';

$criteria=new CDbCriteria;
$criteria->condition='parent IS NULL AND year = :year';
$criteria->params[':year'] = $model->year;
$year=Budget::model()->find($criteria);
if ($year===null){
	throw new CHttpException(404,'The requested Budget year does not exist.');
}
echo '<p id="step_7" style="display:none">';
if($year->code == 1) // this year has already been published
	echo 'Remember to <b>update the zip file</b> with this csv.<br />';
echo 'Return to year '.CHtml::link($model->year, array('budget/updateYear', 'id'=>$year->id)).'</p>';

?>
