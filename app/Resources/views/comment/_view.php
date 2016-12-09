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

/* @var $this CommentController */
/* @var $data Comment */
?>

<div class="comment" id="comment_<?php echo $data->id;?>">
	<p style="margin-bottom:5px;margin-top:-10px">
	<?php
	if($data->thread_position){
		echo '<span id="comment_'.$data->model.$data->model_id.'_'.$data->thread_position.'">#'.$data->thread_position.'</span> ';
	}
	?>
	<?php if(! ($data->user0->username == Yii::app()->user->id || $data->user0->is_disabled == 1))
			echo '<span class="link" onClick="js:getContactForm('.$data->user.');return false;">';
		else
			echo '<span>';
	?>
	<?php echo CHtml::encode($data->user0->fullname); ?></span>

	<?php 
		echo __('comments on the').' '.format_date($data->created,1);
		if($data->user == Yii::app()->user->getUserID() || $data->isModerator(Yii::app()->user->getUserID()))
			echo '<i class="icon-cancel-circle red" style="cursor:pointer;" onClick="js:deleteComment('.$data->id.')"></i>';
	?>
	</p>
	<p style="margin-bottom:-5px;">
	<?php
		echo $data->body;
	?>
	</p>
</div>
