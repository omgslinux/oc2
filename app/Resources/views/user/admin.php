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

/* @var $this UserController */
/* @var $model User */

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
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
<div style="position:relative; right:40px" >
	<div class="teamMenu" onCLick="js:viewLog('User');return false;">
		<i class="icon-book"></i>
	</div>
</div>
<div style="position:relative; right:80px" >
	<div class="teamMenu" onCLick="js:showHelp('<?php echo getInlineHelpURL(":manual:user:admin");?>');return false;">
		<i class="icon-help-circled"></i>
	</div>
</div>
<div style="position:relative; right:120px">
	<div id="searchOptionsToggle" class="color" onCLick="js:toggleSearchOptions();return false;">
		<i class="icon-search-circled"></i>
	</div>
</div>
<?php $this->widget('InlineHelp'); ?>
<?php $this->widget('ViewLog'); ?>

<h1><?php echo __('Users and roles');?></h1>

<div id="searchOptions" class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'username',
		//'fullname',
		'email',
		//'is_socio',
		'is_team_member',
		'is_editor',
		'is_manager',
		'is_admin',
		/*
		'joined',
		'activationcode',
		'activationstatus',
		*/
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view} {update}',
			'buttons'=>array(
				'update' => array(
					'label'=>'Update',
		            'url'=>'Yii::app()->createUrl("user/updateRoles", array("id"=>$data->id))',
				),
			),
		),
	)
));?>


<?php if(Yii::app()->user->hasFlash('success')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-success').slideUp('fast');
    	}, 5000);
		});
	</script>
    <div class="flash-success">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>



