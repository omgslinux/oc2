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

$totalBudgets = $model->getYearsBudgetCount();
$featuredCount = count($model->getFeatured());

$this->menu=array(
	array('label'=>__('Import budgets'), 'url'=>array('csv/importCSV/'.$model->year)),
	array('label'=>__('Manage years'), 'url'=>array('admin')),
);

if($totalBudgets){
	$delTree = array( array('label'=>__('Selected budget delete'), 'url'=>array('budget/deleteTree', 'id'=>$model->year)));
	array_splice( $this->menu, 1, 0, $delTree );
	
	if($model->getYearsTotalEnquiries() == 0){
		$deleteDatos = array( array('label'=>__("Delete this year's budgets"), 'url'=>'#',
									'linkOptions'=>array('submit'=>array('budget/deleteYearsBudgets','id'=>$model->id), 'csrf'=>true, 'confirm'=>'Are you sure you want to delete '.$totalBudgets.' budgets?')));
		array_splice( $this->menu, 1, 0, $deleteDatos );
	}


	$deleteYear= array(	array(	'label'=>__('Delete year'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('delete','id'=>$model->id), 'csrf'=>true, 'confirm'=>'Are you sure you want to delete this item?')));



	$label = __('Define graphics');
	if($totalBudgets > 0 && $featuredCount == 0)
		$label = $label.'<i class="icon-attention green"></i>';
	$featured = array( array('label'=>$label, 'url'=>array('budget/featured', 'id'=>$model->year)));
	array_splice( $this->menu, 1, 0, $featured );

	$downloadCsv = array( array('label'=>__('Export budgets'), 'url'=>array('csv/export', 'id'=>$model->year)));
	array_splice( $this->menu, 1, 0, $downloadCsv );
}elseif($model->year != Config::model()->findByPk('year')->value){
	$deleteYear= array(	array(	'label'=>__('Delete year'), 'url'=>'#',
								'linkOptions'=>array('submit'=>array('delete','id'=>$model->id), 'csrf'=>true, 'confirm'=>'Are you sure you want to delete this item?')));
	array_splice( $this->menu, 1, 0, $deleteYear );
}
$this->inlineHelp=':manual:budget:updateyear';
$this->viewLog="Budget";
?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<script>
function showEnquiry(enquiry_id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/getMegaDelete/'+enquiry_id,
		type: 'GET',
		success: function(data){
			if(data != 0){
				$("#mega_delete_content").html(data);
				$('#mega_delete_button').attr('enquiry_id', enquiry_id);
				$('#mega_delete').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, speed: 10
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
                });
			}
		},
		error: function() {
			alert("Error on show mega delete");
		}
	});
}
function megaDelete(el){
	enquiry_id = $(el).attr('enquiry_id');
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/megaDelete/'+enquiry_id,
		type: 'POST',
		//beforeSend: function(){ $('#right_loading_gif').show(); },
		//complete: function(){ $('#right_loading_gif').hide(); },
		success: function(data){
			$('#mega_delete').bPopup().close();
			if(data != 0){
				window.location = '<?php echo Yii::app()->request->baseUrl; ?>/budget/updateYear/<?php echo $model->id;?>';
				//$('#enquirys-grid').yiiGridView('update'); // get this working again (jquery overwritten)
			}
		},
		error: function() {
			alert("Error on megaDelete");
		}
	});
}

</script>

<?php $title=__('Edit year').' '.$model->year;?>

<?php 
echo $this->renderPartial('_formYear',
							array(	'model'=>$model,
									'title'=>$title,
									'totalBudgets'=>$totalBudgets,
									'featuredCount'=>$featuredCount,
								));

if($enquirys->getData()){
echo '<div class="horizontalRule" style="margin-top:20px"></div>';
echo '<div style="font-size:1.5em">'.__('Budgetary enquiries for').' '.$model->year.'</div>';

$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'enquirys-grid',
	'dataProvider'=>$enquirys,
	//'filter'=>$model,
	'ajaxUpdate'=>true,
	'columns'=>array(
		array(
			'name'=>__('Enquiry'),
			'value'=>'$data->title',
		),
		array(
			'name'=>'state',
			'type'=>'raw',
            'value'=>function($data,$row){
				$value = Enquiry::getHumanStates($data->state,$data->addressed_to);
				return $value;
				},
		),
		array(
			'name'=>'internal code',
			'value'=>'$data->budget0->csv_id',
		),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{change} {megaDelete}',
			'buttons'=>array(
				'change' => array(
					'label'=>'<i class="icon-wrench-circled green"></i>',
		            'url'=>'Yii::app()->createUrl("enquiry/changeBudget", array("id"=>$data->id))',
				),
				'megaDelete' => array(
					// http://www.doprogramsdream.nl/blog/blogPost/view/id/16
					'label'=>'<i class="icon-cancel-circled red"></i>',
					'url'=>'$data->id',
					'click'=>'js:function() { showEnquiry($(this).attr("href"));return false; }',
				),
			),
		),
	)
));


}
?>
<div id="mega_delete" class="modal" style="width:850px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<div id="mega_delete_content"></div>
</div>

<?php if(Yii::app()->user->hasFlash('csv_generated')):?>
    <div class="flash-success" id="csv_generated_ok">
		<?php echo Yii::app()->user->getFlash('csv_generated');?>
    </div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>


