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

/* @var $this LogController */
/* @var $model Log */

?>
<div style="position:relative;" >
	<div class="teamMenu" onCLick="js:window.location.href = '<?php echo $this->createUrl('/user/panel');?>';">
		<i class="icon-home"></i>
	</div>
</div>
<div style="position:relative; right:40px" >
	<div class="teamMenu" onCLick="js:showHelp('<?php echo getInlineHelpURL(":manual:log");?>');return false;">
		<i class="icon-help-circled"></i>
	</div>
</div>
<?php $this->widget('InlineHelp'); ?>

<h1><?php echo __('Browse logs');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view pgrid-cursor-pointer'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'loadingCssClass'=>'pgrid-view-loading',
	'id'=>'log-grid1',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'created',
		array(
			'name'=>'user',
			'type'=>'raw',
            'value'=>function($data,$row){
				if($user = User::model()->findByPk($data->user))
					return $user->username;
				return 'system';
			},
		),
		'prefix',
		'message',
	),
)); ?>
