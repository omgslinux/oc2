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

/* @var $this FileController */
/* @var $model File */
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;


echo '<div class="modalTitle">'.__('Uploaded files').'</div>';

$dataProvider = new CActiveDataProvider('File', array(
    'criteria'=>array('condition'=>'model = "SitePage"')
));

echo '<div style="margin: 10px -10px 0 -10px">';
$this->widget('zii.widgets.grid.CGridView', array(
	'htmlOptions'=>array('class'=>'pgrid-view'),
	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
	'id'=>'file-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'header'=>'link URL',
			'name'=>'path',
			'type'=>'raw',
			'value'=>'$data->getWebPath()',
		),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{delete}',
		),
	),
));
echo '</div>';
?>
